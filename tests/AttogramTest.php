<?php // Attogram Framework - Attogram Test v0.0.2

use PHPUnit\Framework\TestCase;

class AttogramTest extends PHPUnit\Framework\TestCase
{

    protected $attogram;

    public function setUp()
    {
        include_once( __DIR__ . '/../attogram/attogram.php' );
        //$this->attogram = new Attogram\attogram();
    }

    public function testClassExists()
    {
        $this->assertTrue( class_exists('Attogram\attogram'), 'Attogram class not found' );
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testFailingInclude()
    {
        include 'not_existing_file.php';
    }

}
