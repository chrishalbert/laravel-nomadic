<?php

namespace ChrisHalbert\LaravelNomadic;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Traits\CapsuleManagerTrait;
use Illuminate\Support\Traits\Macroable;
use SebastianBergmann\PeekAndPoke\Proxy;

class NomadicMigrationCreatorTest extends \PHPUnit_Framework_TestCase
{
    protected $creator;

    public function setUp()
    {
        $this->creator = new Proxy(new NomadicMigrationCreator(new Filesystem()));
    }

    public function testGetStubPathMethods()
    {
        $path = dirname(dirname(__FILE__)) . '/src/stubs';
        $this->assertEquals($path, $this->creator->stubPath());
    }

    public function testPopulateStub()
    {
        $stub = file_get_contents(__DIR__ . '/../src/stubs/blank.stub');
        $stub = $this->creator->populateStub('TestingTraits', $stub, '');
        $this->assertContains('use CapsuleManagerTrait, Macroable;', $stub);
        $this->assertContains('use ' . CapsuleManagerTrait::class . ';', $stub);
        $this->assertContains('use ' . Macroable::class . ';', $stub);
    }

    public function tearDown()
    {
        unset($this->creator);
    }
}
