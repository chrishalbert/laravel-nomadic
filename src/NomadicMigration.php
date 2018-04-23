<?php

namespace ChrisHalbert\LaravelNomadic;

use Illuminate\Database\Migrations\Migration;

/**
 * Class NomadicMigration
 * @package ChrisHalbert\LaravelNomadic
 */
abstract class NomadicMigration extends Migration
{
    /**
     * Additional properties or column names in your migration table.
     * @var array
     */
    protected $properties;

    /**
     * Repository to access the migration.
     * @var NomadicRepositoryInterface
     */
    protected $repository;

    /**
     * The name of the migration file name, with timestamp.
     * @var string
     */
    protected $fileName;

    /**
     * NomadicMigration constructor.
     * @param NomadicRepositoryInterface $repository The repository.
     */
    public function __construct(NomadicRepositoryInterface $repository)
    {
        $this->properties = array();
        $this->repository = $repository;
        $this->fileName = basename((new \ReflectionClass($this))->getFileName(), '.php');
    }

    /**
     * Set the values of this specific migration.
     * @param string $key   The column name.
     * @param mixed  $value The value to be inserted.
     * @return void
     */
    public function setProperty($key, $value)
    {
        $this->properties[$key] = $value;
    }

    /**
     * Syncs up w/ the database and returns the property.
     * @param string $key     The column name of the migration.
     * @param bool   $persist By default, we get the property from the db.
     * @return mixed
     */
    public function getProperty($key, $persist = true)
    {
        if ($persist) {
            $this->syncWithDb();
        }
        return $this->properties[$key];
    }

    /**
     * Returns all properties of the migration.
     * @param bool   $persist Whether to sync with the db first.
     * @return mixed
     */
    public function getProperties($persist = false)
    {
        if ($persist) {
            $this->syncWithDb();
        }
        return $this->properties;
    }

    /**
     * Reassigns the properties with the database.
     */
    protected function syncWithDb()
    {
        $this->properties = $this->repository->getProperties($this->fileName);
    }
}
