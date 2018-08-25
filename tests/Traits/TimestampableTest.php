<?php

namespace ChrisHalbert\LaravelNomadic\Traits;

use Carbon\Carbon;
use ChrisHalbert\LaravelNomadic\DatabaseNomadicRepository;

class TimestampableTest extends \PHPUnit_Framework_TestCase
{

    protected $repo;

    protected $migration;

    public function setUp()
    {
        $this->repo = $this->getMockBuilder(DatabaseNomadicRepository::class)
            ->setMethods(['getProperties'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->migration =  new \NomadicMigrationWithTimestampable($this->repo);
    }

    public function tearDown()
    {
        unset($this->repo);
        unset($this->migration);
        \ConfigMock::reset();
    }

    public function testTimestampableTrait()
    {
        Carbon::setTestNow($time = Carbon::now());
        $this->migration->up();
        $this->assertEquals([
            'started_at' => $time,
            'finished_at' => $time,
        ], $this->migration->getProperties());
    }
}
