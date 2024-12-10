<?php

namespace app\lib;

use ArrayAccess;
use Stringable;

abstract class Model implements Stringable, ArrayAccess
{
    protected array $params = [];
    // должен заполнять $params
    abstract protected function defineFields(): void;

    public function __construct(array $params = [], ...$args)
    {
        $this->defineFields();
        $this->update($params + $args);
    }

    public function clear(): void
    {
        $this->params = [];
        $this->defineFields();
    }
    public function prepare(): void
    {
        array_walk($this->params, fn(&$value) => htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8'));
    }

    public function validate(): bool
    {
        foreach ($this->params as $key => $value) {
            if ($value === null) return false;
        }
        return true;
    }

    public function __toString(): string
    {
        return json_encode($this->params, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT |
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

    public function __get(string $name): mixed
    {
        if (array_key_exists($name, $this->params)) {
            return $this->params[$name];
        }
        return null;
    }

    public function __set(string $name, mixed $value): void
    {
        if (array_key_exists($name, $this->params)) {
            $this->params[$name] = $value;
        }
    }

    protected static function createFields(...$params): array
    {
        return array_combine($params, array_fill(0, count($params), null));
    }

    public function toArray(): array
    {
        return $this->params;
    }

    public function update(array $data = [], ...$args): void
    {
        $data += $args;
        $filteredParameters = array_intersect_key($data, $this->params);
        $this->params = array_merge($this->params, $filteredParameters);
    }

    public function hasField(string $name): bool
    {
        return array_key_exists($name, $this->params);
    }

    public function unsetField(string $name): void
    {
        if (array_key_exists($name, $this->params)) {
            unset($this->params[$name]);
        }
    }

    public function clone(): self
    {
        return new static($this->params);
    }

    public function equals(Model $other): bool
    {
        return $this->params === $other->params;
    }

    public function getFields(): array
    {
        return array_keys($this->params);
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->hasField($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->__get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->__set($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->unsetField($offset);
    }
    public static function createFromData(array $data): self
    {
        $instance = new static();
        $instance->update($data);
        return $instance;
    }
    public function newInstanceFromData(array $data): self
    {
        $newInstance = clone $this;
        $newInstance->update($data);
        return $newInstance;
    }
}
