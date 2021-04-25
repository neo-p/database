<?php

namespace NeoP\Database\Annotation\Handler;

use NeoP\DI\Container;
use NeoP\Annotation\Annotation\Handler\Handler;
use NeoP\Annotation\Annotation\Mapping\AnnotationHandler;
use NeoP\Database\Annotation\Mapping\Model;
use NeoP\Annotation\Entity\AnnotationProperty;
use NeoP\Database\Exception\DatabaseException;
use NeoP\Database\Model\Model as ModelCall;
use NeoP\Pool\PoolProvider;
use ReflectionClass;

/**
 * @AnnotationHandler(Model::class)
 */
class ModelHandler extends Handler
{
    public function handle(Model $annotation, ReflectionClass &$reflection)
    {
        $pool = $annotation->getPool();
        $prefix = $annotation->getPrefix();
        $table = $annotation->getTable();
        if (! PoolProvider::hasPool($pool)) {
            throw new DatabaseException("Pool [" . $pool . "] is not exists...");
        }
        if (!$table) {
            $table = strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $reflection->getShortName()));
            $table = $prefix . $table;
        }
        $reflection->setStaticPropertyValue("table", $table);
        $reflection->setStaticPropertyValue("prefix", $prefix);
        $reflection->setStaticPropertyValue("pool", $pool);
        unset($reflection);
    }
}