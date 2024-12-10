<?php



use app\App;
use app\lib\Database;
use app\lib\DatabaseConfig;
use app\lib\Page;
use app\router\Response;
use app\router\ResponseCode;
use app\router\Router;
use controllers\create_controller;
use controllers\edit_controller;
use controllers\entity_controller;
use controllers\table_controller;

include_once "app/App.php";
$app = new App();

$config = new DatabaseConfig(
    database_name: "my_database",
    host: "mysql",
    username: "my_user",
    password: "my_password"
);

$db = new Database(config: $config);

Router::get("/", function () use ($app, $db) {
    $page = new class(__DIR__ . "/pages/home.php", $db) extends Page {
        private $db;

        public function __construct($filePath, $db)
        {
            parent::__construct($filePath);
            $this->db = $db;
        }

        public function getBody()
        {
            $tables = $this->db->getAllTables();

            include $this->filePath;
        }
    };
    $page->addStyle("https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css");
    return $page->render();
});


Router::get("/table", function () use ($app, $db) {
    return (new table_controller($db))->render();
})->middleware(function () use ($app, $db) {
    $table_name = $_GET["tablename"] ?? null;
    if ($table_name === null) {
        return Response::error("No table name provided", ResponseCode::BAD_REQUEST);
    }

    if (!$db->tableExists($table_name)) {
        return Response::error("Table not found", ResponseCode::NOT_FOUND);
    }
});


Router::get("/create_page", function () use ($app, $db) {
    return (new create_controller($db))->render();
})->middleware(function () use ($app, $db) {
    $table_name = $_GET["tablename"] ?? null;
    if ($table_name === null) {
        return Response::error("No table name provided", ResponseCode::BAD_REQUEST);
    }

    if (!$db->tableExists($table_name)) {
        return Response::error("Table not found", ResponseCode::NOT_FOUND);
    }
});

Router::get("/edit_page", function () use ($app, $db) {
    return (new edit_controller($db))->render();
})->middleware(function () use ($app, $db) {
    $table_name = $_GET["tablename"] ?? null;
    $id = $_GET["id"] ?? null;
    if ($table_name === null || $id === null) {
        return Response::error("No table name or ID provided", ResponseCode::BAD_REQUEST);
    }

    if (!$db->tableExists($table_name)) {
        return Response::error("Table not found", ResponseCode::NOT_FOUND);
    }

    if (!$db->read($table_name, $id)) {
        return Response::error("Record not found", ResponseCode::NOT_FOUND);
    }
});

Router::post("/create", function () use ($app, $db) {
    return entity_controller::create($db);
});
Router::delete("/delete", function () use ($app, $db) {
    return entity_controller::delete($db);
});
Router::put("/update", function () use ($app, $db) {
    return entity_controller::update($db);
});
$app->run();
