<?php

namespace Bx\Tree;

use Bx\Tree\Interfaces\ChildNodeInterface;
use Bx\Tree\Interfaces\NodeInterface;
use Bx\Tree\Interfaces\ParentNodeInterface;
use ArrayAccess;

class ArrayNode extends BaseNode implements ArrayAccess
{
    public function __construct(array $data, ?ParentNodeInterface $parentNode = null)
    {
        $this->originalItem = $data;
        $this->parent = $parentNode;
    }

    public function initChild(array $data): ArrayNode
    {
        $node = new ArrayNode($data, $this);
        $this->children[] = $node;

        return $node;
    }

    /**
     * @return array
     */
    public function getOriginal(): array
    {
        return parent::getOriginal();
    }

    /**
     * @param  string $key
     * @param  mixed  ...$values
     * @return ParentNodeInterface|null
     */
    public function findParentByValue(string $key, ...$values): ?ParentNodeInterface
    {
        if (empty($values)) {
            return null;
        }

        return $this->findParent(
            function (NodeInterface $node) use ($key, $values) {
                $data = $node->getOriginal();
                $value = $data[$key] ?? null;

                return !is_null($value) && in_array($value, $values);
            }
        );
    }

    /**
     * @param  string $key
     * @param  mixed  ...$values
     * @return ParentNodeInterface[]
     */
    public function filterParentByValue(string $key, ...$values): array
    {
        if (empty($values)) {
            return [];
        }

        return $this->filterParent(
            function (NodeInterface $node) use ($key, $values) {
                $data = $node->getOriginal();
                $value = $data[$key] ?? null;

                return !is_null($value) && in_array($value, $values);
            }
        );
    }

    /**
     * @param  string $key
     * @param  mixed  ...$values
     * @return ChildNodeInterface|null
     */
    public function findChildByValue(string $key, ...$values): ?ChildNodeInterface
    {
        if (empty($values)) {
            return null;
        }

        return $this->findChild(
            function (NodeInterface $node) use ($key, $values) {
                $data = $node->getOriginal();
                $value = $data[$key] ?? null;

                return !is_null($value) && in_array($value, $values);
            }
        );
    }

    /**
     * @param  string $key
     * @param  mixed  ...$values
     * @return ChildNodeInterface[]
     */
    public function filterChildrenByValue(string $key, ...$values): array
    {
        if (empty($values)) {
            return [];
        }

        return $this->filterChildren(
            function (NodeInterface $node) use ($key, $values) {
                $data = $node->getOriginal();
                $value = $data[$key] ?? null;

                return !is_null($value) && in_array($value, $values);
            }
        );
    }

    /**
     * @param  mixed $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->originalItem[$offset]);
    }

    /**
     * @param  mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->originalItem[$offset] ?? null;
    }

    /**
     * @param  mixed $offset
     * @param  mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->originalItem[$offset] = $value;
    }

    /**
     * @param  mixed $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->originalItem[$offset]);
    }

    /**
     * @param  array       $nodeList
     * @param  string      $mapValue
     * @param  string|null $mapKey
     * @return array
     */
    protected function getMapFromList(array $nodeList, string $mapValue, ?string $mapKey = null): array
    {
        $result = [];
        foreach ($nodeList as $item) {
            $value = $item[$mapValue] ?? null;
            $key = !empty($mapKey) ? ($item[$mapKey] ?? null) : null;
            if (!empty($key)) {
                $result[$key] = $value;
            } else {
                $result[] = $value;
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $data = $this->originalItem;
        $childrenData = [];
        foreach ($this->getChildren() as $child) {
            $childrenData[] = $child->jsonSerialize();
        }

        $data['children'] = $childrenData;

        return $data;
    }
}
