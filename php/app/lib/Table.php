<?php

namespace app\lib;

use app\interfaces\Storage;

abstract class Table
{
    protected Model $model;


    abstract public function create($model, ...$models): int|string|array|null;

    abstract public function read(int|string $key, ...$keys): Model|array|null;

    abstract public function update(int|string $key, $model): void;

    abstract public function delete(int|string $key, ...$keys): void;

    abstract public function find(array $conditions, ?int $count = null): array|null;

    abstract public function createOrUpdate($model, ...$models): int|string|array|null;
}
