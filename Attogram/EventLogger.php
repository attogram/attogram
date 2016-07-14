<?php
// Attogram Framework - EventLogger class v0.1.3

namespace Attogram;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

class EventLogger extends AbstractProcessingHandler
{
    private $database;

    /**
     * start the event Logger.
     *
     * @param object $database Attogram Database object
     * @param string $level    (optional) Minimum reporting level, Defaults to debug
     * @param bool   $bubble   (optional) Bubble up, Defaults to true
     */
    public function __construct($database, $level = Logger::DEBUG, $bubble = true)
    {
        $this->database = $database;
        parent::__construct($level, $bubble);
    }

    /**
     * write an event to the Database.
     *
     * @param array $record
     */
    protected function write(array $record)
    {
        $this->database->queryb(
            'INSERT INTO event (channel,level,message,time) VALUES (:channel,:level,:message,:time)',
            array(
                'channel' => $record['channel'],
                'level' => $record['level'],
                'message' => $record['formatted'],
                'time' => $record['datetime']->format('U')
            )
        );
    }

} // end class class EventLogger
