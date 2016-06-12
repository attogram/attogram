<?php // Attogram Framework - logger v0.0.1

namespace Attogram;

/**
 * Nullish Stackish PSR3ish Logger
 */
class logger
{
  public $stack;
  public function log( string $level, string $message, array $context = array() ) {
    $this->stack[] = "$level: $message" . ( $context ? ': ' . print_r($context,1) : '');
  }
  public function emergency( string $message, array $context = array()) { $this->log('emergency',$message,$context); }
  public function alert(     string $message, array $context = array()) { $this->log('alert',    $message,$context); }
  public function critical(  string $message, array $context = array()) { $this->log('critical', $message,$context); }
  public function error(     string $message, array $context = array()) { $this->log('error',    $message,$context); }
  public function warning(   string $message, array $context = array()) { $this->log('warning',  $message,$context); }
  public function notice(    string $message, array $context = array()) { $this->log('notice',   $message,$context); }
  public function info(      string $message, array $context = array()) { $this->log('info',     $message,$context); }
  public function debug(     string $message, array $context = array()) { $this->log('debug',    $message,$context); }
} // end class logger
