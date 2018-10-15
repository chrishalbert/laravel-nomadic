<?php

namespace ChrisHalbert\LaravelNomadic;

use ChrisHalbert\LaravelNomadic\Hooks\NomadicMigrationHookInterface;
use Illuminate\Database\Migrations\Migration;

/**
 * Class NomadicMigration
 * @package ChrisHalbert\LaravelNomadic
 */
abstract class NomadicMigration extends Migration
{
    const CONSTRUCT = 'construct';

    const PRE_MIGRATE = 'preMigrate';

    const POST_MIGRATE = 'postMigrate';

    const PRE_ROLLBACK = 'preRollback';

    const POST_ROLLBACK = 'postRollback';

    const DESTRUCT = 'destruct';

    const HOOK_TYPE_EXCEPTION = 'Must be an instance of a `NomadicMigrationHookInterface` or `Closure`';

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
     * Migration hooks.
     * @var array
     */
    private $migrationHooks = [
        self::CONSTRUCT => [],
        self::PRE_MIGRATE => [],
        self::POST_MIGRATE => [],
        self::PRE_ROLLBACK => [],
        self::POST_ROLLBACK => [],
        self::DESTRUCT => []
    ];

    /**
     * NomadicMigration constructor.
     * @param NomadicRepositoryInterface $repository The repository.
     * @throws \Exception If the hook isset not as an array though.
     */
    public function __construct(NomadicRepositoryInterface $repository)
    {
        $this->properties = array();
        $this->repository = $repository;
        $this->fileName = basename((new \ReflectionClass($this))->getFileName(), '.php');
        $this->initHooks();
        $this->initTraits();

        $this->runHooks(self::CONSTRUCT);
    }

    /**
     * Run the migrations.
     *
     * @return void
     * @throws \Exception
     */
    public function up()
    {
        $this->runHooks(self::PRE_MIGRATE);
        $this->migrate();
        $this->runHooks(self::POST_MIGRATE);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     * @throws \Exception
     */
    public function down()
    {
        $this->runHooks(self::PRE_ROLLBACK);
        $this->rollback();
        $this->runHooks(self::POST_ROLLBACK);
    }

    /**
     * Placeholder to define migrations. Not an abstract method in case the developer
     * chooses to override the up() and to ensure backwards compatibility.
     * @return void
     */
    public function migrate()
    {
        return;
    }

    /**
     * Placeholder to define rollback. Not an abstract method in case the developer
     * chooses to override the down() and to ensure backwards compatibility.
     * @return void
     */
    public function rollback()
    {
        return;
    }

    /**
     * Returns the filename.
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
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
     * @param bool $persist Whether to sync with the db first.
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
     * Adds a hook to a specific hook type.
     * @param string                                $name Name of the hook.
     * @param NomadicMigrationHookInterface|Closure $hook The hook being added.
     * @return void
     * @throws \Exception If the hook is not an instance of a NomadicMigrationHookInterface.
     */
    public function addHook($name, $hook)
    {
        if (!($hook instanceof NomadicMigrationHookInterface || $hook instanceof \Closure)) {
            throw new \Exception(self::HOOK_TYPE_EXCEPTION);
        }

        $this->verifyValidHook($name);

        $this->migrationHooks[$name][] = $hook;
    }

    /**
     * Returns the hooks for a hook type.
     * @param string $name Name of the hook.
     * @return array
     * @throws \Exception If the hook is not an instance of a NomadicMigrationHookInterface.
     */
    public function getHooks($name)
    {
        $this->verifyValidHook($name);

        return $this->migrationHooks[$name];
    }

    /**
     * Clears certain hooks.
     * @param string $name Name of a hook.
     * @return void
     * @throws \Exception If the hook is not an instance of a NomadicMigrationHookInterface.
     */
    public function clearHooks($name)
    {
        $this->verifyValidHook($name);
        $this->migrationHooks[$name] = [];
    }

    /**
     * Destructor for the class.
     */
    public function __destruct()
    {
        $this->runHooks(self::DESTRUCT);
    }

    /**
     * Initializes the hooks. Since hooks are a little more limited than traits, this happens
     * before the initTraits.
     * @throws \Exception
     */
    protected function initHooks()
    {
        $configHooks = config('nomadic.hooks');
        foreach ($this->migrationHooks as $hook => &$values) {
            if (isset($configHooks[$hook]) && !is_array($configHooks[$hook])) {
                throw new \Exception("Configs for nomadic hook `{$hook}` must be an array.");
            }

            if (isset($configHooks[$hook])) {
                $values = $configHooks[$hook];
            }
        }
    }

    /**
     * Initializes the traits based on the trait `ClassName` searching for initClassName() method.
     * This should come after initHooks. InitTraits can initialize hooks and if reordering of hooks
     * is necessary, it can happen in the traits.
     */
    protected function initTraits()
    {
        $traits = class_uses($this);

        foreach ($traits as $trait) {
            $initMethod = sprintf("init" . class_basename($trait));
            if (method_exists($trait, $initMethod)) {
                $this->{$initMethod}();
            }
        }
    }

    /**
     * Verifies whether or not the hook is a valid type.
     * @param string $name Name of a hook.
     * @throws \Exception If the hook is not an instance of a NomadicMigrationHookInterface.
     */
    protected function verifyValidHook($name)
    {
        if (!isset($this->migrationHooks[$name])) {
            throw new \Exception("Invalid migration hook `{$name}`.");
        }
    }

    /**
     * Runs an array of hooks.
     * @param string $name Name of a hook type.
     * @throws \Exception If the hook is not an instance of a NomadicMigrationHookInterface.
     */
    protected function runHooks($name)
    {
        $this->verifyValidHook($name);
        foreach ($this->migrationHooks[$name] as $hook) {
            $this->runHook($hook);
        }
    }

    /**
     * Runs a hook.
     * @param string $hook A hook to execute.
     * @throws \Exception If not Closure or NomadicMigrationHookInterface.
     */
    protected function runHook($hook)
    {
        if ($hook instanceof NomadicMigrationHookInterface) {
            $hook->execute($this);
            return;
        }

        if ($hook instanceof \Closure) {
            $hook($this);
            return;
        }

        throw new \Exception(self::HOOK_TYPE_EXCEPTION);
    }

    /**
     * Reassigns the properties with the database.
     */
    protected function syncWithDb()
    {
        $this->properties = $this->repository->getProperties($this->fileName);
    }
}
