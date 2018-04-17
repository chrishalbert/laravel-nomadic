<?php

namespace ChrisHalbert\LaravelNomadic;

use Illuminate\Database\Migrations\Migration;

abstract class NomadicMigration extends Migration
{
    protected $properties = array();

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
}