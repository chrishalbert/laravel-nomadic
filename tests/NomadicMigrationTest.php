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
            ->withAnyParameters()
            ->willReturn($this->mockDbProperties());

        $this->migration = $this->getMockForAbstractClass(
            NomadicMigration::class,
            [$this->repo],
            ''
        );
    }

    public function testSetAndGetProperty()
    {
        $this->migration->setProperty('author', 'Chris');
        $this->assertEquals('Chris', $this->migration->getProperty('author', false));
        $this->assertEquals('DB Chris', $this->migration->getProperty('author'));
    }

    public function testGetProperties()
    {
        $this->assertEquals([], $this->migration->getProperties());
        $this->assertEquals($this->mockDbProperties(), $this->migration->getProperties(true));
    }

    public function tearDown()
    {
        unset($this->repo);
        unset($this->migration);
    }

    protected function mockDbProperties()
    {
        return [
            'migration' => '2018_04_21_Migration',
            'batch' => 1,
            'author' => 'DB Chris'
        ];
    }
}
