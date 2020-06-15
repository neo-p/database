<?php

namespace NeoP\Database\Annotation\Handler;

use NeoP\DI\Container;
use NeoP\Annotation\Annotation\Handler\Handler;
use NeoP\Annotation\Annotation\Mapping\AnnotationHandler;
use NeoP\Database\Annotation\Mapping\Database;
use NeoP\Annotation\Entity\AnnotationProperty;
use NeoP\Database\Exception\DatabaseException;
use NeoP\Pool\PoolProvider;

/**
 * @AnnotationHandler(Database::class)
 */
class DatabaseHandler extends Handler
{
    public function handle(Database $annotation, AnnotationProperty &$reflection)
    {
        $name = $annotation->getConfig();
        if (! PoolProvider::hasPool($name)) {
            throw new DatabaseException("Pool [" . $name . "] is not exists...");
        }
        $reflection->getReflectionProperty()->setAccessible(true);
        $reflection->getReflectionProperty()->setValue(
            Container::getDefinition($this->className),
            PoolProvider::getPool($name));
        unset($reflection);
    }
}