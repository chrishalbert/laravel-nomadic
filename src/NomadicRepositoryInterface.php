<?php

namespace ChrisHalbert\LaravelNomadic;

use Illuminate\Database\Migrations\MigrationRepositoryInterface;

interface NomadicRepositoryInterface extends MigrationRepositoryInterface
{
    /**
     * Log that a migration was run.
     *
     * @param  string  $file
     * @param  int     $batch
     * @param  array   $params
     * @return void
     */
    public function log($file, $batch, $params = []);
}
