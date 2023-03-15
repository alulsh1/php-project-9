<?php

namespace PostgreSQLTutorial;

/**
 * —оздание класса Connection
 */
final class Connection
{
    /**
     * Connection
     * тип @var
     */
    private static ?Connection $conn = null;

    /**
     * ѕодключение к базе данных и возврат экземпл€ра объекта \PDO
     * @return \PDO
     * @throws \Exception
     */
    public function connect()
    {
        if (getenv("DATABASE_URL")) {
            $databaseUrl = parse_url(getenv("DATABASE_URL"));
        }

        if (isset($databaseUrl["host"])) {
            // необходимо провер€ть произвольное поле,
            // потому что по умолчанию запишет в $databaseUrl почти пустой массив
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

        // подключение к базе данных postgresql
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
     * возврат экземпл€ра объекта Connection
     * тип @return
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
