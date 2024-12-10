<?php



use app\lib\Database;
use app\lib\Model;
use app\lib\Table;

class dbTable extends Table
{
    public function __construct(protected Database $db, protected Model $model, protected string $tableName) {}

    public function create($model, ...$models): array|int|string|null
    {
        if (!empty($models)) {
            $answer = [];
            foreach ($models as $model) {
                $answer[] = $this->db->create($this->tableName, $model->toArray());
            }
            $answer[] = $this->db->create($this->tableName, $model->toArray());
            return $answer;
        }
        return $this->db->create($this->tableName, $model->toArray());
    }

    public function read(int|string $key, ...$keys): Model|array|null
    {
        if (!empty($keys)) {
            $answer = [];
            foreach ($keys as $key) {
                $answer[] = $this->model->newInstanceFromData($this->db->read($this->tableName, $key));
            }
            $answer[] = $this->model->newInstanceFromData($this->db->read($this->tableName, $key));
            return $answer;
        }
        return $this->model->newInstanceFromData($this->db->read($this->tableName, $key));
    }

    public function update(int|string $key, $model): void
    {
        $this->db->update($this->tableName, $key, $model->toArray());
    }

    public function delete(int|string $key, ...$keys): void
    {
        if (!empty($keys)) {
            foreach ($keys as $key) {
                $this->db->delete($this->tableName, $key);
            }
        }
        $this->db->delete($this->tableName, $key);
    }

    public function find(array $conditions, ?int $count = null): array|null
    {
        return $this->db->find($this->tableName, $conditions, $count);
    }

    public function createOrUpdate($model, ...$models): int|string|array|null
    {
        if (!empty($models)) {
            $answer = [];
            foreach ($models as $model) {
                $answer[] = $this->db->createOrUpdate($this->tableName, $model->toArray());
            }
            $answer[] = $this->db->createOrUpdate($this->tableName, $model->toArray());
            return $answer;
        }
        return $this->db->createOrUpdate($this->tableName, $model->toArray());
    }
}
