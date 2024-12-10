<?php

namespace controllers;

use app\lib\Database;
use app\lib\Page;
use app\router\Response;
use app\router\ResponseCode;

class edit_controller extends Page
{
    public function __construct(protected Database $db)
    {
        parent::__construct(__DIR__ . "/../pages/edit.php");
    }

    public function getBody()
    {
        $tablename = $_GET["tablename"];
        $table_columns = $this->db->getTableColumns($tablename);

        $foriegignKeys = $this->db->getForeignKeys($tablename);
        foreach ($foriegignKeys as $value) {
            $dependedColumns[$value['COLUMN_NAME']] = $this->db->getColumnValues($value["REFERENCED_TABLE_NAME"], $value["REFERENCED_COLUMN_NAME"]);
        }

        $row = $this->db->find($tablename, id: $_GET["id"]);
        // var_dump($row);

        $enumValues = array();
        foreach ($table_columns as $column) {
            if ($column['Field'] == 'type' && strpos($column['Type'], 'enum') === 0) {
                $valuesString = substr($column['Type'], 5, -1);
                $values = explode("','", $valuesString);
                $enumValues = array_map(function ($value) {
                    return str_replace("'", "", $value);
                }, $values);
                break;
            }
        }



        $this->addStyle("https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css");
        include_once $this->filePath;
    }
}
