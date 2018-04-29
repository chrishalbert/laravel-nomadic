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
        $stub = parent::populateStub($name, $stub, $table);

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
