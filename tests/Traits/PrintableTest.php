<?php

namespace ChrisHalbert\LaravelNomadic\Traits;

use ChrisHalbert\LaravelNomadic\DatabaseNomadicRepository;

class PrintableTest extends \PHPUnit_Framework_TestCase
{

    protected $repo;

    public function setUp()
    {
        $this->repo = $this->getMockBuilder(DatabaseNomadicRepository::class)
            ->setMethods(['getProperties'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function tearDown()
    {
        unset($this->repo);
        \ConfigMock::reset();
    }

    public function testPrintableTrait()
    {
        $migration = new \NomadicMigrationWithPrintable($this->repo);

        $this->expectOutputRegex('/' . \NomadicMigrationWithPrintable::class . ' started at/');
        $this->expectOutputRegex('/' . \NomadicMigrationWithPrintable::class . ' finished at/');

        $migration->up();
    }
}
