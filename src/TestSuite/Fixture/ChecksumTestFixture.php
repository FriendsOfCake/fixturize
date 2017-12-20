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

        $this->truncate($db);

        $this->_disableForeignKeys($db);

        $result = parent::insert($db);

        $this->_enableForeignKeys($db);

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

        $this->_disableForeignKeys($db);

        $result = parent::truncate($db);

        $this->_enableForeignKeys($db);

        return $result;
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

        $this->_disableForeignKeys($db);
        $this->dropConstraints($db);

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
        if (empty(static::$_tableHashes[$tableKey])) {
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
        return $checksum;
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

/**
 * Disable foreign key
 *
 * @param ConnectionInterface $db
 */
    protected function _disableForeignKeys(ConnectionInterface $db)
    {
        if (method_exists($db, 'disableForeignKeys')) {
            $db->disableForeignKeys();
        }
    }

/**
 * Enable foreign key
 *
 * @param ConnectionInterface $db
 */
    protected function _enableForeignKeys(ConnectionInterface $db)
    {
        if (method_exists($db, 'enableForeignKeys')) {
            $db->enableForeignKeys();
        }
    }
}
