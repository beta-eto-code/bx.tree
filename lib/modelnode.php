<?php

namespace Bx\Tree;

use ArrayAccess;
use Bx\Model\Interfaces\CollectionItemInterface;
use Bx\Tree\Interfaces\ChildNodeInterface;
use Bx\Tree\Interfaces\NodeInterface;
use Bx\Tree\Interfaces\ParentNodeInterface;
use JsonSerializable;

class ModelNode extends BaseNode implements ArrayAccess
{
    public function __construct(CollectionItemInterface $item, ?ParentNodeInterface $parentNode = null)
    {
        $this->originalItem = $item;
        $this->parent = $parentNode;
    }

    /**
     * @param  CollectionItemInterface $item
     * @return ModelNode
     */
    public function initChild(CollectionItemInterface $item): ModelNode
    {
        $node = new ModelNode($item, $this);
        $this->children[] = $node;

        return $node;
    }

    /**
     * @return CollectionItemInterface
     */
    public function getOriginal(): CollectionItemInterface
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
                /**
                 * @var CollectionItemInterface $item
                 */
                $item = $node->getOriginal();
                $value = $item->getValueByKey($key);

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
                /**
                 * @var CollectionItemInterface $item
                 */
                $item = $node->getOriginal();
                $value = $item->getValueByKey($key);

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
                /**
                 * @var CollectionItemInterface $item
                 */
                $item = $node->getOriginal();
                $value = $item->getValueByKey($key);

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
                /**
                 * @var CollectionItemInterface $item
                 */
                $item = $node->getOriginal();
                $value = $item->getValueByKey($key);

                return !is_null($value) && in_array($value, $values);
            }
        );
    }

    /**
     * @param  string $name
     * @param  array  $args
     * @return mixed
     */
    public function __call(string $name, array $args)
    {
        if (method_exists($this->originalItem, $name)) {
            return $this->originalItem->{$name}(...$args);
        }

        return null;
    }

    /**
     * @param  mixed $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->originalItem->hasValueKey($offset);
    }

    /**
     * @param  mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->originalItem->getValueByKey($offset);
    }

    /**
     * @param  mixed $offset
     * @param  mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if ($this->originalItem instanceof ArrayAccess) {
            $this->originalItem->offsetSet($offset, $value);
        }
    }

    /**
     * @param  mixed $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        if ($this->originalItem instanceof ArrayAccess) {
            $this->originalItem->offsetUnset($offset);
        }
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
            /**
             * @var CollectionItemInterface $item
             */
            $value = $item->getValueByKey($mapValue);
            $key = !empty($mapKey) ? $item->getValueByKey($mapKey) : null;
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
        $data = $this->originalItem instanceof JsonSerializable ? $this->originalItem->jsonSerialize() : [];
        $childrenData = [];
        foreach ($this->getChildren() as $child) {
            $childrenData[] = $child->jsonSerialize();
        }

        $data['children'] = $childrenData;

        return $data;
    }
}
