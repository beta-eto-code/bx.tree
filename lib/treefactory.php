<?php

namespace Bx\Tree;

use Bx\Model\Interfaces\CollectionInterface;
use Bx\Model\Interfaces\CollectionItemInterface;
use Bx\Tree\Interfaces\ParentNodeInterface;
use Iterator;

class TreeFactory
{
    /**
     * @param CollectionInterface $collection
     * @param string $parentKey
     * @param string $primaryKey
     * @param CollectionItemInterface|null $defaultRoot
     * @return ParentNodeInterface|null
     */
    public static function createFromCollection(
        CollectionInterface $collection,
        string $parentKey,
        string $primaryKey,
        ?CollectionItemInterface $defaultRoot = null
    ): ?ParentNodeInterface {
        /**
         * @psalm-var Iterator $collection
         * @var       ModelNode[] $map
         */
        $map = static::makeMap(
            $collection,
            $primaryKey,
            function (CollectionItemInterface $item) {
                return new ModelNode($item);
            }
        );

        if (empty($map)) {
            return null;
        }

        $rootList = [];
        foreach ($map as $item) {
            $itemParentValue = $item->getOriginal()->getValueByKey($parentKey);
            if (!empty($itemParentValue)) {
                $parent = $map[$itemParentValue] ?? null;
                if ($parent instanceof ParentNodeInterface) {
                    $parent->addChild($item);
                }
            } else {
                $rootList[] = $item;
            }
        }

        if (empty($rootList)) {
            return null;
        }

        if (count($rootList) > 1 && $defaultRoot != null) {
            $rootNode = new ModelNode($defaultRoot);
            foreach ($rootList as $item) {
                $rootNode->addChild($item);
            }

            return $rootNode;
        }

        return current($rootList);
    }

    /**
     * @param array $dataList
     * @param string $parentKey
     * @param string $primaryKey
     * @param array|null $defaultRoot
     * @return ParentNodeInterface|null
     */
    public static function createFromArray(
        array $dataList,
        string $parentKey,
        string $primaryKey,
        ?array $defaultRoot = null
    ): ?ParentNodeInterface {
        /**
         * @var ArrayNode[] $map
         */
        $map = static::makeMap(
            $dataList,
            $primaryKey,
            function (array $item) {
                return new ArrayNode($item);
            }
        );

        $rootList = [];
        foreach ($map as $item) {
            $itemParentValue = $item->getOriginal()[$parentKey] ?? null;
            if (!empty($itemParentValue)) {
                $parent = $map[$itemParentValue] ?? null;
                if ($parent instanceof ParentNodeInterface) {
                    $parent->addChild($item);
                }
            } else {
                $rootList[] = $item;
            }
        }

        if (count($rootList) > 1 && !empty($defaultRoot)) {
            $root = new ArrayNode($defaultRoot);
            foreach ($rootList as $item) {
                $root->addChild($item);
            }

            return $root;
        }

        $firstElement = current($map);
        if (empty($firstElement)) {
            return null;
        }

        return $firstElement->getRoot() ?? $firstElement;
    }

    /**
     * @param  Iterator|array $list
     * @param  string         $primaryKey
     * @param  callable       $fnMakeNode
     * @return array
     */
    private static function makeMap($list, string $primaryKey, callable $fnMakeNode): array
    {
        $result = [];
        foreach ($list as $item) {
            $key = $item instanceof CollectionItemInterface ?
                $item->getValueByKey($primaryKey) :
                $item[$primaryKey] ?? null;

            if (!empty($key)) {
                $result[$key] = $fnMakeNode($item);
            }
        }

        return $result;
    }
}
