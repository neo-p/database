<?php

namespace NeoP\Database\Annotation\Mapping;

use NeoP\Annotation\Annotation\Mapping\AnnotationMappingInterface;

use function annotationBind;

/** 
 * @Annotation 
 * @Target("CLASS")
 * @Attributes({
 *     @Attribute("table", type="string"),
 *     @Attribute("prefix", type="string"),
 *     @Attribute("pool", type="string"),
 * })
 *
 */
final class Model implements AnnotationMappingInterface
{
    private $table = '';
    private $prefix = '';
    private $pool = 'db.pool';
    
    function __construct($params)
    {
        annotationBind($this, $params, 'setTable');
    }

    public function setTable(string $table = ''): void
    {
        $this->table = $table;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function setPrefix(string $prefix = ''): void
    {
        $this->prefix = $prefix;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function setPool(string $pool = 'db.pool'): void
    {
        $this->pool = $pool;
    }

    public function getPool(): string
    {
        return $this->pool;
    }
}