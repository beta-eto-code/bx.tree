<?php

namespace Bx\Tree\Interfaces;

interface ChildNodeInterface extends NodeInterface
{
    /**
     * @return ParentNodeInterface|null
     */
    public function getParent(): ?ParentNodeInterface;

    /**
     * @param  callable $fn - function(NodeInterface $item): bool
     * @return ParentNodeInterface|null
     */
    public function findParent(callable $fn): ?ParentNodeInterface;

    /**
     * @param  string $key
     * @param  mixed  ...$values
     * @return ParentNodeInterface|null
     */
    public function findParentByValue(string $key, ...$values): ?ParentNodeInterface;

    /**
     * @param  callable $fn - function(NodeInterface $item): bool
     * @return ParentNodeInterface[]
     */
    public function filterParent(callable $fn): array;

    /**
     * @param  callable    $fn
     * @param  string      $mapValue
     * @param  string|null $mapKey
     * @return array
     */
    public function filterParentColumn(callable $fn, string $mapValue, ?string $mapKey = null): array;

    /**
     * @return ParentNodeInterface|null
     */
    public function getRoot(): ?ParentNodeInterface;

    /**
     * @param  string $key
     * @param  mixed  ...$values
     * @return ParentNodeInterface[]
     */
    public function filterParentByValue(string $key, ...$values): array;

    /**
     * @param  string      $key
     * @param  string      $mapValue
     * @param  string|null $mapKey
     * @param  mixed       ...$values
     * @return array
     */
    public function filterParentByValueColumn(string $key, string $mapValue, ?string $mapKey = null, ...$values): array;

    /**
     * @return array
     */
    public function getParentList(): array;

    /**
     * @param  string      $mapValue
     * @param  string|null $mapKey
     * @return array
     */
    public function getParentListColumn(string $mapValue, ?string $mapKey = null): array;

    /**
     * @param  ParentNodeInterface $parentNode
     * @return mixed
     */
    public function setParent(ParentNodeInterface $parentNode);
}
