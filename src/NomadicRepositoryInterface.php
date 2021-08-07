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
     * @param mixed $file   A string of the file.
     * @param mixed $batch  The int # of the batch.
     * @param array $params The params.
     * @return void
     */
    public function log($file, $batch, array $params = array());

    /**
     * Get all the properties.
     * @param string $migrationFileName The filename
     * @return array
     */
    public function getProperties(string $migrationFileName);
}
