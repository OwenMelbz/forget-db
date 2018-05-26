# 🧠 forget-db

A simple(ish) command line tool written in PHP using Laravel Zero and Faker to help you anonymise/pseudonymise data within your database to support protecting either sensitive information, or peoples right to be forgotten with GDPR compliance.

The tool allows you to connect to either mysql, postgres, sqlite or sqlserver and replace defined information with random data to allow you to keep statistics/relationships/audit of actions etc.

It uses a simple yaml configuration file to define the conditions for overwriting, which fields you want to overwrite, and what to overwrite them with.

# Installation

I would recommend installing this globally on your system with

```sh
curl -L https://github.com/OwenMelbz/forget-db/raw/master/builds/application --output forget-db
chmod +x forget-db
mv ./forget-db /usr/local/bin/forget-db
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
    - orWhere cake LIKE %test%
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
  conditions: where user_id = 1
  columns:
    firstname: firstname
    lastname: lastname
```

## Properties

Each top level item in the config file is a database table e.g `table_one`

The first **required** property is `key` this is very important as it defines which column is used for the update query and most likely needs to be unique.

The second property is `conditions` this should be an array of sql clauses if you need to restrict usage, e.g if you only need user_id 1 to be forgotten, then you can add `where user_id = 1` you should be able to have multiple conditions, these get passed into Laravels' `whereRaw` method, so check out those documents if you need more advance queries.

The last property is `columns` this is simply an array of column names, with their values set to Faker methods, you can get a full list of generators [Faker Generators here](https://github.com/fzaninotto/Faker) - most are easy to remember such as `name, email, company` etc

# Usage

Once you have your config complete you can run `forget-db ./path/to/config.yml` the wizard will ask for your connection details, and then will start the cleanse.

After its completed you should get a notification to let you know!

# Warnings / Notes
- There is no dry run, I recommend you test this on a temporary database first.
- Not all faker field types are yet supported, e.g `date($format = 'Y-m-d', $max = 'now')`
- The system that is running the tool, must have a connection to the database server.
- Due to trying to keep optimial server compatibility, updates are not done in bulk, but are done one at a time, so make sure you're aware of any row/table locking on your server.
- This was all written late at night, so please bare with me :) 