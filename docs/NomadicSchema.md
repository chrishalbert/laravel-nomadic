# Nomadic Schema 
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
   
## Setup
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