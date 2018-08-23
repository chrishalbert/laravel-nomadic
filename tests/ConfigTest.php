<?php

class ConfigTest extends PHPUnit_Framework_TestCase
{
    public function testConfigHasKeys() {
        $configs = require dirname(__FILE__) . '/../src/nomadic.php';
        $this->assertArrayHasKey('schema', $configs);
        $this->assertArrayHasKey('traits', $configs);
    }
}