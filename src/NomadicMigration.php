<?php

namespace ChrisHalbert\LaravelNomadic;

use Illuminate\Database\Migrations\Migration;

abstract class NomadicMigration extends Migration
{
    protected $properties;

    protected $repository;

    protected $fileName;

    public function __construct(NomadicRepositoryInterface $repository)
    {
        $this->properties = array();
        $this->repository = $repository;
        $this->fileName = basename((new \ReflectionClass($this))->getFileName(), '.php');
    }

    public function setProperty($key, $value)
    {
        $this->properties[$key] = $value;
    }

    public function getProperty($key)
    {
        return $this->properties[$key];
    }

    public function getProperties()
    {
        return $this->properties;
    }

    protected function syncWithDb()
    {
        $this->properties = $this->repository->getProperties($this->fileName);
    }
}