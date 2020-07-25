<?php

namespace NeoP\Database;

use NeoP\Database\Exception\DatabaseException;
use Illuminate\Database\Capsule\Manager;

class Database
{
    /**
     * 数据库配置
     * @var array
     */
    protected $_database;

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
     * is connect
     * @var bool
     */
    protected $_isConnect = false;

    function __construct($root, array $database)
    {
        $this->_database = $database;
        $this->_root = $root;
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
                $instance  = new Manager();
                $instance->addConnection($this->_database);
                // $instance->setEventDispatcher(new Dispatcher(new IlluminateContainer));
                // 设置全局静态可访问DB
                $instance->setAsGlobal();
                // 启动Eloquent （如果只使用查询构造器，这个可以注释）
                $instance->bootEloquent();
                $this->_isConnect = true;
                $this->_instance = $instance;
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
}