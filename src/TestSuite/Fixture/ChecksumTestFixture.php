<?php
namespace FriendsOfCake\Fixturize\TestSuite\Fixture;

use Cake\TestSuite\Fixture\TestFixture;
use Cake\Database\Driver\Mysql;
use Cake\Datasource\ConnectionInterface;

/**
 * This class will inspect the database table hash and detect any change to the underlying
 * data set and automatically re-create the table and data
 *
 * If no data has changed, the usual truncate/insert flow is bypassed, increasing the speed
 * of the test suite with heavy fixture usage up significantly.
 *
 */
class ChecksumTestFixture extends TestFixture
{

/**
 * List of table hashes
 *
 * @var array
 */
    public static $_tableHashes = [];

/**
 * Inserts records in the database
 *
 * This will only happen if the underlying table is modified in any way or
 * does not exist with a hash yet.
 *
 * @param ConnectionInterface $db
 * @return boolean
 */
    public function insert(ConnectionInterface $db)
    {
        if ($this->_tableUnmodified($db)) {
            return true;
        }

        $result = parent::insert($db);
        static::$_tableHashes[$this->_getTableKey()] = $this->_hash($db);
        return $result;
    }

/**
 * Deletes all table information.
 *
 * This will only happen if the underlying table is modified in any way
 *
 * @param ConnectionInterface $db
 * @return void
 */
    public function truncate(ConnectionInterface $db)
    {
        if ($this->_tableUnmodified($db)) {
            return true;
        }

        return parent::truncate($db);
    }

/**
 * Drops the table from the test datasource
 *
 * @param ConnectionInterface $db
 * @return void
 */
    public function drop(ConnectionInterface $db)
    {
        unset(static::$_tableHashes[$this->_getTableKey()]);
        return parent::drop($db);
    }

/**
 * Test if a table is modified or not
 *
 * If there is no known hash, treat it as being modified
 *
 * In all other cases where the initial and current hash differs, assume
 * the table has changed
 *
 * @param DboSource $db
 * @return boolean
 */
    protected function _tableUnmodified($db)
    {
        $tableKey = $this->_getTableKey();
        if (!array_key_exists($tableKey, static::$_tableHashes)) {
            return false;
        }

        if (static::$_tableHashes[$tableKey] === $this->_hash($db)) {
            return true;
        }

        return false;
    }

/**
 * Get the table hash from MySQL for a specific table
 *
 * @param ConnectionInterface $db
 * @return string
 */
    protected function _hash(ConnectionInterface $db)
    {
        $driver = $db->getDriver();

        if (!$driver instanceof Mysql) {
            // Have no better idea right now to make it always regenerate the tables
            return microtime();
        }

        $sth = $db->execute("CHECKSUM TABLE " . $this->table . ';');
        $result = $sth->fetch('assoc');
        $checksum = $result['Checksum'];

        // get auto increment count
        $autoIncrement = $this->_getAutoIncrement($db);

        return $checksum . $autoIncrement;
    }

/**
 * Get the table auto increment count
 *
 * @param ConnectionInterface $db
 * @return string
 */
    protected function _getAutoIncrement(ConnectionInterface $db)
    {
        $autoIncrementSth = $db->execute('SELECT `AUTO_INCREMENT` FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=:schema AND TABLE_NAME=:table;', [
            'schema' => $db->config()['database'],
            'table' => $this->table,
        ]);
        $autoIncrementResult = $autoIncrementSth->fetch('assoc');

        return (string) $autoIncrementResult['AUTO_INCREMENT'];
    }

/**
 * Get the key for table hashes
 *
 * @return string key for specify connection and table
 */
    protected function _getTableKey ()
    {
        return $this->connection() . '-' . $this->table;
    }
}
