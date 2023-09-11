<?php

namespace Bx\Tree\Interfaces;

interface ParentNodeInterface extends NodeInterface
{
    /**
     * @return ChildNodeInterface[]
     */
    public function getChildren(): array;

    /**
     * @param  callable $fn - function(NodeInterface $item): bool
     * @return ChildNodeInterface|null
     */
    public function findChild(callable $fn): ?ChildNodeInterface;

    /**
     * @param  string $key
     * @param  mixed  ...$values
     * @return ChildNodeInterface|null
     */
    public function findChildByValue(string $key, ...$values): ?ChildNodeInterface;

    /**
     * @param  callable $fn - function(NodeInterface $item): bool
     * @return ChildNodeInterface[]
     */
    public function filterChildren(callable $fn): array;

    /**
     * @param  callable    $fn
     * @param  string      $mapValue
     * @param  string|null $mapKey
     * @return array
     */
    public function filterChildrenColumn(callable $fn, string $mapValue, ?string $mapKey = null): array;

    /**
     * @param  string $key
     * @param  mixed  ...$values
     * @return ChildNodeInterface[]
     */
    public function filterChildrenByValue(string $key, ...$values): array;

    /**
     * @param  string      $key
     * @param  string      $mapValue
     * @param  string|null $mapKey
     * @param  mixed       ...$values
     * @return array
     */
    public function filterChildrenByValueColumn(
        string $key,
        string $mapValue,
        ?string $mapKey = null,
        ...$values
    ): array;
}
