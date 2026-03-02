<?php

class Database
{
    private $connection;

    public function __construct()
    {
        $this->connection = new PDO(
            'mysql:host=localhost;dbname=test',
            'root',
            ''
        );

        echo "Database connection established.\n";
    }

    public function query($sql)
    {
        return $this->connection->query($sql);
    }
}