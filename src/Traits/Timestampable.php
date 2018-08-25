<?php

namespace ChrisHalbert\LaravelNomadic\Traits;

use Carbon\Carbon;
use ChrisHalbert\LaravelNomadic\NomadicMigration;

trait Timestampable
{
    /**
     * Initializes timestampable.
     * @return void
     */
    public function initTimestampable()
    {
        $self = $this;
        $this->addHook(NomadicMigration::PRE_MIGRATE, function () use ($self) {
            $self->setProperty('started_at', Carbon::now());
        });
        $this->addHook(NomadicMigration::POST_MIGRATE, function () use ($self) {
            $self->setProperty('finished_at', Carbon::now());
        });
    }
}
