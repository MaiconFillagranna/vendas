<?php

require_once 'Singleton.php';

class Connection extends Singleton {
    
    /**
     * Pega a Instância única da Conexão
     * @return Connection
     */
    public static function getInstance() {
        return parent::getInstance();
    }
    
    public function getConn() {

        $conn = new mysqli(null, null, null, "databasesenior");
        
        $sql = "create table testeloco1(codigo integer not null)";
        $result = $conn->query($sql);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 
        echo "Connected successfully";
    }
    
}
