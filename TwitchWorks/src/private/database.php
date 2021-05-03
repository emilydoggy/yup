<?php
    /*
     * Copyright 2017-2021 HowToCompute. All Rights Reserved
     */
    function DatabaseConnect() {
        $config = parse_ini_file("config.ini", true);

        $host = $config['database']['dbhost'];
        $username = $config['database']['username'];
        $password = $config['database']['password'];
        $database = $config['database']['dbname'];

        try
        {
            $connection = new mysqli($host, $username, $password, $database);

            // Check connection
            if ($connection->connect_error) {
                header("HTTP/1.1 500 Internal Server Error");
                die("Connection failed: " . $connection->connect_error);
            }

            return $connection;
        }
        catch(Exception $ex)
        {
            header("HTTP/1.1 500 Internal Server Error");
            die("Database Communication Error");
        }
    }
?>
