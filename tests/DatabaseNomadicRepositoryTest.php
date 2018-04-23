<?php

namespace ChrisHalbert\LaravelNomadic;

use Illuminate\Support\Collection;

function config()
{
    return ['name', 'date'];
}

class DatabaseNomadicRepositoryTest extends \PHPUnit_Framework_TestCase
{
    protected $repo;

    public function setUp()
    {
        $this->repo = $this->getMockBuilder(DatabaseNomadicRepository::class)
            ->setMethods(array('table'))
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testLog()
    {
        $collection = $this->getMockBuilder(Collection::class)
            ->setMethods(['insert'])
            ->getMock();

        $collection->expects($this->once())
            ->method('insert')
            ->with(array_merge($this->getMockData(), ['migration' => 'file', 'batch' => 1]));

        $this->repo->expects($this->once())
            ->method('table')
            ->willReturn($collection);

        $this->repo->log('file', 1, array_merge($this->getMockData(), ['dataShouldNot' => 'be included']));
    }

    public function testGetProperties()
    {
        $fileName = '2018_04_21_Migration';

        $collection = new Collection([(object) array_merge($this->getMockData(), ['migration' => $fileName, 'batch' => 1])]);

        $this->repo->expects($this->once())
            ->method('table')
            ->willReturn($collection);

        $this->assertNotEmpty($this->repo->getProperties($fileName));
    }

    public function testGetPropertiesWithUnRunMigration()
    {
        $fileName = '2018_04_21_Migration';

        $collection = new Collection();

        $this->repo->expects($this->once())
            ->method('table')
            ->willReturn($collection);

        $this->assertEmpty($this->repo->getProperties($fileName));
    }

    public function tearDown()
    {
        unset($this->repo);
    }

    protected function getMockData()
    {
        return array('name' => 'Chris', 'date' => '2018-10-10');
    }
}
