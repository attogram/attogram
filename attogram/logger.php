<?php // Attogram Framework - logger class v0.0.5

namespace Attogram;

/**
 * Null-ish Stack-ish PSR3 Logger
 */
class logger implements \Psr\Log\LoggerInterface
{

  public $stack;

  public function log( $level, $message, array $context = array() )
  {
    $this->stack[] = "$level: $message" . ( $context ? ': ' . print_r( $context, 1 ) : '');
  }

  public function emergency( $message, array $context = array() )
  {
    $this->log( 'emergency', $message, $context );
  }

  public function alert( $message, array $context = array() )
  {
    $this->log( 'alert', $message, $context );
  }

  public function critical( $message, array $context = array() )
  {
    $this->log( 'critical', $message, $context );
  }

  public function error( $message, array $context = array() )
  {
    $this->log('error', $message, $context );
  }

  public function warning( $message, array $context = array() )
  {
    $this->log( 'warning', $message, $context );
  }

  public function notice( $message, array $context = array() )
  {
    $this->log( 'notice', $message ,$context );
  }

  public function info( $message, array $context = array() )
  {
    $this->log( 'info', $message, $context );
  }

  public function debug( $message, array $context = array() )
  {
    $this->log( 'debug', $message, $context );
  }

} // end class logger
