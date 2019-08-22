<?php

namespace ChrisHalbert\LaravelNomadic;

use Illuminate\Database\Migrations\DatabaseMigrationRepository;

/**
 * Class DatabaseNomadicRepository
 * @package ChrisHalbert\LaravelNomadic
 */
class DatabaseNomadicRepository extends DatabaseMigrationRepository implements NomadicRepositoryInterface
{
    /**
     * Log the data.
     * @param string $file   The file.
     * @param int    $batch  The batch #.
     * @param array  $params The params.
     * @return void
     */
    public function log($file, $batch, array $params = [])
    {
        $schema = config('nomadic.schema');
        unset($params['migrations']);
        unset($params['batch']);

        // Only allow schema defined columns to be used in migrations
        $params = array_filter($params, function ($i) use ($schema) {
            return in_array($i, $schema);
        }, ARRAY_FILTER_USE_KEY);

        $record = array('migration' => $file, 'batch' => $batch);
        $record = array_merge($record, $params);

        $this->table()->insert($record);
    }

    /**
     * Get all the properties.
     * @param string $migrationFileName The filename
     * @return array
     */
    public function getProperties(string $migrationFileName)
    {
        $existingMigration = $this->table()->where('migration', $migrationFileName)->first();
        if (is_object($existingMigration)) {
            return get_object_vars($existingMigration);
        }

        return [];
    }
}
