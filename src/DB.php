<?php
namespace NeoP\Database;

use NeoP\Pool\PoolProvider;
use NeoP\Database\Exception\DatabaseException;

class DB
{
    public static function pool(string $name = 'db.pool')
    {
        if (! PoolProvider::hasPool($name)) {
            throw new DatabaseException("Pool [" . $name . "] is not exists...");
        }
        return PoolProvider::getPool($name);
    }
}