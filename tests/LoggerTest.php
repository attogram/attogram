<?php // Attogram Framework - Logger Test v0.0.2

use PHPUnit\Framework\TestCase;

class LoggerTest extends PHPUnit\Framework\TestCase
{

    protected $logger;

    public function setUp()
    {
        include_once( __DIR__ . '/../attogram/logger.php' );
        $this->logger = new Attogram\logger();
    }

    public function testClassExists()
    {
        $this->assertTrue(
          class_exists('Attogram\logger'),
          'Attogram\logger class not found'
        );
    }

    public function testInterface()
    {
        $this->assertTrue(
          ( array( 'Psr\Log\LoggerInterface'=>'Psr\Log\LoggerInterface' ) == class_implements( $this->logger ) ),
          'class logger does NOT implement Psr\Log\LoggerInterface'
        );
    }
}
