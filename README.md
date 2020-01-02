# 🧠 forget-db

[![Build Status](https://travis-ci.org/OwenMelbz/forget-db.svg?branch=master)](https://travis-ci.org/OwenMelbz/forget-db)

A simple(ish) command line tool written in PHP 7.4 using Laravel Zero and Faker to help you anonymise/pseudonymise data within your database to support protecting either sensitive information, or peoples right to be forgotten with GDPR compliance.

The tool allows you to connect to either mysql, postgres, sqlite or sqlserver and replace defined information with random data to allow you to keep statistics/relationships/audit of actions etc.

It uses a simple yaml configuration file to define the conditions for overwriting, which fields you want to overwrite, and what to overwrite them with.

# Installation

I would recommend installing this globally on your system with

```sh
curl -L https://github.com/OwenMelbz/forget-db/raw/master/builds/application --output forget-db
chmod +x forget-db
mv ./forget-db /usr/local/bin/forget-db
forget-db update # optional - but will make sure your binary is up to date
```

# Configuration

To generate a new config just run (from anywhere) `forget-db new`

This will walk you through generating an example config file.

This will generate a basic config file that looks something like

```yml
table_one:
  key: id
  conditions:
    - where user_id != 1
    - or cake LIKE "%test%"
  columns:
    firstname: firstname
    lastname: lastname

table_two:
  key: id
  columns:
    firstname: firstname
    lastname: lastname

table_three:
  key: id
  conditions: table_two.id = 1
  joins: table_two on table_two.id = table_three.table_two_id
  columns:
    firstname: firstname
    lastname: lastname
```

## Properties

Each top level item in the config file is a database table e.g `table_one`

The first **required** property is `key` this is very important as it defines which column is used for the update query and most likely needs to be unique.

The second **required** property is `columns` this is simply an array of column names, with their values set to Faker methods, you can get a full list of generators [Faker Generators here](https://github.com/fzaninotto/Faker) - most are easy to remember such as `name, email, company` etc

The first **optional** property is `conditions` this should be an array of sql clauses if you need to restrict usage, e.g if you only need user_id 1 to be forgotten, then you can add `where user_id = 1` you should be able to have multiple conditions, these get passed into Laravels' `whereRaw` method, so check out those documents if you need more advance queries.

The second **optional** property is `joins` this can be a string or array of joins which will be used to accompany the conditions, it takes a simple join query in the following format `joined_table on joined_table.column = other_table.column`, you can use the modifier system to prefix a table with a join type, e.g `left:tablename on...` this gets passed to Laravel's `leftJoin()`, `rightJoin()` and `join()` functions.

## Modifiers

Recently we introduced the ability to add modifiers to your column definitions, currently there is only one. To use a modifer just prefix the faker method with `modifer:` to create something like `modifer:email_address`

 Name        | Description           |
|:------------- |:-------------|
| unique:      | Will utilise Fakers unique() method to generate unique data for a column |
| left/right:  | Allows you to define the type of join to execute |


# Usage

Once you have your config complete you can run `forget-db forget ./path/to/config.yml` the wizard will ask for your connection details, and then will start the cleanse.

If forget-db finds a .env file within your current working directory, it will try to populate the default options with what is within it. Typically Laravel style connections work out of the box!

After its completed you should get a notification to let you know!

## Dry-run

We do not have a full dry-run system, however you can preview the query that selects your data set, and a table of the data that it has found, simply specify the `--dry` arg after your command e.g `forget-db forget ./config --dry` - this will only run "selects" on your database and will not write any changes! You will see something similar to:

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

What it doesn't do is:

- Give you what the data will look like AFTER you run it
- Give you the update commands it will run

> Warning - When doing a dry run, remember that it will output to your terminal, so if you are exposing sensitive data make sure you're taking the correct precautions!

# Warnings / Notes
- Due to syntax and Laravel requirements this must be run via a php 7.1 binary
- There is no full dry run, I recommend you test this on a temporary database first or test your conditions using the --dry arg
- Not all faker field types are yet supported, e.g `date($format = 'Y-m-d', $max = 'now')`
- The system that is running the tool, must have a connection to the database server.
- Due to trying to keep optimial server compatibility, updates are not done in bulk, but are done one at a time, so make sure you're aware of any row/table locking on your server.
- This was all written late at night, so please bare with me :) 
