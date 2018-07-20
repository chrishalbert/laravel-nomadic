<?php

namespace ChrisHalbert\LaravelNomadic;

use ChrisHalbert\LaravelNomadic\Hooks\NomadicMigrationHookInterface;

class NomadicMigrationTest extends \PHPUnit_Framework_TestCase
{
    protected $migration;

    protected $repo;

    public function setUp()
    {
        $this->repo = $this->getMockBuilder(DatabaseNomadicRepository::class)
            ->setMethods(['getProperties'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->migration = $this->getMockForAbstractClass(
            NomadicMigration::class,
            [$this->repo]
        );
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Configs for nomadic hook `construct` must be an array.
     */
    public function testInvalidConfigHookAssignment()
    {
        \ConfigMock::set('nomadic.hooks', ['construct' => 5]);
        $this->getMockForAbstractClass(
            NomadicMigration::class,
            [$this->repo],
            '',
            true
        );
    }

    public function testMigrationHooks()
    {
        $construct = [$this->getMockMigrationHook(NomadicMigration::CONSTRUCT)];
        $preMigrate = [$this->getMockMigrationHook(NomadicMigration::PRE_MIGRATE)];
        $postMigrate = [$this->getMockMigrationHook(NomadicMigration::POST_MIGRATE)];
        $preRollback = [$this->getMockMigrationHook(NomadicMigration::PRE_ROLLBACK)];
        $postRollback = [$this->getMockMigrationHook(NomadicMigration::POST_ROLLBACK)];
        $destruct = [$this->getMockMigrationHook(NomadicMigration::DESTRUCT)];

        \ConfigMock::set('nomadic.hooks', compact(
            'construct',
            'preMigrate',
            'postMigrate',
            'preRollback',
            'postRollback',
            'destruct'
        ));

        $hookedUpMigration = $this->getMockForAbstractClass(
            NomadicMigration::class,
            [$this->repo]
        );

        $this->expectOutputString('constructpreMigratepostMigratepreRollbackpostRollbackdestruct');
        $hookedUpMigration->up();
        $hookedUpMigration->down();
        $hookedUpMigration->__destruct();
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Invalid migration hook `fubar`.
     */
    public function testVerifyValidHooksThrowsException()
    {
        $this->migration->clearHooks('fubar');
    }

    public function testMigrateRollbackDoNothing()
    {
        $this->assertNull($this->migration->migrate());
        $this->assertNull($this->migration->rollback());
    }

    public function testAddHooksGetHooksClearHooks()
    {
        $this->migration->addHook(NomadicMigration::PRE_MIGRATE, $this->getMockMigrationHook(NomadicMigration::PRE_MIGRATE));
        $this->migration->addHook(NomadicMigration::POST_MIGRATE, $this->getMockMigrationHook(NomadicMigration::POST_MIGRATE));
        $this->assertNotEmpty($this->migration->getHooks(NomadicMigration::PRE_MIGRATE));
        $this->assertNotEmpty($this->migration->getHooks(NomadicMigration::POST_MIGRATE));

        $this->expectOutputString('preMigratepostMigrate');
        // Verify the migration can run when added manually
        $this->migration->up();


        $this->migration->clearHooks(NomadicMigration::PRE_MIGRATE);
        $this->migration->clearHooks(NomadicMigration::POST_MIGRATE);
        $this->assertEmpty($this->migration->getHooks(NomadicMigration::PRE_MIGRATE));
        $this->assertEmpty($this->migration->getHooks(NomadicMigration::POST_MIGRATE));
        // Should not do anything else
        $this->migration->up();
    }

    public function testSetAndGetProperty()
    {
        $this->expectGetPropertiesCalled();
        $this->migration->setProperty('author', 'Chris');
        $this->assertEquals('Chris', $this->migration->getProperty('author', false));
        $this->assertEquals('DB Chris', $this->migration->getProperty('author'));
    }

    public function testGetProperties()
    {
        $this->expectGetPropertiesCalled();
        $this->assertEquals([], $this->migration->getProperties());
        $this->assertEquals($this->mockDbProperties(), $this->migration->getProperties(true));
    }

    public function tearDown()
    {
        unset($this->repo);
        unset($this->migration);
        \ConfigMock::reset();
    }

    protected function expectGetPropertiesCalled()
    {
        $this->repo->expects($this->once())
            ->method('getProperties')
            ->withAnyParameters()
            ->willReturn($this->mockDbProperties());
    }

    protected function mockDbProperties()
    {
        return [
            'migration' => '2018_04_21_Migration',
            'batch' => 1,
            'author' => 'DB Chris'
        ];
    }

    protected function getMockMigrationHook($type)
    {
        $hook = $this->getMockBuilder(NomadicMigrationHookInterface::class)
            ->setMockClassName($type)
            ->setMethods(['execute'])
            ->getMock();

        $hook->expects($this->once())
            ->method('execute')
            ->willReturnCallback(function() use ($type) {
                echo $type;
            });
        return $hook;
    }
}
