<?php

namespace Bx\Tree;

use Bx\Tree\Interfaces\ChildNodeInterface;
use Bx\Tree\Interfaces\DecoratorInterface;
use Bx\Tree\Interfaces\ParentNodeInterface;

abstract class BaseNode implements ParentNodeInterface, ChildNodeInterface, DecoratorInterface
{
    /**
     * @var mixed
     */
    protected $originalItem;
    /**
     * @var ParentNodeInterface|null
     */
    protected $parent;
    /**
     * @var ChildNodeInterface[]
     */
    protected $children = [];

    /**
     * @param  array       $nodeList
     * @param  string      $mapValue
     * @param  string|null $mapKey
     * @return array
     */
    abstract protected function getMapFromList(array $nodeList, string $mapValue, ?string $mapKey = null): array;

    /**
     * @param          ChildNodeInterface $item
     * @return         void
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function addChild(ChildNodeInterface $item)
    {
        $item->setParent($this);
        $this->children[] = $item;
    }

    /**
     * @param  ParentNodeInterface $parentNode
     * @return void
     */
    public function setParent(ParentNodeInterface $parentNode)
    {
        $this->parent = $parentNode;
    }

    /**
     * @return ParentNodeInterface|null
     */
    public function getParent(): ?ParentNodeInterface
    {
        return $this->parent;
    }

    /**
     * @param  callable $fn - function(NodeInterface $item): bool
     * @return ParentNodeInterface|null
     */
    public function findParent(callable $fn): ?ParentNodeInterface
    {
        if (empty($this->parent)) {
            return null;
        }

        $item = $this;
        while ($item instanceof ChildNodeInterface && $item = $item->getParent()) {
            if ($fn($item)) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param  callable $fn - function(NodeInterface $item): bool
     * @return ParentNodeInterface[]
     */
    public function filterParent(callable $fn): array
    {
        if (empty($this->parent)) {
            return [];
        }

        $result = [];
        $item = $this;
        while ($item instanceof ChildNodeInterface && $item = $item->getParent()) {
            if ($fn($item)) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * @return ChildNodeInterface[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param  callable $fn - function(NodeInterface $item): bool
     * @return ChildNodeInterface|null
     */
    public function findChild(callable $fn): ?ChildNodeInterface
    {
        foreach ($this->getChildren() as $child) {
            if ($fn($child) === true) {
                return $child;
            }

            if ($child instanceof ParentNodeInterface) {
                $result = $child->findChild($fn);
                if ($result instanceof ChildNodeInterface) {
                    return $result;
                }
            }
        }

        return null;
    }

    /**
     * @param  callable $fn - function(NodeInterface $item): bool
     * @return ChildNodeInterface[]
     */
    public function filterChildren(callable $fn): array
    {
        $list = [];
        foreach ($this->getChildren() as $child) {
            if ($fn($child) === true) {
                $list[] = $child;
            }

            if ($child instanceof ParentNodeInterface) {
                $result = $child->filterChildren($fn);
                if ($result) {
                    $list = array_merge($list, $result);
                }
            }
        }

        return $list;
    }

    /**
     * @return mixed
     */
    public function getOriginal()
    {
        return $this->originalItem;
    }

    /**
     * @return ParentNodeInterface|null
     */
    public function getRoot(): ?ParentNodeInterface
    {
        $result = $this->getParent();
        if (empty($result)) {
            return null;
        }

        while ($result instanceof ChildNodeInterface && $result->getParent()) {
            $result = $result->getParent();
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getParentList(): array
    {
        if (empty($this->parent)) {
            return [];
        }

        $result = [];
        $item = $this;
        while ($item instanceof ChildNodeInterface && $item = $item->getParent()) {
            $result[] = $item;
        }

        return $result;
    }

    /**
     * @param  callable    $fn
     * @param  string      $mapValue
     * @param  string|null $mapKey
     * @return array
     */
    public function filterParentColumn(callable $fn, string $mapValue, ?string $mapKey = null): array
    {
        return $this->getMapFromList($this->filterParent($fn), $mapValue, $mapKey);
    }

    /**
     * @param  string      $key
     * @param  string      $mapValue
     * @param  string|null $mapKey
     * @param  mixed       ...$values
     * @return array
     */
    public function filterParentByValueColumn(string $key, string $mapValue, ?string $mapKey = null, ...$values): array
    {
        return $this->getMapFromList($this->filterParentByValue($key, ...$values), $mapValue, $mapKey);
    }

    /**
     * @param  string      $mapValue
     * @param  string|null $mapKey
     * @return array
     */
    public function getParentListColumn(string $mapValue, ?string $mapKey = null): array
    {
        return $this->getMapFromList($this->getParentList(), $mapValue, $mapKey);
    }

    /**
     * @param  callable $fn
     * @param  string   $childrenKey
     * @return array
     */
    public function map(callable $fn, string $childrenKey = 'children'): array
    {
        $result = $fn($this);
        $childrenData = [];
        foreach ($this->getChildren() as $child) {
            $childrenData[] = $child->map($fn);
        }

        $result[$childrenKey] = $childrenData;

        return $result;
    }

    /**
     * @param  callable    $fn
     * @param  string      $mapValue
     * @param  string|null $mapKey
     * @return array
     */
    public function filterChildrenColumn(callable $fn, string $mapValue, ?string $mapKey = null): array
    {
        return $this->getMapFromList($this->filterChildren($fn), $mapValue, $mapKey);
    }

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
    ): array {
        return $this->getMapFromList($this->filterChildrenByValue($key, ...$values), $mapValue, $mapKey);
    }
}
