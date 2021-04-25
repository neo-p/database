<?php

namespace NeoP\Database;

use NeoP\DI\Container;
use NeoP\Database\Exception\DatabaseException;
use Hyperf\Database\Connection;
use Hyperf\Database\Connectors\ConnectionFactory;

class Database
{
    /**
     * 数据库配置
     * @var array
     */
    protected $_config;

    /**
     * root
     */
    protected $_root;

    /**
     * instance
     * @var Connection
     */
    protected $_instance;

    /**
     * hyperf database connection factory
     * @var ConnectionFactory
     */
    protected $_factory;

    /**
     * is connect
     * @var bool
     */
    protected $_isConnect = false;

    function __construct($root, array $config)
    {
        $this->_config = $config;
        $this->_root = $root;
        $this->_factory = new ConnectionFactory(new Container());
    }

    /**
     * createConnection
     * @return Client
     * @throws DatabaseException
     */
    public function _createConnection()
    {
        if (! $this->_isConnect()) {
            try {
                $this->_isConnect = true;
                $this->_instance = $this->_factory->make($this->_config);
                if ($this->_instance instanceof Connection) {
                    // Reset reconnector after db reconnect.
                    $this->_instance->setReconnector(function ($connection) {
                        if ($connection instanceof Connection) {
                            $this->_refresh($connection);
                        }
                    });
                }
            } catch (\Throwable $e) {
                throw new DatabaseException($e->getMessage(), $e->getCode);
            }
        }
    }

    /**
     * release
     * @return bool
     */
    protected function _release(): bool
    {
        $this->_root->_release($this);
        return true;
    }

    /**
     * connect
     * @return bool
     */
    protected function _connect(): bool
    {
        $this->_root->_connect($this);
        return true;
    }

    /**
     * is connect
     * @return bool
     */
    public function _isConnect(): bool
    {
        return $this->_isConnect;
    }

    public function __call($name, $arguments)
    {
        $this->_connect();
        $result = $this->_instance->$name(...$arguments);
        $this->_release();
        return $result;
    }

    /**
     * Refresh pdo and readPdo for current connection.
     */
    protected function _refresh(Connection $connection)
    {
        $refresh = $this->factory->make($this->config);
        if ($refresh instanceof Connection) {
            $connection->disconnect();
            $connection->setPdo($refresh->getPdo());
            $connection->setReadPdo($refresh->getReadPdo());
        }
    }
}