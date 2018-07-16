<?php

return [

    // These are the additional columns in the migrations table that migrations can append data to.
    'schema' => [
    ],

    // These are traits that you can have appended to your migrations automatically
    'traits' => [
    ],

    // Variety of hooks
    // preCreate, postCreate: Executed with `php artisan make:migration`, must be defined here
    // other hooks: Executed with the migrations
    'hooks' => [
        'preCreate' => [
            // Runs before a migration is created
        ],
        'postCreate' => [
            // Runs after a migration is created
        ],
        'construct' => [
            // Runs after the parent constructor
        ],
        'preMigrate' => [
            // Runs before the migration code
        ],
        'postMigrate' => [
            // Runs after the migration code
        ],
        'preRollback' => [
            // Runs before the rollback
        ],
        'postRollback' => [
            // Runs after the rollback
        ],
        'destruct' => [
            // Runs before the parent destructor
        ]
    ],
];
