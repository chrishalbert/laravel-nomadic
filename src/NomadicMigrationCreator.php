<?php

namespace ChrisHalbert\LaravelNomadic;

use Illuminate\Database\Migrations\MigrationCreator;

/**
 * Class NomadicMigrationCreator
 * @package ChrisHalbert\LaravelNomadic
 */
class NomadicMigrationCreator extends MigrationCreator
{
    const USE_STUB = "use %s;";

    protected function populateStub($name, $stub, $table)
    {
        $stub = str_replace('DummyClass', $this->getClassName($name), $stub);

        // Here we will replace the table place-holders with the table specified by
        // the developer, which is useful for quickly creating a tables creation
        // or update migration from the console instead of typing it manually.
        if (! is_null($table)) {
            $stub = str_replace('DummyTable', $table, $stub);
        }

        $traits = config('nomadic.traits');

        if (!empty($traits) && is_array($traits)) {
            $stub = $this->appendTraits($traits, $stub);
        }

        return $stub;
    }

    protected function appendTraits($stub, $traits)
    {
        $newlyIncluded = "";
        $uses = array();
        foreach ($traits as $trait) {
            $trait .= sprintf(self::USE_STUB, $trait) . PHP_EOL;
            $uses[] = array_pop(explode('\\', $trait));
        }

        $currentlyIncluded = sprintf(self::USE_STUB, NomadicMigration::class);
        $stub = str_replace($currentlyIncluded, $currentlyIncluded . PHP_EOL . $newlyIncluded, $stub);
        $stub = str_replace('// DummyTraits', sprintf(self::USE_STUB, implode(", ", $uses)), $stub);
        return $stub;
    }

    /**
     * Get the path to the stubs.
     * @return string
     */
    public function getStubPath()
    {
        return __DIR__ . '/stubs';
    }

    /**
     * Get the path to the stubs.
     * @return string
     */
    public function stubPath()
    {
        return $this->getStubPath();
    }
}
