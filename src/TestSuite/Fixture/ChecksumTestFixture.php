<?php
declare(strict_types=1);

namespace FriendsOfCake\Fixturize\TestSuite\Fixture;

use Cake\Database\Driver\Mysql;
use Cake\Datasource\ConnectionInterface;
use Cake\TestSuite\Fixture\TestFixture;

/**
 * This class will inspect the database table hash and detect any change to the underlying
 * data set and automatically re-create the table and data
 *
 * If no data has changed, the usual truncate/insert flow is bypassed, increasing the speed
 * of the test suite with heavy fixture usage up significantly.
 */
class ChecksumTestFixture extends TestFixture
{
    /**
     * List of table hashes
     *
     * @var array<string, string>
     */
    protected static $_tableHashes = [];

    /**
     * Inserts records in the database
     *
     * This will only happen if the underlying table is modified in any way or
     * does not exist with a hash yet.
     *
     * @param \Cake\Datasource\ConnectionInterface $db An instance of the connection
     *   into which the records will be inserted.
     * @return \Cake\Database\StatementInterface|bool on success or if there are no records to insert,
     *  or false on failure.
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
     * @param \Cake\Datasource\ConnectionInterface $db A reference to a db instance
     * @return bool
     */
    public function truncate(ConnectionInterface $db): bool
    {
        if ($this->_tableUnmodified($db)) {
            return true;
        }

        return parent::truncate($db);
    }

    /**
     * Drops the table from the test datasource
     *
     * @param \Cake\Datasource\ConnectionInterface $db An instance of the connection the fixture should be removed from.
     * @return bool True on success, false on failure.
     */
    public function drop(ConnectionInterface $db): bool
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
     * @param \Cake\Datasource\ConnectionInterface $db A reference to a db instance
     * @return bool
     */
    protected function _tableUnmodified(ConnectionInterface $db): bool
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
     * @param \Cake\Datasource\ConnectionInterface $db A reference to a db instance
     * @return string
     */
    protected function _hash(ConnectionInterface $db): string
    {
        $driver = $db->getDriver();

        if ($driver instanceof Mysql) {
            $sth = $db->execute('CHECKSUM TABLE ' . $this->table);

            return $sth->fetchColumn(1);
        }

        // Have no better idea right now to make it always regenerate the tables
        return microtime();
    }

    /**
     * Get the key for table hashes
     *
     * @return string key for specify connection and table
     */
    protected function _getTableKey(): string
    {
        return $this->connection() . '-' . $this->table;
    }
}
