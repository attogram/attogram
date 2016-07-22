<?php
// Attogram Framework - Attogram Test v0.1.8

class AttogramTest extends PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        include_once __DIR__.'/../Attogram/Attogram.php';
        include_once __DIR__.'/../Attogram/AttogramDatabaseInterface.php';
        include_once __DIR__.'/../Attogram/EventLogger.php';
        include_once __DIR__.'/../Attogram/NullDatabase.php';
    }

    public function testInterfaceExists()
    {
        $this->assertTrue(
            interface_exists('\Attogram\AttogramDatabaseInterface'),
            'AttogramDatabaseInterface class not found'
        );
    }

    public function testClassExists()
    {
        $this->assertTrue(
            class_exists('\Attogram\Attogram'),
            'Attogram class not found'
        );
        $this->assertTrue(
            class_exists('\Attogram\EventLogger'),
            'EventLogger class not found'
        );
        $this->assertTrue(
            class_exists('\Attogram\NullDatabase'),
            'NullDatabase class not found'
        );
    }

    public function testNullDatabase()
    {
        $nullDatabase = new \Attogram\NullDatabase;
        $this->assertFalse(
            $nullDatabase->initDB(),
            'NullDatabase initDB failed'
        );
        $this->assertEquals(
            $nullDatabase->query('vacuum'),
            array()
        );
        $this->assertEquals(
            $nullDatabase->query('vacuum', array('test'=>'test')),
            array()
        );
        $this->assertFalse(
            $nullDatabase->queryb('vacuum'),
            'NullDatabase queryb failed'
        );
        $this->assertFalse(
            $nullDatabase->queryb('vacuum', array('test'=>'test')),
            'NullDatabase queryb with bind failed'
        );
        $this->assertEquals(
            $nullDatabase->getTableCount('tableName'),
            0
        );
        $this->assertFalse(
            $nullDatabase->createTable('tableName'),
            'NullDatabase createTable failed'
        );
        //$test = $nullDatabase->tabler();
        //$test = $nullDatabase->pager();
        $this->assertEquals(
            $nullDatabase->getSetLimitAndOffset(),
            array(0, 0)
        );
    }
}
