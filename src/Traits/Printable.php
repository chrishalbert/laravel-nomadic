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
        $this->addHook(NomadicMigration::PRE_MIGRATE, new PrintRunning());
        $this->addHook(NomadicMigration::POST_MIGRATE, new PrintRan());
        $this->addHook(NomadicMigration::PRE_ROLLBACK, new PrintRunning());
        $this->addHook(NomadicMigration::POST_ROLLBACK, new PrintRan());
    }
}
