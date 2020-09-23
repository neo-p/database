<?php

namespace NeoP\Database;

use NeoP\Database\Contract\DatabaseInterface;
use NeoP\Database\Exception\DatabaseException;
use NeoP\Database\Query\QueryBuilder;
use NeoP\Pool\Contract\PoolInterface;
use NeoP\Pool\Annotation\Mapping\Pool as PoolMapping;
use NeoP\Pool\PoolProvider;

use Swoole\Database\PDOPool;
use Swoole\Database\PDOConfig;

/**
 * @PoolMapping(DatabaseInterface::class)
 */
class Database implements DatabaseInterface, PoolInterface
{
    /**
     * 数据库配置
     * @var array
     */
    protected $_config = [
        'driver' => PDOConfig::DRIVER_MYSQL,
        'port' => 3306,
        'database' => 'neo-p',
        'username' => 'root',
        'password' => 'root',
        'charset' => 'utf8mb4',
        'unixSocket' => null,
        'options' => [],
        'size' => 64,
    ];

    /**
     * pool
     * @var bool
     */
    protected $_pool;

    /**
     * createConnection
     * @return Client
     * @throws DatabaseException
     */
    public function _createPool(array $config, string $name)
    {
        if (! $this->_pool) {
            $this->_config = array_replace_recursive($this->_config, $config);

            $this->_pool = new PDOPool(
                (new PDOConfig())
                    ->withDriver($this->_config['driver'])
                    ->withHost($this->_config['host'])
                    ->withPort($this->_config['port'])
                    ->withUnixSocket($this->_config['unixSocket'])
                    ->withDbName($this->_config['database'])
                    ->withCharset($this->_config['charset'])
                    ->withUsername($this->_config['username'])
                    ->withPassword($this->_config['password'])
                    ->withOptions($this->_config['options']),
                $this->_config['size']
            );
            
            PoolProvider::setPool($name, $this);
        }
        return $this;
    }

    /**
     * release
     * @return bool
     */
    public function release($instance): bool
    {
        $this->_pool->put($instance);
        return true;
    }

    /**
     * connect
     * @return bool
     */
    public function getConnect()
    {
        return $this->_pool->get();
    }

    public function __call($name, $arguments)
    {
        return QueryBuilder::getInstance($this)->$name(...$arguments);
    }
}