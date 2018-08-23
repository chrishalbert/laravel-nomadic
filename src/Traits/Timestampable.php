<?php

namespace ChrisHalbert\LaravelNomadic\Traits;

use Carbon\Carbon;
use ChrisHalbert\LaravelNomadic\NomadicMigration;

trait Timestampable
{
    /**
     * Initializes printable.
     * @return void
     */
    public function initPrintable()
    {
        $self = $this;
        $this->addHook(NomadicMigration::PRE_MIGRATE, function () use ($self) {
            $self->set('started_at', Carbon::now());
        });
        $this->addHook(NomadicMigration::POST_MIGRATE, function () use ($self) {
            $self->set('finished_at', Carbon::now());
        });
    }
}
