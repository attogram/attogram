<?php // Attogram Framework - Attogram Test v0.0.1

use PHPUnit\Framework\TestCase;

class AttogramTest extends PHPUnit\Framework\TestCase
{
    protected $attogram_class_file;

    public function setUp()
    {
        $this->attogram_class_file = __DIR__ . '/../attogram/attogram.php';
    }

    public function testInclude()
    {
      include_once( $this->attogram_class_file );

      $this->assertTrue(
          class_exists('Attogram\attogram'),
          'Attogram class not found: file=' . $this->attogram_class_file );
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testFailingInclude()
    {
        include 'not_existing_file.php';
    }

}
