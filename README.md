# 🧠 forget-db

A simple(ish) command line tool written in PHP 7.1 using Laravel Zero and Faker to help you anonymise/pseudonymise data within your database to support protecting either sensitive information, or peoples right to be forgotten with GDPR compliance.

The tool allows you to connect to either mysql, postgres, sqlite or sqlserver and replace defined information with random data to allow you to keep statistics/relationships/audit of actions etc.

It uses a simple yaml configuration file to define the conditions for overwriting, which fields you want to overwrite, and what to overwrite them with.

# Installation

I would recommend installing this globally on your system with

```sh
curl -L https://github.com/kissg1988/forget-db/raw/master/builds/application --output forget-db
chmod +x forget-db
mv ./forget-db /usr/local/bin/forget-db
forget-db update # optional - but will make sure your binary is up to date
```

# Configuration

To generate a new config just run (from anywhere) `forget-db new`

This will walk you through generating an example config file.

This will generate a basic config file that looks something like:

```yml
table_one:
    key: user_id
    conditions: ['where user_id != 1', 'or user_email LIKE "%@%"']
    columns: { user_name: name, user_email: email }
table_two:
    key: user_id
    columns: { user_name: name, user_email: email }
table_three:
    key: user_id
    conditions: 'table_two.user_id = 1'
    joins: 'table_two on table_two.user_id = table_three.table_two_id'
    columns: { user_name: name, user_email: email }
```

## Properties

Each top level item in the config file is a database table e.g `table_one`

The first **required** property is `key` this is very important as it defines which column is used for the update query and most likely needs to be unique.

The second **required** property is `columns` this is simply an array of column names, with their values set to Faker methods, you can get a full list of generators [Faker Generators here](https://github.com/fzaninotto/Faker) - most are easy to remember such as `name, email, company` etc. You can also use parametrized formatters like `regexify`.

The first **optional** property is `conditions` this should be an array of sql clauses if you need to restrict usage, e.g if you only need user_id 1 to be forgotten, then you can add `where user_id = 1`. You should be able to have multiple conditions, these get passed into Laravels' `whereRaw` method, so check out those documents if you need more advance queries.

The second **optional** property is `joins` this can be a string or array of joins which will be used to accompany the conditions, it takes a simple join query in the following format `joined_table on joined_table.column = other_table.column`, you can use the modifier system to prefix a table with a join type, e.g `left:tablename on...` this gets passed to Laravel's `leftJoin()`, `rightJoin()` and `join()` functions.

## Modifiers

Recently we introduced the ability to add modifiers to your column definitions, currently there is only one. To use a modifer just prefix the faker method with `modifer:` to create something like `modifer:email_address`

 Name        | Description           |
|:------------- |:-------------|
| unique:      | Will utilise Fakers unique() method to generate unique data for a column |
| left/right:  | Allows you to define the type of join to execute |


# Usage

Once you have your config complete you can run `forget-db forget ./path/to/config.yml`. The wizard will ask for your connection details, and then will start the cleanse. If you specify database connection parameters in file named `.env` placed in the working directory, those values are automatically used by the code. See `.env.example` to learn more about the settings that can be configured this way. Note that in interactive mode, the environment being used at runtime (as specified using the `--env` switch) affects the defaults. In non-interactive mode, all yes/no questions default to yes, regardless of the environment setting. To run in non-interactive mode, use the `-n` switch (useful for scripted runs).

After the command is completed you'll get some output about the success of the operation (with a detailed error message as appropriate). The code returns `0` exit code on success and `1` on failure.

## Dry-run

We do not have a full dry-run system, however you can preview the query that selects your data set, and a table of the data that it has found, simply specify the `--dry` arg after your command e.g `forget-db forget ./config --dry`. This will only run "selects" on your database and will not write any changes! You will see something similar to:

```
🧠  forget-db :: 2 rows found to process.
🧠  forget-db :: Query run... select `users`.`id`, `users`.`email`, `users`.`password` from `users`

+----+-------------------------+------------------+
| id | email                   | password         |
+----+-------------------------+------------------+
| 1  | arvel.bradtke@auer.com  | 371817583255573  |
| 2  | remington54@volkman.org | 6011543368953199 |
+----+-------------------------+------------------+
```

Limitations for dry-runs:

- It won't give you what the data will look like AFTER you run `forget`
- It won't give you the update commands it will run

> Warning - When doing a dry run, remember that it will output to your terminal, so if you are exposing sensitive data make sure you're taking the correct precautions!

# Warnings / Notes
- Due to syntax and Laravel requirements running the code requires PHP 7.1+
- There is no full dry-run support, I recommend to test this on a temporary database first or test your conditions using the `--dry` arg
- The system that is running the tool must have a connection to the database server (firewall rules and ACLs might need to be set accordingly).
- Due to trying to keep optimial server compatibility, updates are not done in bulk, but are done one at a time, so make sure you're aware of any row/table locking on your server.
- For large datasets, dry-running might crash depending on resource limits set in your local php environment. In interactive mode, the code asks whether you want to go on or not if more than 50 records are to be fetched.
