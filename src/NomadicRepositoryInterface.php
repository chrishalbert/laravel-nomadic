<?php

namespace ChrisHalbert\LaravelNomadic;

use Illuminate\Database\Migrations\MigrationRepositoryInterface;

/**
 * Interface NomadicRepositoryInterface
 * @package ChrisHalbert\LaravelNomadic
 */
interface NomadicRepositoryInterface extends MigrationRepositoryInterface
{
    /**
     * Log that a migration was run.
     * @param string $file   The file.
     * @param int    $batch  The batch.
     * @param array  $params Additional properties.
     * @return void
     */
    public function log($file, $batch, $params = array());

    /**
     * Get all the properties.
     * @param string $migrationFileName The filename
     * @return array
     */
    public function getProperties($migrationFileName);
}
