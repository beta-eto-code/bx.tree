<?php

namespace Bx\Tree\Interfaces;

use JsonSerializable;

interface NodeInterface extends JsonSerializable
{
    /**
     * @return mixed
     */
    public function getOriginal();

    /**
     * @param  callable $fn          - function(NodeInterface $node): array
     * @param  string   $childrenKey
     * @return array
     */
    public function map(callable $fn, string $childrenKey = 'children'): array;
}
