<?php

namespace ChrisHalbert\LaravelNomadic\Traits;

use ChrisHalbert\LaravelNomadic\Hooks\PrintRan;
use ChrisHalbert\LaravelNomadic\Hooks\PrintRunning;
use ChrisHalbert\LaravelNomadic\NomadicMigration;

trait Printable
{
    /**
     * Initializes printable.
     * @return void
     */
    public function initPrintable()
    {
        parent::addHook(NomadicMigration::PRE_MIGRATE, new PrintRunning());
        parent::addHook(NomadicMigration::POST_MIGRATE, new PrintRan());
        parent::addHook(NomadicMigration::PRE_ROLLBACK, new PrintRunning());
        parent::addHook(NomadicMigration::POST_MIGRATE, new PrintRan());
    }
}
