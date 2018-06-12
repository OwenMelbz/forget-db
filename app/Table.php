<?php

namespace App;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use LaravelZero\Framework\Commands\Command;

/**
 * Class Table
 * @package App
 */
class Table
{

    /**
     * The name of the database table.
     *
     * @var string
     */
    protected $name;

    /**
     * The primary key of the table to base
     * the update query from.
     *
     * @var string
     */
    protected $primaryKey;

    /**
     * A collection of App\Condition to
     * store the query constraints.
     *
     * @var Collection
     */
    protected $conditions;

    /**
     * A collection of App\Relationship to
     * relationships/joins.
     *
     * @var Collection
     */
    protected $relationships;

    /**
     * A collection of App\Column to
     * store the transformations.
     *
     * @var Collection
     */
    protected $columns;

    /**
     * The LaravelZero\Framework\Commands\Command which
     * acts as a message bus for output to the console.
     *
     * @var Command
     */
    protected $messenger;

    /**
     * Table constructor.
     *
     * @param string $name
     * @param string $primaryKey
     * @param Collection $conditions
     * @param Collection $relationships
     * @param Collection $columns
     */
    public function __construct(string $name, string $primaryKey, Collection $conditions, Collection $relationships, Collection $columns)
    {
        $this->name = $name;
        $this->primaryKey = $primaryKey;
        $this->conditions = $conditions;
        $this->relationships = $relationships;
        $this->columns = $columns;
    }

    /**
     * Allows us to set the messenger.
     *
     * @param Command $messenger
     */
    public function setMessenger(Command $messenger): void
    {
        $this->messenger = $messenger;
    }

    /**
     * This is the main function which processes everything.
     *
     * @param Command $messenger
     * @throws Exception
     */
    public function forget(Command $messenger): void
    {
        $this->setMessenger($messenger);

        $this->writeRowsToDatabase(
            $this->cleanseRows(
                $this->getRows()
            )
        );
    }

    /**
     * Here we take a Collection of transformed rows
     * to insert back into the database based
     * off of the primary key.
     *
     * @param Collection $rows
     */
    private function writeRowsToDatabase(Collection $rows): void
    {
        $total = $rows->count();

        foreach ($rows as $i => $row) {
            DB::transaction(function () use ($row, $total, $i) {
                DB::table($this->name)
                    ->where($this->getPrefixedPrimaryKey(), $row->pk)
                    ->update($row->updates);

                $this->messenger->message(
                    sprintf('%d/%d %s written to database.', ($i + 1), $total, str_plural('row', $total))
                );
            });
        }
    }

    /**
     * Here we take the results of the database query
     * and transform each of the columns into
     * a faked version ready to update.
     *
     * @param Collection $rows
     * @return Collection
     * @throws Exception
     */
    private function cleanseRows(Collection $rows): Collection
    {
        $total = $rows->count();
        $cleansedRows = collect();

        foreach ($rows as $i => $row) {
            foreach ($this->columns as $column) {
                if (!property_exists($row, $column->getName())) {
                    throw new Exception('We cannot find `' . $column->getName() . '` in the `' . $this->name . '` table.');
                }

                $row->{$column->getName()} = $column->generate();
            }

            $pk = $row->{$this->primaryKey};
            $updates = (array) $row;
            unset($updates[$this->primaryKey]); // We don't want to update the primary key, so we remove it.

            $cleansedRows->push((object) [
                'pk' => $pk,
                'updates' => $updates,
            ]);

            $this->messenger->message(
                sprintf('%d/%d %s re-generated.', ($i + 1), $total, str_plural('row', $total))
            );
        }

        return $cleansedRows;
    }

    /**
     * Returns a prefixed name of the primary key
     * to allow joining of various ables which have
     * the name column names, e.g it converts id into users.id.
     *
     * @return string
     */
    private function getPrefixedPrimaryKey(): string
    {
        return $this->name . '.' . $this->primaryKey;
    }

    /**
     * A helper function to get an array
     * of the columns we want to transform.
     *
     * @return Collection
     */
    private function getColumnNames(): Collection
    {
        $names = collect();

        foreach ($this->columns as $column) {
            $names->push(
                $column->getName($this->name)
            );
        }

        return $names;
    }

    /**
     * Returns a Collection of items from the database query
     * based off the conditions. It will only return the
     * columns that have been defined in the config.
     *
     * @return Collection
     */
    public function getRows(): Collection
    {
        $query = DB::table($this->name);

        $columnsToSelect = $this->getColumnNames();
        $columnsToSelect->prepend($this->getPrefixedPrimaryKey());

        // To make life more manageable, we only select the columns that
        // we're actually going to transform.
        $query->select(
            $columnsToSelect->toArray()
        );

        foreach ($this->relationships as $relationship) {
            switch ($relationship->getType()) {
                case 'left':
                    $query->leftJoin(...$relationship->getJoin());
                    break;
                case 'right':
                    $query->rightJoin(...$relationship->getJoin());
                    break;
                default:
                    $query->join(...$relationship->getJoin());
                    break;
            }
        }

        foreach ($this->conditions as $condition) {
            if (str_contains(strtolower($condition->getRaw()), 'or')) {
                $query->orWhereRaw(
                    $condition->getWhere()
                );
            } else {
                $query->whereRaw(
                    $condition->getWhere()
                );
            }
        }

        $results = $query->get();

        $this->messenger->message(
            sprintf('%d %s found to process.', $results->count(), str_plural('row', $results->count()))
        );

        return $results;
    }

}