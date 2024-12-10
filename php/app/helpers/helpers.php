<?php

use app\controllers\Page;

function includeAllFilesFromFolder($folderPath)
{
    // Проверяем, существует ли папка
    if (!is_dir($folderPath)) {
        trigger_error("Папка '$folderPath' не существует.", E_USER_WARNING);
        return;
    }

    // Ищем все PHP-файлы в папке
    $files = glob($folderPath . '/*.php');

    // Подключаем каждый файл
    foreach ($files as $file) {
        if (is_file($file)) {
            include_once $file;
        }
    }
}

// function scanAndInitPlugins()
// {
//     $pluginsDir = SERVER_NAME . "/plugins";
//     if (!is_dir($pluginsDir)) {
//         echo "Директория с плагинами не найдена.";
//         return;
//     }
//     $pluginFolders = scandir($pluginsDir);

//     foreach ($pluginFolders as $folder) {
//         if ($folder === '.' || $folder === '..') {
//             continue;
//         }
//         $pluginFilePath = $pluginsDir . "/$folder/$folder.php";
//         if (file_exists($pluginFilePath)) {
//             require_once $pluginFilePath;
//             $pluginClassName = "plugins\\$folder\\$folder";
//             if (class_exists($pluginClassName) && in_array('app\interfaces\IPlugin', class_implements($pluginClassName))) {
//                 $pluginClassName::init();
//                 // echo "Подключен $pluginClassName <br/>";
//             }
//         }
//     }
// }


function includeAllRoutes()
{
    includeAllFilesFromFolder(__DIR__ . "/../../routes");
}





