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
        static::$_tableHashes[get_class($this)] = $this->_hash($db);
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
        unset(static::$_tableHashes[get_class($this)]);
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
        $fixtureKey = get_class($this);
        if (empty(static::$_tableHashes[$fixtureKey])) {
            return false;
        }

        if (static::$_tableHashes[$fixtureKey] === $this->_hash($db)) {
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
}
