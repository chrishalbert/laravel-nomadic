<?php

use ChrisHalbert\LaravelNomadic\Traits\Timestampable;
use ChrisHalbert\LaravelNomadic\NomadicMigration;

class NomadicMigrationWithTimestampable extends NomadicMigration
{
    use Timestampable;
}