<?php

namespace ChrisHalbert\LaravelNomadic\Hooks;

/**
 * Interface NomadicHookInterface
 * @package ChrisHalbert\LaravelNomadic\Hooks
 */
interface NomadicHookInterface
{
    /**
     * Executes a function with parameters the create receives.
     * @param string $name      The name of the migration.
     * @param string $path      The path.
     * @param string $table     The name of the table.
     * @param mixed  $create    Whether to use create stub.
     * @param string $className The generated name of the class.
     * @param string $filePath  The full path to the file.
     * @return string
     */
    public function execute(
        string $name = '',
        string $path = '',
        string $table = null,
        $create = false,
        string $className = '',
        string $filePath = ''
    );
}
