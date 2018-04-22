# laravel-nomadic
[![Build Status](https://travis-ci.org/chrishalbert/laravel-nomadic.svg?branch=master)](https://travis-ci.org/chrishalbert/laravel-nomadic)
[![Coverage Status](https://coveralls.io/repos/github/chrishalbert/laravel-nomadic/badge.svg?branch=master)](https://coveralls.io/github/chrishalbert/laravel-nomadic?branch=master)

A tool kit of enhancements to Laravel's migrations. 

## Features
* **Nomadic Schema** - Associate data with each migration. Maybe you want to save the date and time the migration was run, 
how long it ran for, or specific data with regards to nature of the migration itself.
* More to come...

## Toolkit Installation - 

Local/project installation:

```
composer require-dev chrishalbert/laravel-nomadic
```

or manually add it to the require-dev section of your composer file.

```json
{
    "require"   : {
        "chrishalbert/laravel-nomadic": "*"
    }
}
```

## Nomadic Schema 
* Use Case: A developer wants to track the migration's runtime.
* Use Case: A developer wants to know exactly when a migration started or ended.
* Use Case: A developer deletes records from a table. To rollback this migration, the down() function would include
hardcoded values. While this could work for some simple applications, any randomized or scrubbed database will not 
necessarily translate across tables.
* Use Case: A developer inserts new records into a table. To rollback this migration, the down() function would need
to query the exact records that were inserted. If the developer's database is a lean version of production, the query
may not be accurate. 
* Use Case: A developer updates records in table. Similarly, to rollback, the down() function would need to know the
exact values of the records prior to updating. This could differ for randomized data.
   
### Installing
1. First, you will need to add the field(s) to your migration table - this is on you to do :)
2. Next, you will integrate into your application.
```
php artisan vendor:publish // Installs the nomadic.php config
```

Open up the nomadic.php and add the fields in the schema array
```
return [
    // Just some examples below - these would be the additional columns in your migration table.
    'schema' => [
        'flag',           
        'author',
        'runTime',
        'migrationInfo'
    ],
];
```

Add the Service Provider to the config/app.php
```php
    'providers' => [
    
        /**
         * Custom Providers...
         */
        ChrisHalbert\LaravelNomadic\NomadicServiceProvider::class,        
    ]
```

Verify the configurations are complete.
```
php artisan make:migration VerifyNomadicInstalled
```

In your migration, you should now see that your migration `extends NomadicMigration`.

### Usage
As noted above in your new migration's comments:
```php
    /**
     * @return void
     */
    public function up()
    {
        // Use $this->setProperty($migrationColumn, $insertedIds);
        // The $migrationColumn MUST be added to the configs/nomadic.php
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // And now you can $insertedIds = $this->getProperty($migrationColumn) and delete
    }
```    
 
## Feature Requests/Bugs
   Submit feature requests or bugs to [laravel-nomadic Issues](https://github.com/chrishalbert/laravel-nomadic/issues).
   
   I know that there are some good ideas out there!
