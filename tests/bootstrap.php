<?php

require_once dirname(dirname(__FILE__)) . "/vendor/autoload.php";

use ChrisHalbert\LaravelNomadic\Hooks\NomadicHookInterface;

class TestHookConfig implements NomadicHookInterface
{
    public function execute(
        string $name = '',
        string $path = '',
        string $table = null,
        $create = false,
        string $className = '',
        string $filePath = ''
    ) {}
}

abstract class FunctionMock
{
    protected static $instance;
    protected static $configs = [];

    public static function set($key, $value)
    {
        self::$configs[$key] = $value;
    }

    public static function get($key)
    {
        return self::$configs[$key];
    }

    public static function reset()
    {
        self::$instance = new static();
    }

    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }
}

class ConfigMock extends FunctionMock
{
    protected function __construct()
    {
        static::$configs = [
            'nomadic.schema' => ['name', 'date'],
            'nomadic.traits' => [
                Illuminate\Support\Traits\CapsuleManagerTrait::class,
                Illuminate\Support\Traits\Macroable::class
            ],
            'nomadic.hooks.preCreate' => [
                function () {
                }
            ],
            'nomadic.hooks.postCreate' => [
                new TestHookConfig()
            ],
            'nomadic.hooks' => [
                'preCreate' => [],
                'postCreate' => [],
                'construct' => [],
                'preMigrate' => [],
                'postMigrate' => [],
                'preRollback' => [],
                'postRollback' => [],
                'destruct' => []
            ],
            'nomadic.stub.path' => '',
            'nomadic.stub.variables' => [
                'fileDocs' => <<<FILEDOCS
/**
 * File documentation.
 */            
FILEDOCS
                , 'classDocs' => <<<CLASSDOCS
/**
 * Class docs.
 */            
CLASSDOCS
                , 'traitDocs' => <<<TRAITDOCS
    /**
     * Trait docs. 
     */            
TRAITDOCS
                , 'additionalProperties' => <<<ADDITIONALPROPS
    /**
     * A property.
     * @var string
     */
    protected \$property;
ADDITIONALPROPS
                , 'migrateTemplate' => <<<MIGRATETEMPLATE
        // Migration notes
MIGRATETEMPLATE
                , 'rollbackTemplate' => <<<ROLLBACKTEMPLATE
        // Rollback notes
ROLLBACKTEMPLATE
                , 'additionalMethods' => <<<ADDITIONALMETHODS
    protected function foo() {}
ADDITIONALMETHODS
            ]
        ];
    }
}

$instance = ConfigMock::instance();

function config($configValue) {
    return ConfigMock::get($configValue);
}

class AppMock extends FunctionMock {}


function app($appValue) {
    return AppMock::get($appValue);
}
