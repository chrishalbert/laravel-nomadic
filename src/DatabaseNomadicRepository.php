<?php

namespace ChrisHalbert\LaravelNomadic;

use Illuminate\Database\Migrations\DatabaseMigrationRepository;

class DatabaseNomadicRepository extends DatabaseMigrationRepository implements NomadicRepositoryInterface
{
    public function log($file, $batch, $params = [])
    {
        unset($params['migrations']);
        unset($params['batch']);

        $record = ['migration' => $file, 'batch' => $batch];
        $record = array_merge($record, $params);

        $this->table()->insert($record);
    }
}
