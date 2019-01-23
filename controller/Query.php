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
        $this->createTablesIfNotExists();
    }
   
    /**
     * Pega a Instância única da Query
     * @return Query
     */
    public static function getInstance() {
        return parent::getInstance();
    }
    
    private function getConn() {
        $conn = new mysqli(self::HOST, self::USERNAME, self::PASSWORD);

        if ($conn->connect_error) {
            die("Erro ao se conectar com o MySQL: " . $conn->connect_error);
        }
        
        $connDB = $this->getDataBaseConnection($conn);
        
        return $connDB;
    }
    
    private function createTablesIfNotExists() {
        $this->createProduto();
        $this->createDocumento();
        $this->createItem();
    }
    
    private function getDataBaseConnection($conn) {
        $SQL = "CREATE DATABASE IF NOT EXISTS ". self::DB;
        $conn->query($SQL);
        return new mysqli(self::HOST, self::USERNAME, self::PASSWORD, self::DB);
    }
    
    private function createProduto() {
        $SQL = "CREATE TABLE IF NOT EXISTS produto (
                    id integer NOT NULL auto_increment,
                    codigo varchar(250),
                    descricao varchar(250),
                    preco numeric(16,2),
                    PRIMARY KEY (id)
                )";
        $this->conn->query($SQL);
    }
    
    private function createDocumento() {
        $SQL = "CREATE TABLE IF NOT EXISTS documento (
                    numero integer NOT NULL auto_increment,
                    total numeric(16,2),
                    confirmado boolean NOT NULL default 0,
                    PRIMARY KEY (numero)
                )";
        $this->conn->query($SQL);
    }
    
    private function createItem() {
        $SQL = "CREATE TABLE IF NOT EXISTS item (
                    documento_id integer NOT NULL,
                    produto_id integer NOT NULL,
                    PRIMARY KEY (documento_id, produto_id),
                    FOREIGN KEY (documento_id) REFERENCES documento(numero) ON DELETE CASCADE,
                    FOREIGN KEY (produto_id) REFERENCES produto(id) ON DELETE CASCADE
                )";
        $this->conn->query($SQL);
    }
    
    public function getFirstRow($SQL) {
        $result = $this->conn->query($SQL);
        if($result) {
            return mysqli_fetch_object($result);
        }
        return null;        
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
        return $this->execute($SQL);
    }
    
    public function execute($SQL) {
        if($this->conn->query($SQL) === TRUE) {
            return true;
        }
        die("Erro ao executar Insert");
    }
    
}
