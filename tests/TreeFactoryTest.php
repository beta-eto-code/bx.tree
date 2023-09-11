<?php

namespace Bx\Tree\Tests;

use Bx\Model\Collection;
use Bx\Tree\Tests\Stubs\SimpleModel;
use Bx\Tree\TreeFactory;
use PHPUnit\Framework\TestCase;

class TreeFactoryTest extends TestCase
{

    public function testCreateFromCollection()
    {
        $rootModel = new SimpleModel(['level' => 0, 'id' => 10]);
        $parentModel1 = new SimpleModel(['level' => 1, 'id' => 1, 'parentId' => 10]);
        $parentModel2 = new SimpleModel(['level' => 1, 'id' => 3, 'parentId' => 10]);
        $childModel1 = new SimpleModel(['level' => 2, 'id' => 2, 'parentId' => 1]);
        $childModel2 = new SimpleModel(['level' => 3, 'id' => 4, 'parentId' => 2]);
        $collection = new Collection($rootModel, $parentModel1, $parentModel2, $childModel1, $childModel2);
        $tree = TreeFactory::createFromCollection($collection, 'parentId', 'id');

        $this->assertEquals($tree->getOriginal(), $rootModel);
        $this->assertCount(2, $tree->getChildren());

        $childrenListLv1 = $tree->getChildren();
        $child1Lv1 = $childrenListLv1[0];
        $this->assertEquals($child1Lv1->getOriginal(), $parentModel1);
        $this->assertEquals($child1Lv1->getParent()->getOriginal(), $rootModel);

        $child2Lv1 = $childrenListLv1[1];
        $this->assertEquals($child2Lv1->getOriginal(), $parentModel2);
        $this->assertEquals($child2Lv1->getParent()->getOriginal(), $rootModel);

        $this->assertCount(1, $child1Lv1->getChildren());
        $this->assertEmpty($child2Lv1->getChildren());
    }

    public function testCreateFromArray()
    {
        $rootElement = ['level' => 0, 'id' => 10];
        $parentElement1 = ['level' => 1, 'id' => 1, 'parentId' => 10];
        $parentElement2 = ['level' => 1, 'id' => 3, 'parentId' => 10];
        $childElement1 = ['level' => 2, 'id' => 2, 'parentId' => 1];
        $childElement2 = ['level' => 3, 'id' => 4, 'parentId' => 2];
        $data = [
            $rootElement,
            $parentElement1,
            $parentElement2,
            $childElement1,
            $childElement2
        ];

        $tree = TreeFactory::createFromArray($data, 'parentId', 'id');
        $this->assertEquals($tree->getOriginal(), $rootElement);
        $this->assertCount(2, $tree->getChildren());

        $childrenListLv1 = $tree->getChildren();
        $child1Lv1 = $childrenListLv1[0];
        $this->assertEquals($child1Lv1->getOriginal(), $parentElement1);
        $this->assertEquals($child1Lv1->getParent()->getOriginal(), $rootElement);

        $child2Lv1 = $childrenListLv1[1];
        $this->assertEquals($child2Lv1->getOriginal(), $parentElement2);
        $this->assertEquals($child2Lv1->getParent()->getOriginal(), $rootElement);

        $this->assertCount(1, $child1Lv1->getChildren());
        $this->assertEmpty($child2Lv1->getChildren());
    }
}
