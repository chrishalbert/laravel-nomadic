<?php

namespace ChrisHalbert\LaravelNomadic;

use ChrisHalbert\LaravelNomadic\Hooks\CustomizeStub;
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

    public function testSetStubPath()
    {
        $this->creator->setStubPath('/fake/path');
        $this->assertEquals('/fake/path', $this->creator->getStubPath());
    }

    public function testPopulateStub()
    {
        $stub = file_get_contents(__DIR__ . '/../src/stubs/blank.stub');
        $stub = $this->creator->populateStub('TestingTraits', $stub, '');
        $this->assertContains('use CapsuleManagerTrait, Macroable;', $stub);
        $this->assertContains('use ' . CapsuleManagerTrait::class . ';', $stub);
        $this->assertContains('use ' . Macroable::class . ';', $stub);
    }

    public function testPopulateStubWithVariables()
    {
        $stub = file_get_contents(__DIR__ . '/../src/stubs/blank.stub');
        $stubVariables = self::getStubVariables();
        $this->creator->setStubVariables($stubVariables);
        $stub = $this->creator->populateStub('TestingTraits', $stub, '');
        foreach ($stubVariables as $stubVariable) {
            $this->assertContains($stubVariable, $stub);
        }
    }

    public function testCustomizeStubHookWithPath()
    {
        // Add mocks for config and app functions
        \ConfigMock::set('nomadic.hooks.preCreate', [new CustomizeStub()]);
        \ConfigMock::set('nomadic.stub.path', __DIR__ . '/files');
        \AppMock::set('migration.creator', $this->creator);

        $migrationName = 'CreateMigrationFromWithStubPath';
        $path = $this->creator->create($migrationName, '/tmp/');
        $customizedMigration = file_get_contents($path);
        $this->assertEquals('Fake Custom Stub', $customizedMigration);
    }

    public function testCustomizeStubHookWithVariables()
    {
        // Add mocks for config and app functions
        \ConfigMock::set('nomadic.hooks.preCreate', [new CustomizeStub()]);
        \AppMock::set('migration.creator', $this->creator);

        $migrationName = 'CreateMigrationFromWithCustomizedStub';
        $path = $this->creator->create($migrationName, '/tmp/');
        $customizedMigration = file_get_contents($path);
        $stubVariables = self::getStubVariables();
        foreach ($stubVariables as $stubVariable) {
            $this->assertContains($stubVariable, $customizedMigration);
        }
    }

    public function testBeforeCreateExecuteThrowsTypeError()
    {
        $this->setExpectedException(
            \TypeError::class,
            'Hook must be an instance of a ' . NomadicHookInterface::class . ' or Closure, `string` given.'
        );
        $this->creator->beforeCreateExecute('');
    }

    public function testAfterCreateExecuteThrowsException()
    {
        $obj = new \stdClass();

        $this->setExpectedException(
            \TypeError::class,
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
        \ConfigMock::reset();
        unset($this->creator);
    }

    protected static function getStubVariables()
    {
        return [
            'fileDocs' => <<<FILEDOCS
/**
 * File documentation.
 */            
FILEDOCS
            ,'classDocs' => <<<CLASSDOCS
/**
 * Class docs.
 */            
CLASSDOCS
            ,'traitDocs' => <<<TRAITDOCS
    /**
     * Trait docs. 
     */            
TRAITDOCS
            ,'additionalProperties' => <<<ADDITIONALPROPS
    /**
     * A property.
     * @var string
     */
    protected \$property;
ADDITIONALPROPS
            ,'migrateTemplate' => <<<MIGRATETEMPLATE
        // Migration notes
MIGRATETEMPLATE
            ,'rollbackTemplate' => <<<ROLLBACKTEMPLATE
        // Rollback notes
ROLLBACKTEMPLATE
            ,'additionalMethods' => <<<ADDITIONALMETHODS
    protected function foo() {}
ADDITIONALMETHODS
        ];

    }
}
