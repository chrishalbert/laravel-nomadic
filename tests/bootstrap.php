<?php

require_once dirname(dirname(__FILE__)) . "/vendor/autoload.php";

use ChrisHalbert\LaravelNomadic\Hooks\NomadicHookInterface;

class TestHookConfig implements NomadicHookInterface
{
    public function execute($name = '', $path = '', $table = null, $create = false, $className = '', $filePath = '')
    {}
}

class ConfigMock
{
    protected static $instance;

    protected static $configs;

    private function __construct()
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
            ]
        ];
    }

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
        self::$instance = new ConfigMock();
    }

    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new ConfigMock();
        }
        return self::$instance;
    }
}

$instance = ConfigMock::instance();

function config($configValue) {

    return ConfigMock::get($configValue);
}
