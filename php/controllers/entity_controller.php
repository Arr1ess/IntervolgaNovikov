<?php

namespace controllers;

use app\lib\Database;
use app\router\Response;
use app\router\ResponseCode;

class entity_controller
{
    /**
     * Метод для создания новой записи в таблице
     * @param Database $db
     * @return Response
     */
    public static function create(Database $db): Response
    {
        // Получаем данные из POST-запроса
        $tableName = $_POST['tablename'] ?? null;
        $data = $_POST;

        // Удаляем лишние данные
        unset($data['tablename']);

        // Проверяем, что таблица и данные существуют
        if (!$tableName || empty($data)) {
            return Response::error("Invalid data", ResponseCode::BAD_REQUEST);
        }

        try {
            // Вставляем данные в таблицу
            $db->create($tableName, $data);
            return new Response(headers: ['Location' => "/table?tablename=$tableName"]);
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), ResponseCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Метод для удаления записи из таблицы
     * @param Database $db
     * @return Response
     */
    public static function delete(Database $db): Response
    {
        // Получаем данные из GET-запроса
        $tableName = $_GET['tablename'] ?? null;
        $id = $_GET['id'] ?? null;

        // Проверяем, что таблица и ID существуют
        if (!$tableName || !$id) {
            return Response::error("Invalid data", ResponseCode::BAD_REQUEST);
        }

        try {
            // Удаляем запись из таблицы
            $db->delete($tableName, $id);
            return new Response();
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), ResponseCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Метод для обновления записи в таблице
     * @param Database $db
     * @return Response
     */
    public static function update(Database $db): Response
    {
        // Получаем данные из PUT-запроса в формате JSON
        $input = file_get_contents('php://input');
        $data = json_decode($input, true); // Декодируем JSON в массив

        // Проверяем, что данные корректно декодированы
        if (json_last_error() !== JSON_ERROR_NONE) {
            return Response::error("Invalid JSON data", ResponseCode::BAD_REQUEST);
        }

        // Извлекаем необходимые данные
        $tableName = $data['tablename'] ?? null;
        $id = $data['id'] ?? null;

        // Удаляем лишние данные
        unset($data['tablename'], $data['id']);

        // Проверяем, что таблица, ID и данные существуют
        if (!$tableName || !$id || empty($data)) {
            return Response::error("Invalid data", ResponseCode::BAD_REQUEST);
        }

        try {
            // Обновляем запись в таблице
            $db->update($tableName, $id, $data);
            return new Response(code: ResponseCode::CREATED);
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), ResponseCode::INTERNAL_SERVER_ERROR);
        }
    }
}
