<?php

namespace app\lib;

use Exception;
use PDO;
use PDOException;

class Database
{
    private $conn;

    public function __construct(DatabaseConfig $config)
    {
        $this->conn = $config->connect();
    }
    public function getTableColumns(string $tableName): array
    {
        try {
            $query = "SHOW COLUMNS FROM $tableName";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error getting columns from table: " . $e->getMessage());
        }
    }
    public function getColumnValues(string $tableName, string $columnName): array
    {
        try {
            // Проверяем, существует ли таблица
            if (!$this->tableExists($tableName)) {
                throw new Exception("Таблица '$tableName' не существует.");
            }

            // Проверяем, существует ли столбец в таблице
            if (!$this->columnExists($tableName, $columnName)) {
                throw new Exception("Столбец '$columnName' не существует в таблице '$tableName'.");
            }

            // Формируем и выполняем запрос
            $query = "SELECT $columnName FROM $tableName";
            $stmt = $this->conn->query($query);

            // Возвращаем массив значений колонки
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {
            throw new Exception("Ошибка при получении значений колонки: " . $e->getMessage());
        }
    }

    public function getDependentColumnValues(string $tableName, string $columnName): array
    {
        try {
            $foreignKeys = $this->getForeignKeys($tableName);

            foreach ($foreignKeys as $foreignKey) {
                if ($foreignKey['COLUMN_NAME'] == $columnName) {
                    $referencedTable = $foreignKey['REFERENCED_TABLE_NAME'];
                    $referencedColumn = $foreignKey['REFERENCED_COLUMN_NAME'];

                    if (!$this->columnExists($referencedTable, $referencedColumn)) {
                        throw new Exception("Связанная таблица или столбец не существуют.");
                    }

                    $query = "SELECT $referencedColumn FROM $referencedTable";
                    $stmt = $this->conn->query($query);
                    return $stmt->fetchAll(PDO::FETCH_COLUMN);
                }
            }

            return [];
        } catch (Exception $e) {
            throw new Exception("Ошибка при получении зависимых значений: " . $e->getMessage());
        }
    }

    public function getForeignKeys(string $tableName): array
    {
        try {
            $query = "
                SELECT 
                    COLUMN_NAME, 
                    REFERENCED_TABLE_NAME, 
                    REFERENCED_COLUMN_NAME 
                FROM 
                    INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE 
                    TABLE_NAME = :tableName 
                    AND REFERENCED_TABLE_NAME IS NOT NULL
            ";

            $stmt = $this->conn->prepare($query);
            $stmt->execute([':tableName' => $tableName]);

            return $stmt->fetchAll();
        } catch (Exception $e) {
            throw new Exception("Ошибка при получении внешних ключей: " . $e->getMessage());
        }
    }

    public function columnExists(string $tableName, string $columnName): bool
    {
        try {
            $query = "SELECT 1 FROM information_schema.columns WHERE table_name = :tableName AND column_name = :columnName";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':tableName' => $tableName, ':columnName' => $columnName]);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            throw new Exception("Ошибка при проверке существования столбца: " . $e->getMessage());
        }
    }
    public function create(string $tableName, array $data): null|int|string
    {
        $columnNames = implode(", ", array_keys($data));
        $params = array_map(function ($value): string {
            return ":$value";
        }, array_keys($data));
        try {
            $query = "INSERT INTO $tableName ($columnNames) VALUES (" . implode(", ", $params) . ")";
            $stmt = $this->conn->prepare($query);
            foreach ($data as $column => $value) {
                $stmt->bindValue(":$column", $value);
            }
            $stmt->execute();
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            throw new \Exception("Error creating record in table: " . $e->getMessage());
        }
    }

    public function delete(string $tableName, string|int $key): void
    {
        try {
            $query = "DELETE FROM $tableName WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $key);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new \Exception("Error deleting record from table: " . $e->getMessage());
        }
    }

    public function update(string $tableName, string|int $key, array $data): void
    {
        try {
            $query = "UPDATE $tableName SET ";
            $params = [];
            foreach ($data as $column => $value) {
                $params[] = "$column = :$column";
            }
            $query .= implode(", ", $params);
            $query .= " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $key);
            foreach ($data as $column => $value) {
                $stmt->bindValue(":$column", $value);
            }
            $stmt->execute();
        } catch (PDOException $e) {
            throw new \Exception("Error updating record in table: " . $e->getMessage());
        }
    }

    public function get(string $tableName, string|int $key): ?array
    {
        try {
            $query = "SELECT * FROM $tableName WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $key);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            throw new \Exception("Error reading record from table: " . $e->getMessage());
        }
    }

    public function find(string $tableName, ...$where): array
    {
        try {
            $query = "SELECT * FROM $tableName";
            if (!empty($where)) {
                $conditions = [];
                foreach ($where as $key => $value) {
                    $conditions[] = "$key = :$key";
                }
                $query .= " WHERE " . implode(" AND ", $conditions);
            }
            $stmt = $this->conn->prepare($query);
            foreach ($where as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new \Exception("Error finding records in table: " . $e->getMessage());
        }
    }

    public function read(string $tableName, int|string $id): ?array
    {
        try {
            $query = "SELECT * FROM $tableName WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            throw new \Exception("Error reading record from table: " . $e->getMessage());
        }
    }

    public function getAllTables(): array
    {
        try {
            $query = "SHOW TABLES";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            throw new \Exception("Error getting list of tables: " . $e->getMessage());
        }
    }

    public function tableExists(string $tableName): bool
    {
        try {
            $query = "SHOW TABLES LIKE '$tableName'";
            $stmt = $this->conn->query($query);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new \Exception("Error checking if table exists: " . $e->getMessage());
        }
    }

    public function createOrUpdate(string $tableName, array $data): int|string|null
    {
        try {
            // Проверяем, существует ли запись с таким ID
            $existingRecord = $this->get($tableName, $data['id']);

            if ($existingRecord) {
                // Обновляем существующую запись
                $this->update($tableName, $data['id'], $data);
                return $data['id'];
            } else {
                // Создаем новую запись
                return $this->create($tableName, $data);
            }
        } catch (PDOException $e) {
            throw new \Exception("Error creating or updating record in table: " . $e->getMessage());
        }
    }
}
