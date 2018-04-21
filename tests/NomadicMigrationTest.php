<?php

namespace ChrisHalbert\LaravelNomadic;

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

        $this->repo->expects($this->once())
            ->method('getProperties')
            ->willReturn($this->mockProperties());

        $this->migration = $this->getMockForAbstractClass(
            NomadicMigration::class,
            [$this->repo],
            ''
        );
    }

    public function testSetAndGetProperty()
    {
        $this->migration->setProperty('author', 'Chris');
        $this->assertEquals('Chris', $this->migration->getProperty('author'));
    }

    public function testGetProperties()
    {
        $this->assertEquals($this->mockProperties(), $this->migration->getProperties());
    }

    public function tearDown()
    {
        unset($this->repo);
        unset($this->migration);
    }

    protected function mockProperties()
    {
        return [
            'migration' => '2018_04_21_Migration',
            'batch' => 1,
            'author' => 'Chris'
        ];
    }
}