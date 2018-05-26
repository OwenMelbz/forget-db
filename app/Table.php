<?php

namespace App;

use App\Commands\ForgetMeNow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use LaravelZero\Framework\Commands\Command;

class Table
{
    protected $name;

    protected $primaryKey;

    protected $conditions;

    protected $columns;

    protected $messenger;

    public function __construct(string $name, string $primaryKey, Collection $conditions, Collection $columns)
    {
        $this->name = $name;
        $this->primaryKey = $primaryKey;
        $this->conditions = $conditions;
        $this->columns = $columns;
    }

    public function forget(Command $messenger)
    {
        $this->messenger = $messenger;

        $this->writeRowsToDatabase(
            $this->cleanseRows(
                $this->getRows()
            )
        );
    }

    private function writeRowsToDatabase(Collection $rows)
    {
        $total = $rows->count();

        foreach ($rows as $i => $row) {
            DB::transaction(function () use ($row, $total, $i) {
                DB::table($this->name)
                    ->where($this->primaryKey, $row->pk)
                    ->update($row->updates);

                $this->messenger->message(
                    sprintf('%d/%d %s written to database.', ($i + 1), $total, str_plural('row', $total))
                );
            });
        }
    }

    private function cleanseRows(Collection $rows)
    {
        $total = $rows->count();
        $cleansedRows = collect();

        foreach ($rows as $i => $row) {
            foreach ($this->columns as $column) {
                if (!isset($row->{$column->getName()})) {
                    throw new \Exception('We cannot find `'. $column->getName() .'` in the `' . $this->name . '` table.');
                }

                $row->{$column->getName()} = $column->generate();
            }

            $pk = $row->{$this->primaryKey};
            $updates = (array) $row;
            unset($updates[$this->primaryKey]);

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

    private function getColumnNames()
    {
        $names = collect();

        foreach ($this->columns as $column) {
            $names->push(
                $column->getName()
            );
        }

        return $names;
    }

    private function getRows()
    {
        $query = DB::table($this->name);

        $columnsToSelect = $this->getColumnNames();
        $columnsToSelect->prepend($this->primaryKey);

        $query->select(
            $columnsToSelect->toArray()
        );

        foreach ($this->conditions as $condition) {
            if (str_contains($condition->getRaw(), 'or')) {
                $query->orWhereRaw(
                    $condition->getWhere()
                );
            } else {
                $query->whereRaw(
                    $condition->getWhere()
                );
            }
        }

        dd($query->toSql());

        $results = $query->get();

        $this->messenger->message(
            sprintf('%d %s found to process.', $results->count(), str_plural('row', $results->count()))
        );

        return $results;
    }

}