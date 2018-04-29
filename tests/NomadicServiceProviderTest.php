<?php

namespace ChrisHalbert\LaravelNomadic;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Console\Application;
use Illuminate\Database\ConnectionResolver;
use Illuminate\Filesystem\Filesystem;
use SebastianBergmann\PeekAndPoke\Proxy;

class NomadicServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    protected $serviceProvider;

    protected $app;

    public function setUp()
    {
        $this->app = $this->getMockBuilder(Container::class)
            ->setMethods(['call', 'output', 'singleton'])
            ->getMock();

        $this->app = new Container();

        $this->app->bind('config', Repository::class);
        $this->app->bind('db', ConnectionResolver::class);
        $this->app->bind('files', Filesystem::class);

        $this->serviceProvider = new Proxy($this->getMockBuilder(NomadicServiceProvider::class)
            ->setMethods(['publishes'])
            ->setConstructorArgs([$this->app])
            ->getMock());
    }

    public function testBoot()
    {
        function config_path()
        {
            return 'some/path';
        }
        $dir = dirname(dirname(__FILE__)) . '/src/nomadic.php';
        $this->serviceProvider->expects($this->once())
            ->method('publishes')
            ->with([$dir => 'some/path']);
        $this->serviceProvider->boot();
    }

    public function testRegister()
    {
        $this->serviceProvider->register();
        $this->assertInstanceOf(DatabaseNomadicRepository::class, $this->app->make('migration.repository'));
        $this->assertInstanceOf(NomadicMigrator::class, $this->app->make('migrator'));
        $this->assertInstanceOf(NomadicMigrationCreator::class, $this->app->make('migration.creator'));
    }

    public function tearDown()
    {
        unset($this->serviceProvider);
        unset($this->app);
    }
}
