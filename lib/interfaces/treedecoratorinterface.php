<?php

namespace Bx\Tree\Interfaces;

interface TreeDecoratorInterface
{
    public function fillAndGetUpdatedNode(NodeInterface $node): NodeInterface;
}
