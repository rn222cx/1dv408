<?php


class Db {
    // The database connection
    private $connection;
    private $stmt;

    /**
     * Connect to the database
     *
     * @return bool false on failure / mysqli MySQLi object instance on success
     */
    public function __construct() {

        try {
            $config = parse_ini_file('.env'); // My secret database information
            $this->connection = new PDO('mysql:host='.$config['DB_HOST'].';dbname='.$config['DB_DATABASE'],$config['DB_USERNAME'],$config['DB_PASSWORD'],
                array(PDO::ATTR_EMULATE_PREPARES => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

        } catch(PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
        }

    }

    public function query($query){
        $this->stmt = $this->connection->prepare($query);
    }

    public function bind($param, $value){
        $this->stmt->bindValue($param, $value);
    }

    public function execute(){
        return $this->stmt->execute();
    }

    public function resultset(){
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function single(){
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }


}