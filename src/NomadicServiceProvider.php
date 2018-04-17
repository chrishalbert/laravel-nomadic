<?php

namespace ChrisHalbert\LaravelNomadic;

use Illuminate\Database\MigrationServiceProvider;

class NomadicServiceProvider extends MigrationServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/nomad.php' => config_path('nomad.php'),
        ]);
    }

    /**
     * Register the migration repository service.
     *
     * @return void
     */
    protected function registerRepository()
    {
        $this->app->singleton('migration.repository', function ($app) {
            $table = $app['config']['database.migrations'];

            return new DatabaseNomadizationRepository($app['db'], $table);
        });
    }

    /**
     * Register the migrator service.
     *
     * @return void
     */
    protected function registerMigrator()
    {
        // The migrator is responsible for actually running and rollback the migration
        // files in the application. We'll pass in our database connection resolver
        // so the migrator can resolve any of these connections when it needs to.
        $this->app->singleton('migrator', function ($app) {
            $repository = $app['migration.repository'];

            return new Nomad($repository, $app['db'], $app['files']);
        });
    }
}
