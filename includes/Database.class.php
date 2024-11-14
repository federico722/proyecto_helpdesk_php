<?php

class Database {
    private $host = 'localhost';
    private $user= 'root';
    private $password = 'admin123';
    private $database = 'sistema_helpdesk';

    public function getConnection(){
        $hostDB = "mysql:host=".$this->host.";dbname=".$this->database.";";

        try{
            $connection = new PDO($hostDB,$this->user,$this->password);
            $connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            return $connection;
        }catch(PDOException $e){
            die("ERROR: ". $e->getMessage());

        }
    }
}

?>

