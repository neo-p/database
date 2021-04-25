<?php

namespace NeoP\Database\Model;
use NeoP\Database\DB;
use ReflectionClass;

class Model
{
    static $table;
    static $pool = 'db.pool';
    static $prefix;

    public static function __callStatic($name, $arguments)
    {
        $reflect = new ReflectionClass(get_called_class());
        $instance = $reflect->newInstance();
        if ($reflect->hasMethod($name)) {
            $result = $instance->$name(...$arguments);
        } else {
            $result = DB::pool($instance->pool)->table($instance::$table)->$name(...$arguments);
        }
        return $result;
    }

    public function __call($name, $arguments)
    {
        $reflect = new ReflectionClass($this);
        $instance = $reflect->newInstance();
        if ($reflect->hasMethod($name)) {
            $result = $instance->$name(...$arguments);
        } else {
            $result = DB::pool($instance::$pool)->table($instance::$table)->$name(...$arguments);
        }
        return $result;
    }

    public static function _new()
    {
        $reflect = new ReflectionClass(get_called_class());
        $instance = $reflect->newInstance();
        return $instance;
    }
}