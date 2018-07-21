<?php

namespace ChrisHalbert\LaravelNomadic\Hooks;

use ChrisHalbert\LaravelNomadic\NomadicMigration;

/**
 * Interface NomadicBaseHookInterface
 * @package ChrisHalbert\LaravelNomadic\Hooks
 */
interface NomadicMigrationHookInterface extends NomadicBaseHookInterface
{
    /**
     * Executes a function with parameters the create receives.
     * @param NomadicMigration $migration A migration.
     * @return string
     */
    public function execute(NomadicMigration $migration = null);
}
