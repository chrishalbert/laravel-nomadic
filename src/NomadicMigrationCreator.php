<?php

namespace ChrisHalbert\LaravelNomadic;

use ChrisHalbert\LaravelNomadic\Hooks\NomadicHookInterface;
use Doctrine\Instantiator\Exception\InvalidArgumentException;
use Illuminate\Database\Migrations\MigrationCreator;

/**
 * Class NomadicMigrationCreator
 * @package ChrisHalbert\LaravelNomadic
 */
class NomadicMigrationCreator extends MigrationCreator
{
    /**
     * Use statement.
     * @const string
     */
    const USE_STUB = "use %s;";

    /**
     * Exception message.
     * @const string
     */
    const INVALID_HOOK = "Hook must be an instance of a %s or %s, `%s` given.";

    /**
     * Name of the class being created.
     * @var string|null
     */
    protected $className = null;

    /**
     * Full path to the file of the migration.
     * @var string|null
     */
    protected $filePath = null;

    /**
     * The registered pre create hooks.
     *
     * @var array
     */
    protected $preCreate = [];

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

    /**
     * Register a pre migration create hook.
     * @param \Closure $callback The callback to execute.
     * @param array    $params   The parameters if applicable.
     * @return void
     */
    public function beforeCreateExecute($callback, array $params = [])
    {
        $this->appendHook($this->preCreate, $callback, $params);
    }

    /**
     * Register a post migration create hook.
     * @param \Closure $callback The callback to execute.
     * @param mixed    $params   The parameters if applicable.
     * @return void
     */
    public function afterCreateExecute($callback, $params = null)
    {
        $this->appendHook($this->postCreate, $callback, $params);
    }

    /**
     * Creates a migration after registering custom hooks and firing pre create hooks.
     * @param string  $name   The name of the migration.
     * @param string  $path   The path.
     * @param string  $table  The name of the table.
     * @param boolean $create Whether to use create stub.
     * @return string
     */
    public function create($name, $path, $table = null, $create = false)
    {
        $params = [$name, $path, $table, $create, $this->getClassName($name), $this->getPath($name, $path)];
        $this->registerHooks($params);
        $this->firePreCreateHooks();
        return parent::create($name, $path, $table, $create);
    }

    /**
     * Gets the class name.
     * @param string $name
     * @return null|string
     */
    protected function getClassName($name)
    {
        if (!isset($this->className)) {
            $this->className = parent::getClassName($name);
        }

        return $this->className;
    }

    /**
     * Gets the file path.
     * @param string $name
     * @param string $path
     * @return string
     */
    protected function getPath($name, $path)
    {
        if (!isset($this->filePath)) {
            $this->filePath = parent::getPath($name, $path);
        }

        return $this->filePath;
    }

    protected function appendHook(&$hookStack, $callback, $params)
    {
        // Maintain backwards compatability
        if ($callback instanceof \Closure) {
            $hookStack[] = $callback;
            return;
        }

        if ($callback instanceof NomadicHookInterface) {
            $hookStack[] = ['callback' => [$callback, 'execute'], 'params' => $params];
            return;
        }

        $type = gettype($callback);
        if ($type === 'object') {
            $type = get_class($callback);
        }

        throw new InvalidArgumentException(
            sprintf(
                self::INVALID_HOOK,
                NomadicHookInterface::class,
                \Closure::class,
                $type
            )
        );
    }

    protected function registerHooks($params)
    {
        $beforeHooks = config('nomadic.hooks.preCreate');
        foreach ($beforeHooks as $hook) {
            $this->beforeCreateExecute($hook, $params);
        }

        $afterHooks = config('nomadic.hooks.postCreate');
        foreach ($afterHooks as $hook) {
            $this->afterCreateExecute($hook, $params);
        }
    }

    protected function populateStub($name, $stub, $table)
    {
        $stub = parent::populateStub($name, $stub, $table);

        $traits = config('nomadic.traits');

        if (!empty($traits) && is_array($traits)) {
            $stub = $this->appendTraits($stub, $traits);
        }

        return $stub;
    }

    protected function appendTraits($stub, $traits)
    {
        $newlyIncluded = "";
        $uses = array();
        foreach ($traits as $trait) {
            $newlyIncluded .= sprintf(self::USE_STUB, $trait) . PHP_EOL;
            $trait = explode('\\', $trait);
            $uses[] = array_pop($trait);
        }

        $currentlyIncluded = sprintf(self::USE_STUB, NomadicMigration::class) . PHP_EOL;
        $stub = str_replace($currentlyIncluded, $currentlyIncluded . $newlyIncluded, $stub);
        $stub = str_replace('// DummyTraits', sprintf(self::USE_STUB, implode(", ", $uses)), $stub);
        return $stub;
    }

    protected function firePostCreateHooks($table = null)
    {
        $this->fireHook($this->postCreate);
    }

    /**
     * Fire the registered post create hooks.
     *
     * @return void
     */
    protected function firePreCreateHooks()
    {
        $this->fireHook($this->preCreate);
    }

    protected function fireHook($hooks)
    {
        foreach ($hooks as $callback) {
            // Backwards compatability support
            if ($callback instanceof \Closure) {
                call_user_func($callback);
                continue;
            }

            if (is_array($callback)) {
                call_user_func_array($callback['callback'], $callback['params']);
            }
        }
    }
}
