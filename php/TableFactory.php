<?php

use app\lib\Database;
use app\lib\Model;

class TableFactory
{
    public static function getTable(string $tablename, Database $db)
    {
        switch ($tablename) {
            case 'clients':
                return new dbTable($db, new class extends Model {
                    protected function defineFields(): void
                    {
                        $this->params = ['id', 'name', 'email', 'password'];
                    }
                }, 'clients');
            default:
                throw new \Exception("Table $tablename not found");
        }
    }
}
