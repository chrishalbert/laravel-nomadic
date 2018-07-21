<?php

namespace ChrisHalbert\LaravelNomadic\Hooks;

use ChrisHalbert\LaravelNomadic\NomadicMigration;
use Carbon\Carbon;

/**
 * Class PrintRunning
 * @package ChrisHalbert\LaravelNomadic\Hooks
 */
class PrintRunning implements NomadicMigrationHookInterface
{
    /**
     * Prints the migration running.
     * @param NomadicMigration $migration The migration.
     * @return void
     */
    public function execute(NomadicMigration $migration = null)
    {
        $migrationFileName = $migration ? $migration->getFileName() : 'Migration';
        echo $migrationFileName . ' started at ' . Carbon::now()->toDateTimeString() . PHP_EOL;
    }
}
