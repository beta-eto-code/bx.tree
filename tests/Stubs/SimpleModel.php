<?php

namespace Bx\Tree\Tests\Stubs;

use Bx\Model\Interfaces\CollectionItemInterface;

class SimpleModel implements CollectionItemInterface
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function assertValueByKey(string $key, $value): bool
    {
        return $this->hasValueKey($key) && $this->getValueByKey($key) === $value;
    }

    public function hasValueKey(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    public function getValueByKey(string $key)
    {
        return $this->data[$key] ?? null;
    }

    public function jsonSerialize()
    {
        return $this->data;
    }
}
