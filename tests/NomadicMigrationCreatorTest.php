<?php

namespace ChrisHalbert\LaravelNomadic;

use Illuminate\Filesystem\Filesystem;

class NomadicMigrationCreatorTest extends \PHPUnit_Framework_TestCase
{
    protected $creator;

    public function setUp()
    {
        $this->creator = new NomadicMigrationCreator(new Filesystem());
    }

    public function testGetStubPathMethods()
    {
        $path = dirname(dirname(__FILE__)) . '/src/stubs';
        $this->assertEquals($path, $this->creator->stubPath());
    }

    public function tearDown()
    {
        unset($this->creator);
    }
}
