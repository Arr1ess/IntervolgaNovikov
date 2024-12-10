<?php

namespace controllers;

use app\lib\Database;
use app\lib\Page;
use app\router\Response;
use app\router\ResponseCode;

class table_controller extends Page
{
    public function __construct(protected Database $db)
    {
        parent::__construct(__DIR__ . "/../pages/table.php");
    }

    public function getBody()
    {
        $tablename = $_GET["tablename"];
        $table_column = $this->db->getTableColumns($tablename);
        $table_rows = $this->db->find($tablename);


        $this->addStyle("https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css");
        include_once $this->filePath;
    }
}
