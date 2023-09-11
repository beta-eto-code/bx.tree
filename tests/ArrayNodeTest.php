<?php

namespace Bx\Tree\Tests;

use Bx\Tree\ArrayNode;
use PHPUnit\Framework\TestCase;

class ArrayNodeTest extends TestCase
{
    public function testGetOriginal()
    {
        $data = ['one' => 1, 'two' => 2];
        $node = new ArrayNode($data);
        $this->assertEquals($data, $node->getOriginal());
    }

    public function testFindParentByValue()
    {
        $parentDataLv1 = ['level' => 1];
        $parentNodeLv1 = new ArrayNode($parentDataLv1);

        $parentDataLv2 = ['level' => 2];
        $parentNodeLv2 = new ArrayNode($parentDataLv2, $parentNodeLv1);

        $data = ['one' => 1, 'two' => 2];
        $node = new ArrayNode($data, $parentNodeLv2);

        $findResult = $node->findParentByValue('level', 1);
        $this->assertEquals($parentNodeLv1, $findResult);

        $findResult = $node->findParentByValue('level', 2);
        $this->assertEquals($parentNodeLv2, $findResult);
    }

    public function testFilterParentByValue()
    {
        $parentDataLv1 = ['level' => 1, 'group' => 1];
        $parentNodeLv1 = new ArrayNode($parentDataLv1);

        $parentDataLv2 = ['level' => 2, 'group' => 2];
        $parentNodeLv2 = new ArrayNode($parentDataLv2, $parentNodeLv1);

        $parentDataLv3 = ['level' => 3, 'group' => 1];
        $parentNodeLv3 = new ArrayNode($parentDataLv3, $parentNodeLv2);

        $data = ['one' => 1, 'two' => 2];
        $node = new ArrayNode($data, $parentNodeLv3);

        $filterResult = $node->filterParentByValue('group', 1);
        $this->assertCount(2, $filterResult);
        $this->assertEquals($parentNodeLv3, $filterResult[0]);
        $this->assertEquals($parentNodeLv1, $filterResult[1]);

        $filterResult = $node->filterParentByValue('level', 2);
        $this->assertCount(1, $filterResult);
        $this->assertEquals($parentNodeLv2, $filterResult[0]);
    }

    public function testFindChildByValue()
    {
        $parentDataLv1 = ['level' => 1, 'group' => 1];
        $parentNodeLv1 = new ArrayNode($parentDataLv1);

        $parentDataLv2 = ['level' => 2, 'group' => 2];
        $parentNodeLv2 = new ArrayNode($parentDataLv2, $parentNodeLv1);
        $parentNodeLv1->addChild($parentNodeLv2);

        $parentDataLv3 = ['level' => 3, 'group' => 1];
        $parentNodeLv3 = new ArrayNode($parentDataLv3, $parentNodeLv2);
        $parentNodeLv2->addChild($parentNodeLv3);

        $data = ['one' => 1, 'two' => 2];
        $node = new ArrayNode($data, $parentNodeLv3);
        $parentNodeLv3->addChild($node);

        $this->assertEquals($parentNodeLv3, $parentNodeLv1->findChildByValue('level', 3));
        $this->assertEquals($parentNodeLv2, $parentNodeLv1->findChildByValue('level', 2));
        $this->assertEquals($node, $parentNodeLv1->findChildByValue('one', 1));
        $this->assertNull($parentNodeLv1->findChildByValue('one', 12));
    }

    public function testOffsetExists()
    {
        $data = ['one' => 1, 'two' => 2];
        $node = new ArrayNode($data);
        $this->assertTrue($node->offsetExists('one'));
        $this->assertTrue($node->offsetExists('two'));
        $this->assertFalse($node->offsetExists('tree'));
    }

    public function testJsonSerialize()
    {
        $data = ['one' => 1, 'two' => 2];
        $node = new ArrayNode($data);
        $this->assertEquals(['one' => 1, 'two' => 2, 'children' => []], $node->jsonSerialize());
    }

    public function testOffsetSet()
    {
        $data = ['one' => 1, 'two' => 2];
        $node = new ArrayNode($data);
        $this->assertEquals(1, $node['one']);

        $node->offsetSet('one', 'newValue');
        $this->assertEquals('newValue', $node['one']);

        $node['one'] = 'otherValue';
        $this->assertEquals('otherValue', $node['one']);
    }

    public function testInitChild()
    {
        $parentDataLv1 = ['level' => 1, 'group' => 1];
        $parentNodeLv1 = new ArrayNode($parentDataLv1);
        $this->assertEmpty($parentNodeLv1->getChildren());

        $data = ['one' => 1, 'two' => 2];
        $node = $parentNodeLv1->initChild($data);
        $this->assertInstanceOf(ArrayNode::class, $node);
        $this->assertEquals($parentNodeLv1, $node->getParent());

        $this->assertCount(1, $parentNodeLv1->getChildren());
        $this->assertEquals($node, current($parentNodeLv1->getChildren()));
    }

    public function testOffsetUnset()
    {
        $data = ['one' => 1, 'two' => 2];
        $node = new ArrayNode($data);
        $this->assertEquals(1, $node['one']);
        $this->assertEquals(2, $node['two']);

        $node->offsetUnset('one');
        $this->assertNull($node['one'] ?? null);

        unset($node['two']);
        $this->assertNull($node['two'] ?? null);
    }

    public function testFilterChildrenByValue()
    {
        $parentDataLv1 = ['level' => 1, 'group' => 1];
        $parentNodeLv1 = new ArrayNode($parentDataLv1);

        $parentDataLv2 = ['level' => 2, 'group' => 2];
        $parentNodeLv2 = $parentNodeLv1->initChild($parentDataLv2);

        $parentDataLv3 = ['level' => 3, 'group' => 2];
        $parentNodeLv3 = $parentNodeLv2->initChild($parentDataLv3);

        $data = ['one' => 1, 'two' => 2];
        $node = $parentNodeLv3->initChild($data);

        $filterResult = $parentNodeLv1->filterChildrenByValue('group', 2);
        $this->assertCount(2, $filterResult);
        $this->assertEquals($parentNodeLv2, $filterResult[0]);
        $this->assertEquals($parentNodeLv3, $filterResult[1]);

        $filterResult = $parentNodeLv1->filterChildrenByValue('one', 1);
        $this->assertCount(1, $filterResult);
        $this->assertEquals($node, $filterResult[0]);

        $filterResult = $parentNodeLv1->filterChildrenByValue('one', 12);
        $this->assertEmpty($filterResult);
    }

    public function testOffsetGet()
    {
        $data = ['one' => 1, 'two' => 2];
        $node = new ArrayNode($data);
        $this->assertEquals(1, $node->offsetGet('one'));
        $this->assertEquals(1, $node['one']);

        $this->assertEquals(2, $node->offsetGet('two'));
        $this->assertEquals(2, $node['two']);
    }
}
