<?php

    namespace src\db;

    class DBConnector {
        
        private static $db_connection = null;

        private function __construct() {
            
            $dbname = getenv("DB_NAME");
            $username = getenv("DB_USERNAME");
            $password = getenv("DB_PASSWORD");
            $host = getenv("DB_HOST");
            $port = getenv("DB_PORT");

            try {
                self::$db_connection = new \PDO("pgsql:host=$host;dbname=$dbname;", $username, $password);
            } catch (\PDOException $e) {
                self::$db_connection = $e->getMessage();
            }

        }

        public static function get_connection() {
            if(self::$db_connection == null) {
                new DBConnector();   
            }
            return self::$db_connection;
        }

    }

?>