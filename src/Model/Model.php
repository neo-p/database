<?php

namespace NeoP\Database\Model;
use NeoP\Database\DB;
use ReflectionClass;

class Model
{
    protected $table;
    protected $pool = 'db.pool';
    protected $prefix = '';

    public static function __callStatic($name, $arguments)
    {
        $reflect = new ReflectionClass(get_called_class());
        $instance = $reflect->newInstance();
        if (!$instance->table) {
            $table = strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $reflect->getShortName()));
            $instance->table = $instance->prefix . $table;
        }
        if ($reflect->hasMethod($name)) {
            $result = $instance->$name(...$arguments);
        } else {
            $result = DB::pool($instance->pool)->table($instance->table)->$name(...$arguments);
        }
        return $result;
    }

    public function __call($name, $arguments)
    {
        $reflect = new ReflectionClass($this);
        $instance = $reflect->newInstance();
        if (!$instance->table) {
            $table = strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $reflect->getShortName()));
            $instance->table = $instance->prefix . $table;
        }
        if ($reflect->hasMethod($name)) {
            $result = $instance->$name(...$arguments);
        } else {
            $result = DB::pool($instance->pool)->table($instance->table)->$name(...$arguments);
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