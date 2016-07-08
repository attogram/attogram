<?php // Attogram Framework - Attogram Test v0.0.1

use PHPUnit\Framework\TestCase;

class AttogramTest extends PHPUnit\Framework\TestCase
{


    public function testDebug()
    {
        // Assert
        $this->assertEquals( 1, 1 );
    }

    public function testPushAndPop()
    {
        $stack = [];
        $this->assertEquals(0, count($stack));

        array_push($stack, 'foo');
        $this->assertEquals('foo', $stack[count($stack)-1]);
        $this->assertEquals(1, count($stack));

        $this->assertEquals('foo', array_pop($stack));
        $this->assertEquals(0, count($stack));
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testFailingInclude()
    {
        include 'not_existing_file.php';
    }

}
