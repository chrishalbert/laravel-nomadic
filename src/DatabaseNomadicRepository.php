<?php

namespace ChrisHalbert\LaravelNomadic;

use Illuminate\Database\Migrations\DatabaseMigrationRepository;

class DatabaseNomadicRepository extends DatabaseMigrationRepository implements NomadicRepositoryInterface
{
    public function log($file, $batch, $params = [])
    {
        $schema = config('nomadic.schema');
        unset($params['migrations']);
        unset($params['batch']);

        // Only allow schema defined columns to be used in migrations
        $params = array_filter($params, function($i) use ($schema) {
            return in_array($i, $schema);
        }, ARRAY_FILTER_USE_KEY);


        $record = array('migration' => $file, 'batch' => $batch);
        $record = array_merge($record, $params);

        $this->table()->insert($record);
    }
}
