<?php

use ChrisHalbert\LaravelNomadic\NomadicMigration;

class NomadicMockMigration extends NomadicMigration {
    public function getProperties()
    {
        return ['property' => 'value'];
    }
}