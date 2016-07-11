<?php
// Attogram Framework - Attogram Test v0.1.0

class AttogramTest extends PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        include_once __DIR__.'/../attogram/attogram.php';
    }

    public function testClassExists()
    {
        $this->assertTrue(class_exists('Attogram\attogram'), 'Attogram\attogram class not found');
    }
}
