<?php // Attogram Framework - logger class v0.1.0

namespace Attogram;

/**
 * Null PSR-3 Logger
 */
class logger implements \Psr\Log\LoggerInterface
{

  public function log( $level, $message, array $context = array() )
  {
  }

  public function emergency( $message, array $context = array() )
  {
  }

  public function alert( $message, array $context = array() )
  {
  }

  public function critical( $message, array $context = array() )
  {
  }

  public function error( $message, array $context = array() )
  {
  }

  public function warning( $message, array $context = array() )
  {
  }

  public function notice( $message, array $context = array() )
  {
  }

  public function info( $message, array $context = array() )
  {
  }

  public function debug( $message, array $context = array() )
  {
  }

} // end class logger
