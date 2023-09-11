<?php

namespace Bx\Tree\Tests;

use Bx\Tree\ModelNode;
use Bx\Tree\Tests\Stubs\SimpleModel;
use Bx\Tree\Tests\Stubs\SimpleModelWithArrayAccess;
use PHPUnit\Framework\TestCase;

class ModelNodeTest extends TestCase
{
    public function testGetOriginal()
    {
        $model = new SimpleModel(['one' => 1, 'two' => 2]);
        $node = new ModelNode($model);
        $this->assertEquals($model, $node->getOriginal());
    }

    public function testFindParentByValue()
    {
        $parentModelLv1 = new SimpleModel(['level' => 1]);
        $parentNodeLv1 = new ModelNode($parentModelLv1);

        $parentModelLv2 = new SimpleModel(['level' => 2]);
        $parentNodeLv2 = new ModelNode($parentModelLv2, $parentNodeLv1);

        $model = new SimpleModel(['one' => 1, 'two' => 2]);
        $node = new ModelNode($model, $parentNodeLv2);

        $findResult = $node->findParentByValue('level', 1);
        $this->assertEquals($parentNodeLv1, $findResult);

        $findResult = $node->findParentByValue('level', 2);
        $this->assertEquals($parentNodeLv2, $findResult);
    }

    public function testFilterParentByValue()
    {
        $parentModelLv1 = new SimpleModel(['level' => 1, 'group' => 1]);
        $parentNodeLv1 = new ModelNode($parentModelLv1);

        $parentModelLv2 = new SimpleModel(['level' => 2, 'group' => 2]);
        $parentNodeLv2 = new ModelNode($parentModelLv2, $parentNodeLv1);

        $parentModelLv3 = new SimpleModel(['level' => 3, 'group' => 1]);
        $parentNodeLv3 = new ModelNode($parentModelLv3, $parentNodeLv2);

        $model = new SimpleModel(['one' => 1, 'two' => 2]);
        $node = new ModelNode($model, $parentNodeLv3);

        $filterResult = $node->filterParentByValue('group', 1);
        $this->assertCount(2, $filterResult);
        $this->assertEquals($parentNodeLv3, $filterResult[0]);
        $this->assertEquals($parentNodeLv1, $filterResult[1]);

        $filterResult = $node->filterParentByValue('group', 2);
        $this->assertCount(1, $filterResult);
        $this->assertEquals($parentNodeLv2, $filterResult[0]);
    }

    public function testOffsetSet()
    {
        $model = new SimpleModel(['one' => 1, 'two' => 2]);
        $node = new ModelNode($model);
        $this->assertEquals(1, $node['one']);

        $node->offsetSet('one', 'newValue');
        $this->assertEquals(1, $node['one']);

        $node['one'] = 'otherValue';
        $this->assertEquals(1, $node['one']);

        $modelWithArrayAccess = new SimpleModelWithArrayAccess(['one' => 1, 'two' => 2]);
        $node = new ModelNode($modelWithArrayAccess);
        $this->assertEquals(1, $node['one']);

        $node->offsetSet('one', 'newValue');
        $this->assertEquals('newValue', $node['one']);

        $node['one'] = 'otherValue';
        $this->assertEquals('otherValue', $node['one']);
    }

    public function testFilterChildrenByValue()
    {
        $parentModelLv1 = new SimpleModel(['level' => 1, 'group' => 1]);
        $parentNodeLv1 = new ModelNode($parentModelLv1);

        $parentModelLv2 = new SimpleModel(['level' => 2, 'group' => 2]);
        $parentNodeLv2 = $parentNodeLv1->initChild($parentModelLv2);

        $parentModelLv3 = new SimpleModel(['level' => 3, 'group' => 2]);
        $parentNodeLv3 = $parentNodeLv2->initChild($parentModelLv3);

        $model = new SimpleModel(['one' => 1, 'two' => 2]);
        $node = $parentNodeLv3->initChild($model);

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

    public function testOffsetExists()
    {
        $model = new SimpleModel(['one' => 1, 'two' => 2]);
        $node = new ModelNode($model);
        $this->assertTrue($node->offsetExists('one'));
        $this->assertTrue($node->offsetExists('two'));
        $this->assertFalse($node->offsetExists('tree'));
    }

    public function testInitChild()
    {
        $parentModelLv1 =  new SimpleModel(['level' => 1, 'group' => 1]);
        $parentNodeLv1 = new ModelNode($parentModelLv1);
        $this->assertEmpty($parentNodeLv1->getChildren());

        $model = new SimpleModel(['one' => 1, 'two' => 2]);
        $node = $parentNodeLv1->initChild($model);
        $this->assertInstanceOf(ModelNode::class, $node);
        $this->assertEquals($parentNodeLv1, $node->getParent());

        $this->assertCount(1, $parentNodeLv1->getChildren());
        $this->assertEquals($node, current($parentNodeLv1->getChildren()));
    }

    public function testFindChildByValue()
    {
        $parentModelLv1 = new SimpleModel(['level' => 1, 'group' => 1]);
        $parentNodeLv1 = new ModelNode($parentModelLv1);

        $parentModelLv2 = new SimpleModel(['level' => 2, 'group' => 2]);
        $parentNodeLv2 = new ModelNode($parentModelLv2, $parentNodeLv1);
        $parentNodeLv1->addChild($parentNodeLv2);

        $parentModelLv3 = new SimpleModel(['level' => 3, 'group' => 1]);
        $parentNodeLv3 = new ModelNode($parentModelLv3, $parentNodeLv2);
        $parentNodeLv2->addChild($parentNodeLv3);

        $model = new SimpleModel(['one' => 1, 'two' => 2]);
        $node = new ModelNode($model, $parentNodeLv3);
        $parentNodeLv3->addChild($node);

        $this->assertEquals($parentNodeLv3, $parentNodeLv1->findChildByValue('level', 3));
        $this->assertEquals($parentNodeLv2, $parentNodeLv1->findChildByValue('level', 2));
        $this->assertEquals($node, $parentNodeLv1->findChildByValue('one', 1));
        $this->assertNull($parentNodeLv1->findChildByValue('one', 12));
    }

    public function testOffsetGet()
    {
        $model = new SimpleModel(['one' => 1, 'two' => 2]);
        $node = new ModelNode($model);
        $this->assertEquals(1, $node->offsetGet('one'));
        $this->assertEquals(1, $node['one']);

        $this->assertEquals(2, $node->offsetGet('two'));
        $this->assertEquals(2, $node['two']);
    }

    public function testOffsetUnset()
    {
        $model = new SimpleModel(['one' => 1, 'two' => 2]);
        $node = new ModelNode($model);
        $this->assertEquals(1, $node['one']);
        $this->assertEquals(2, $node['two']);

        $node->offsetUnset('one');
        $this->assertNotNull($node['one'] ?? null);

        unset($node['two']);
        $this->assertNotNull($node['two'] ?? null);

        $modelWithArrayAccess = new SimpleModelWithArrayAccess(['one' => 1, 'two' => 2]);
        $node = new ModelNode($modelWithArrayAccess);
        $this->assertEquals(1, $node['one']);
        $this->assertEquals(2, $node['two']);

        $node->offsetUnset('one');
        $this->assertNull($node['one'] ?? null);

        unset($node['two']);
        $this->assertNull($node['two'] ?? null);
    }

    public function testJsonSerialize()
    {
        $model = new SimpleModel(['one' => 1, 'two' => 2]);
        $node = new ModelNode($model);
        $this->assertEquals(['one' => 1, 'two' => 2, 'children' => []], $node->jsonSerialize());
    }
}
