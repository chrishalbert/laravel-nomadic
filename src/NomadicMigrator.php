<?php
namespace ChrisHalbert\LaravelNomadic;

use Illuminate\Database\Migrations\Migrator;
use ChrisHalbert\LaravelNomadic\NomadicRepositoryInterface;
use ChrisHalbert\LaravelNomadic\NomadicMigration;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\ConnectionResolverInterface as Resolver;
use Illuminate\Support\Str;

/**
 * Class NomadicMigrator
 * @package ChrisHalbert\LaravelNomadic
 */
class NomadicMigrator extends Migrator
{
    /**
     * The migration repository implementation.
     * @var \Illuminate\Database\Migrations\MigrationRepositoryInterface
     */
    protected $repository;

    /**
     * Create a new migrator instance.
     * @param  NomadicRepositoryInterface                       $repository The nomadic repository.
     * @param  \Illuminate\Database\ConnectionResolverInterface $resolver   The resolver.
     * @param  \Illuminate\Filesystem\Filesystem                $files      The files.
     * @return void
     */
    public function __construct(
        NomadicRepositoryInterface $repository,
        Resolver $resolver,
        Filesystem $files
    ) {
        $this->files = $files;
        $this->resolver = $resolver;
        $this->repository = $repository;
    }

    /**
     * Run "up" a migration instance.
     * @param string $file    The file of the migration.
     * @param int    $batch   The batch number.
     * @param bool   $pretend Whether or not we pretend.
     * @return void
     */
    protected function runUp($file, $batch, $pretend)
    {
        // First we will resolve a "real" instance of the migration class from this
        // migration file name. Once we have the instances we can run the actual
        // command such as "up" or "down", or we can just simulate the action.
        $migration = $this->resolve(
            $name = $this->getMigrationName($file)
        );

        if ($pretend) {
            return $this->pretendToRun($migration, 'up');
        }

        $this->note("<comment>Migrating:</comment> {$name}");

        $this->runMigration($migration, 'up');

        // For backwards compatability, files not created with Nomadic will still run normally
        $properties = array();
        if (method_exists($migration, 'getProperties')) {
            $properties = array_merge($properties, $migration->getProperties());
        }

        // Once we have run a migrations class, we will log that it was run in this
        // repository so that we don't try to run it next time we do a migration
        // in the application. A migration repository keeps the migrate order.
        $this->repository->log($name, $batch, $properties);

        $this->note("<info>Migrated:</info>  {$name}");
    }

    /**
     * Resolve a migration instance from a file.
     * @param string $file The file name.
     * @return object
     */
    public function resolve($file)
    {
        $class = Str::studly(implode('_', array_slice(explode('_', $file), 4)));

        // For backwards compatability
        if (!is_subclass_of($class, NomadicMigration::class)) {
            return new $class;
        }

        return new $class(app('migration.repository'));
    }
}
