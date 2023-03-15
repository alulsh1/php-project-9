<?php

namespace PostgreSQLTutorial;

/**
 * �������� ������ Connection
 */
final class Connection
{
    /**
     * Connection
     * ��� @var
     */
    private static ?Connection $conn = null;

    /**
     * ����������� � ���� ������ � ������� ���������� ������� \PDO
     * @return \PDO
     * @throws \Exception
     */
    public function connect()
    {
        if (getenv("DATABASE_URL")) {
            $databaseUrl = parse_url(getenv("DATABASE_URL"));
        }

        if (isset($databaseUrl["host"])) {
            // ���������� ��������� ������������ ����,
            // ������ ��� �� ��������� ������� � $databaseUrl ����� ������ ������
            $params["host"] = $databaseUrl["host"];
            $params["port"] = isset($databaseUrl["port"])
                ? $databaseUrl["port"]
                : null;
            $params["database"] = isset($databaseUrl["path"])
                ? ltrim($databaseUrl["path"], "/")
                : null;
            $params["user"] = isset($databaseUrl["user"])
                ? $databaseUrl["user"]
                : null;
            $params["password"] = isset($databaseUrl["pass"])
                ? $databaseUrl["pass"]
                : null;
        } else {
            $params = parse_ini_file("database.ini");
        }
        if ($params === false) {
            throw new \Exception("Error reading database configuration file");
        }

        // ����������� � ���� ������ postgresql
        $conStr = sprintf(
            "pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s",
            $params["host"],
            $params["port"],
            $params["database"],
            $params["user"],
            $params["password"]
        );

        $pdo = new \PDO($conStr);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }

    /**
     * ������� ���������� ������� Connection
     * ��� @return
     */
    public static function get()
    {
        if (null === static::$conn) {
            static::$conn = new self();
        }

        return static::$conn;
    }

    protected function __construct()
    {
    }
}
