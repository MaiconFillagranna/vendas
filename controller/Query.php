<?php

require_once 'Singleton.php';

class Query extends Singleton {
    
    const HOST = "127.0.0.1";
    const USERNAME = "root";
    const PASSWORD = "";
    const DB = "seniordatabase";

    private $conn;
    
    protected function __construct() {
        parent::__construct();
        $this->conn = $this->getConn();
    }
   
    /**
     * Pega a Instância única da Conexão
     * @return Query
     */
    public static function getInstance() {
        return parent::getInstance();
    }
    
    private function getConn() {
        $conn = new mysqli(self::HOST, self::USERNAME, self::PASSWORD, self::DB);

        if ($conn->connect_error) {
            die("Erro ao se conectar com o DB: " . $conn->connect_error);
        }
        
        $this->createTablesIfNotExists();
        
        return $conn;
    }
    
    private function createTablesIfNotExists() {
        $this->createProduto();
        $this->createDocumento();
        $this->createItem();
    }
    
    private function createProduto() {
        
    }
    
    private function createDocumento() {
        
    }
    
    private function createItem() {
        
    }
    
    public function getFirstRow($SQL) {
        $result = $this->conn->query($SQL);
        return mysqli_fetch_object($result);
    }
    
    public function getAllRows($SQL) {
        $array = [];
        $result = $this->conn->query($SQL);
        while($objeto = mysqli_fetch_object($result)) {
            $array[] = $objeto;
        }
        return $array;
    }
    
    public function save($SQL) {
        if($this->conn->query($SQL) === TRUE) {
            return true;
        }
        die("Erro ao executar Insert");
    }
    
}
