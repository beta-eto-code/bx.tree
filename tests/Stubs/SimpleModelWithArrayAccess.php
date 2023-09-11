<?php

namespace Bx\Tree\Tests\Stubs;

use ArrayAccess;
use Bx\Tree\Tests\Stubs\SimpleModel;

class SimpleModelWithArrayAccess extends SimpleModel implements ArrayAccess
{
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->data);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset] ?: null;
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }
}