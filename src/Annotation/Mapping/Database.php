<?php

namespace NeoP\Database\Annotation\Mapping;

use NeoP\Annotation\Annotation\Mapping\AnnotationMappingInterface;

use function annotationBind;

/** 
 * @Annotation 
 * @Target("PROPERTY")
 * @Attributes({
 *     @Attribute("config", type="string")
 * })
 *
 */
final class Database implements AnnotationMappingInterface
{
    private $config = 'db.pool';
    
    function __construct($params)
    {
        annotationBind($this, $params, 'setConfig');
    }

    public function setConfig(string $config = 'db.pool'): void
    {
        $this->config = $config;
    }

    public function getConfig(): string
    {
        return $this->config;
    }
}