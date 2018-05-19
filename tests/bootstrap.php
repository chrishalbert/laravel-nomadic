<?php

require_once dirname(dirname(__FILE__)) . "/vendor/autoload.php";

use ChrisHalbert\LaravelNomadic\Hooks\NomadicHookInterface;

class TestHookConfig implements NomadicHookInterface
{
    public function execute($name = '', $path = '', $table = null, $create = false, $className = '', $filePath = '')
    {}
}

function config($configValue) {
    $configs = [
        'nomadic.schema' => ['name', 'date'],
        'nomadic.traits' => [
            Illuminate\Support\Traits\CapsuleManagerTrait::class,
            Illuminate\Support\Traits\Macroable::class
        ],
        'nomadic.hooks.preCreate' => [
            function() {}
        ],
        'nomadic.hooks.postCreate' => [
            new TestHookConfig()
        ]
    ];

    return $configs[$configValue];
}
