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
        $configHooks = config('nomadic.hooks');
        foreach ($this->migrationHooks as $hook => &$values) {
            if (isset($configHooks[$hook]) && !is_array($configHooks[$hook])) {
                throw new \Exception("Configs for nomadic hook `{$hook}` must an array.");
            }

            $values = $configHooks[$hook];
        }

        $this->runHooks(self::CONSTRUCT);
    }

    /**
     * Runs the up with the hooks.
     * @return void
     */
    public function up()
    {
        $this->runHooks(self::PRE_MIGRATE);
        static::migrate();
        $this->runHooks(self::POST_MIGRATE);
    }

    /**
     * Runs the down with the hooks.
     * @return void
     */
    public function down()
    {
        $this->runHooks(self::PRE_ROLLBACK);
        static::rollback();
        $this->runHooks(self::POST_ROLLBACK);
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
     * @param string                        $name Name of the hook.
     * @param NomadicMigrationHookInterface $hook The hook being added.
     * @return void
     * @throws \Exception If the hook is not an instance of a NomadicMigrationHookInterface.
     */
    public function addHook($name, NomadicMigrationHookInterface $hook)
    {
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
     * Verifies whether or not the hook is a valid type.
     * @param string $name Name of a hook.
     * @throws \Exception If the hook is not an instance of a NomadicMigrationHookInterface.
     */
    protected function verifyValidHook($name)
    {
        if (isset($this->migrationHooks[$name])) {
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
     * @param NomadicMigrationHookInterface $hook A hook to execute.
     */
    protected function runHook(NomadicMigrationHookInterface $hook)
    {
        $hook->execute();
    }

    /**
     * Reassigns the properties with the database.
     */
    protected function syncWithDb()
    {
        $this->properties = $this->repository->getProperties($this->fileName);
    }
}
