<?php

namespace NeoP\Database\Pool;

use NeoP\Pool\Pool;
use NeoP\Database\Contract\DatabaseInterface;
use NeoP\Database\Database;
use NeoP\Pool\Contract\PoolOriginInterface;
use NeoP\Pool\Contract\PoolInterface;
use NeoP\Pool\PoolProvider;
use NeoP\Pool\Annotation\Mapping\Pool as PoolMapping;

/**
 * @PoolMapping(DatabaseInterface::class)
 */
class DatabasePool extends Pool implements DatabaseInterface, PoolOriginInterface
{

    public function _create(array $config)
    {
        return new Database(
            $this,
            $config['database'],
        );
    }

    public function get(array $config, string $name): PoolInterface
    {
        if (PoolProvider::hasPool($name)) {
            return PoolProvider::getPool($name);
        } else {
            $instance = $this->_create($config);
            $maxConnect = 5;
            $maxIdle = 5;
            if (isset($config['pool'])) {
                $maxConnect = $config['pool']['max_connect'] ?? 1;
                $maxIdle = $config['pool']['max_idle'] ?? 1;
            }
            $instance->createConnection();
            $this->pool($instance, $maxConnect, $maxIdle);
            PoolProvider::setPool($name, $this);
            return $this;
        }
    }
    
    public function connect(Database $instance): bool
    {
        if (!$instance->isConnect()) {
            $instance->createConnection();
        }
        return true;
    }

    public function release(&$instance): bool
    {
        if ($instance->isConnect()) {
            parent::release($instance);
        }
        return true;
    }
    
}
