<?php

namespace NeoP\Database;

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
    public function createConnection()
    {
        if (! $this->isConnect()) {
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
    protected function release(): bool
    {
        $this->_root->release($this);
        return true;
    }

    /**
     * connect
     * @return bool
     */
    protected function connect(): bool
    {
        $this->_root->connect($this);
        return true;
    }

    /**
     * is connect
     * @return bool
     */
    public function isConnect(): bool
    {
        return $this->_isConnect;
    }

    public function __call($name, $arguments)
    {
        $this->connect();
        $result = $this->_instance->$name(...$arguments);
        $this->release();
        return $result;
    }
}