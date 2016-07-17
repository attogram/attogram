<?php
// Attogram Framework - Attogram Test v0.1.3

namespace Attogram;

class AttogramTest extends PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        include_once __DIR__.'/../Attogram/Attogram.php';
    }

    public function testClassExists()
    {
        $this->assertTrue(class_exists('\Attogram\Attogram'), 'Attogram class not found');
    }
}
