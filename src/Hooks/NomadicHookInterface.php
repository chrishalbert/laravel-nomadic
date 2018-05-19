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
     * @param string  $name      The name of the migration.
     * @param string  $path      The path.
     * @param string  $table     The name of the table.
     * @param boolean $create    Whether to use create stub.
     * @param string  $className The generated name of hte class.
     * @param string  $filePath  The full path to the file.
     * @return string
     */
    public function execute($name = '', $path = '', $table = null, $create = false, $className = '', $filePath = '');
}
