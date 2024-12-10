<?php


namespace app\interfaces;

use app\Interfaces\SavedModel;
use app\lib\Model;

interface Storage
{
    public function read(string|int $id): ?Model;
    public function update(SavedModel $savedModel): void;
    public function delete($id): void;
    public function create(Model $model): null|string|int;
    public function find(...$where): array;
}
