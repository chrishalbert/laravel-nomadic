<?php

use ChrisHalbert\LaravelNomadic\NomadicMigration;

class NomadicMockMigration extends NomadicMigration
{
    public function getProperties($syncWithDb = false)
    {
        return ['property' => 'value'];
    }
}
