<?php

namespace ChrisHalbert\LaravelNomadic;

use ChrisHalbert\LaravelNomadic\Hooks\NomadicHookInterface;
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

    public function testBeforeCreateExecuteThrowsException()
    {
        $this->setExpectedException(
            \InvalidArgumentException::class,
            'Hook must be an instance of a ' . NomadicHookInterface::class . ' or Closure, `string` given.'
        );
        $this->creator->beforeCreateExecute('');
    }

    public function testAfterCreateExecuteThrowsException()
    {
        $obj = new \stdClass();

        $this->setExpectedException(
            \InvalidArgumentException::class,
            'Hook must be an instance of a ' . NomadicHookInterface::class . ' or Closure, `stdClass` given.'
        );
        $this->creator->afterCreateExecute($obj);
    }

    public function testCreateWithHooks()
    {
        $hook = $this->getMockBuilder(NomadicHookInterface::class)
            ->setMethods(['execute'])
            ->getMock();

        $afterCallback = function() use ($hook) {
            $hook->execute('afterCallback');
        };

        $beforeCallback = function() use ($hook) {
            $hook->execute('beforeCallback');
        };

        $this->creator->beforeCreateExecute($beforeCallback); // first
        $this->creator->afterCreateExecute($hook, ['afterName', '', 'afterTable', true, 'PostHook', '/file/PostHook.php']); // third
        $this->creator->beforeCreateExecute($hook, ['beforeName', 'beforePath']); // second
        $this->creator->afterCreateExecute($afterCallback); // fourth

        $hook->expects($spy = $this->any())
            ->method('execute');

        $this->creator->files = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->setMethods(['put'])
            ->getMock();
        $this->creator->files->expects($this->once())
            ->method('put');

        $this->creator->create('name', 'path');

        $invocations = $spy->getInvocations();

        $this->assertEquals(4, count($invocations));
        $this->assertEquals(['beforeCallback', '', null, false, '', ''], $invocations[0]->parameters);
        $this->assertEquals(['beforeName', 'beforePath', null, false, '', ''], $invocations[1]->parameters);
        $this->assertEquals(['afterName', '', 'afterTable', true, 'PostHook', '/file/PostHook.php'], $invocations[2]->parameters);
        $this->assertEquals(['afterCallback', '', null, false, '', ''], $invocations[3]->parameters);
    }

    public function tearDown()
    {
        unset($this->creator);
    }
}
