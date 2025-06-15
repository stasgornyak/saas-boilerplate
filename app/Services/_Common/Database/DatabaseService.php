<?php

namespace App\Services\_Common\Database;

use Illuminate\Support\Facades\DB;

class DatabaseService
{
    public static function deleteDatabase(string $dbName): bool
    {
        $query = "DROP DATABASE IF EXISTS `$dbName`;";

        return DB::statement($query);
    }

    public static function deleteUser(string $userName): bool
    {
        $query1 = "DROP USER IF EXISTS '$userName'@'localhost';";
        $query2 = "DROP USER IF EXISTS '$userName'@'127.0.0.1';";

        $res1 = DB::statement($query1);
        $res2 = DB::statement($query2);

        return $res1 && $res2;
    }

    public static function databaseExists(string $dbName): bool
    {
        $query = 'SELECT `SCHEMA_NAME` FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?';
        $db = DB::select($query, [$dbName]);

        return ! empty($db);
    }

    public static function userExists(string $userName): bool
    {
        $query = 'SELECT `user` FROM mysql.user WHERE user = ?';
        $users = DB::select($query, [$userName]);

        return (bool) count($users);
    }

    public static function createDatabase(string $dbName): bool
    {
        $charset = config('database.connections.mysql.charset', 'utf8mb4');
        $collation = config('database.connections.mysql.collation', 'utf8mb4_unicode_ci');

        $query = "CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET $charset COLLATE $collation;";

        return DB::statement($query);
    }

    public static function createUser(string $dbName, string $userName, string $userPass): bool
    {
        $query1 = "CREATE USER '$userName'@'localhost' IDENTIFIED BY '$userPass';";
        $query2 = "GRANT ALL PRIVILEGES ON `$dbName`.* TO '$userName'@'localhost';";

        $res1 = DB::statement($query1);
        $res2 = DB::statement($query2);

        return $res1 && $res2;
    }
}
