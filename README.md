# laravel-nomadic
[![Build Status](https://travis-ci.org/chrishalbert/laravel-nomadic.svg?branch=master)](https://travis-ci.org/chrishalbert/laravel-nomadic)
[![Coverage Status](https://coveralls.io/repos/github/chrishalbert/laravel-nomadic/badge.svg?branch=master)](https://coveralls.io/github/chrishalbert/laravel-nomadic?branch=master)
[![Latest Stable Version](https://poser.pugx.org/chrishalbert/laravel-nomadic/v/stable)](https://packagist.org/packages/chrishalbert/laravel-nomadic)
[![Total Downloads](https://poser.pugx.org/chrishalbert/laravel-nomadic/downloads)](https://packagist.org/packages/chrishalbert/laravel-nomadic)
[![License](https://poser.pugx.org/chrishalbert/laravel-nomadic/license)](https://packagist.org/packages/chrishalbert/laravel-nomadic)


A configuration based tool kit of enhancements to Laravel's migrations. Exposes functionality so that developers can customize how migrations are generated. 

## Features
* **Nomadic Schema** - Associate data with each migration. Maybe you want to save the date and time the migration was run, 
how long it ran for, or specific data with regards to nature of the migration itself.
* **Nomadic Traits** - Inject your own custom traits into your migrations so that you can reuse common code.
* **Nomadic Hooks** - Apply hooks before or after a migration is generated.
* More to come...

## Installation - 

1. Installation:

```
composer require chrishalbert/laravel-nomadic
```

or manually add it to the require-dev section of your composer file.

```json
{
    "require"   : {
        "chrishalbert/laravel-nomadic": "1.*"
    }
}
```

2. Next, add the Service Provider to the config/app.php
```php
    'providers' => [
    
        /**
         * Custom Providers...
         */
        ChrisHalbert\LaravelNomadic\NomadicServiceProvider::class,        
    ]
```

3. Lastly, publish some default configs into your application.
```
php artisan vendor:publish // Installs the nomadic.php config
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
   
### Setup
1. First, you will need to add the field(s) to your migration table - this is on you to do :)
2. Open up the nomadic.php and add the fields in the schema array
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

Now, verify the configurations are complete.
```
php artisan make:migration VerifyNomadicInstalled
```

In your migration, you should now see that your migration `extends NomadicMigration`.

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

## Nomadic Traits 
* Use Case: Reuse common functionality that your migrations tend to use.
   
### Usage 
Open up the nomadic.php and add your custom traits
```
return [
    'traits' => [
        \My\Custom\Trait::class
    ],
];
```
Now when you create migrations, you should see your stubbed migration using your custom traits.

## Nomadic Hooks
* Use Case: You would like to alert the developer to run all migrations before creating a new one.
* Use Case: After a migration is generated, you want to remind the user to add schema changes to the release notes.

### Usage 
Open up the nomadic.php where you can add 2 different types of hooks. 

#### Create Hooks
These are hooks used when you run `php artisan make:migration`. These hooks can ONLY be defined here in the config file. You can pass a closure, or you can pass a NomadicHookInterface. The benefit of passing the NomadicCreateHookInterface, is that you get the same data passed to the create(). These arguments are
passed to the execute() method, which is what is called. 
```
return [
    'hooks' => [
        'preCreate' => [
            function() {
                \Log::info('Make sure to run all migrations');
            },
        ],
        'postCreate' => [
            new NomadicCreateHookInterfaceImplementation()
        ]
    ],
];
```

#### Migration Hooks
These hooks are excuted when the migration runs. The construct hook can only be set in the config file. Everything else can be set here in the configuration
or modified at runtime.
```
return [

    // Hooks executed with the migrations
    'hooks' => [
        'construct' => [            	// Can only be defined in the configs
        ],
        'preMigrate' => [ 				// Executed before up()
        ],
        'postMigrate' => [				// Executed after up()
        ],
        'preRollback' => [				// Executed before rolling down()
        ],
        'postRollback' => [				// Executed after rolling down()
        ],
        'destruct' => [					// Executed during destruction
        ]
    ],
];
```


## Feature Requests/Bugs
   Submit feature requests or bugs to [laravel-nomadic Issues](https://github.com/chrishalbert/laravel-nomadic/issues).
