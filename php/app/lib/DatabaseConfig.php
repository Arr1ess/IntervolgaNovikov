<?php

namespace app\lib;

use Exception;
use PDO;
use PDOException;


class DatabaseConfig
{
	private ?PDO $pdo = null;

	public function __construct(
		private string $database_name,
		private string $host,
		private string $password,
		private string $username,
		private string $charset = 'utf8mb4',
		private array $options = []
	) {}

	public function connect(): PDO
	{
		if ($this->pdo === null) {
			$dsn = "mysql:host={$this->host};dbname={$this->database_name};charset={$this->charset}";
			$default_options = [
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_EMULATE_PREPARES => false,
				PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
			];
			$options = array_replace($default_options, $this->options);

			try {
				$this->pdo = new PDO($dsn, $this->username, $this->password, $options);
			} catch (PDOException $e) {
				throw new Exception("Database connection failed: " . $e->getMessage());
			}
		}
		return $this->pdo;
	}
	public function close(): void
	{
		$this->pdo = null;
	}
}
