db-drop-tables
==============

[![Build Status](https://travis-ci.org/mikedfunk/db-drop-tables.svg?branch=develop)](https://travis-ci.org/mikedfunk/db-drop-tables)

In development, schema can be constantly changing. You don't want to have to
add a migration file for every single column modification/addition/removal, do
you? So just run this, then artisan migrate, then artisan db:seed. Or wrap those
up in a phing command (or another artisan command) and do them in one shot.

## Installation

1. Install via [composer](http://getcomposer.org): `composer require --dev mike-funk/db-drop-tables:dev-master`
2. Add the service provider to your `app/config/app.php` in the `providers` area: `'MikeFunk\DbDropTables\DbDropTablesServiceProvider',`
3. This will *not* work until you add a connection for the `mysql_information_schema` database in `app/config/database.php`:

```php
'mysql_information_schema' => array(
    'driver'    => 'mysql',
    'host'      => 'my_host_name'
    'database'  => 'INFORMATION_SCHEMA',
    'username'  => 'my_db_username'
    'password'  => 'my_db_password'
),
```

## Usage

Call the command from within laravel with `php artisan db:drop-tables`. It will confirm that you want to drop all of your tables in your MySQL database.
