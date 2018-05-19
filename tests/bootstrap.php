<?php

require_once dirname(dirname(__FILE__)) . "/vendor/autoload.php";


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
            function() {}
        ]
    ];

    return $configs[$configValue];
}
