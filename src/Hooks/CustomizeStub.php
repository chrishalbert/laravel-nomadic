<?php

namespace ChrisHalbert\LaravelNomadic\Hooks;

/**
 * Class Customize
 * @package ChrisHalbert\LaravelNomadic\Hooks
 */
class CustomizeStub implements NomadicCreateHookInterface
{
    /**
     * Executes a function with parameters the create receives.
     * @param string  $name      The name of the migration.
     * @param string  $path      The path.
     * @param string  $table     The name of the table.
     * @param boolean $create    Whether to use create stub.
     * @param string  $className The generated name of the class.
     * @param string  $filePath  The full path to the file.
     * @return void
     */
    public function execute(
        string $name = '',
        string $path = '',
        string $table = null,
        bool $create = false,
        string $className = '',
        string $filePath = ''
    ) {
        // Only a custom stub path OR the stub's template vars can be used
        // If the stub path is given, it is used.
        $customStubPath = config('nomadic.stub.path') ?? '';
        if ($customStubPath) {
            app('migration.creator')->setStubPath($customStubPath);
        }

        return;

        $stubVaribles = config('nomadic.stub.variables');
        if (is_array($stubVaribles) && !empty($stubVaribles)) {
            app('migration.creator')->setStubVariables($stubVaribles);
        }
    }
}
