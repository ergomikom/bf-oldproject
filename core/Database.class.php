<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Database
 *
 * @author marcin
 */

// Inicjacja konfiguracji bazy danych
require (Config::get("CONFIG_PATH")."db.config.php");

class Database{
    private $dsn;
    private $user;
    private $pass;
    private $stmt;

    private $dbh;
    private $error;

    public function __construct($connName){
        $this->dsn = Config::get("PDO")[$connName]['dsn'];
        $this->user = Config::get("PDO")[$connName]['username'];
        $this->pass = Config::get("PDO")[$connName]['password'];
        $options = array(
            PDO::ATTR_PERSISTENT    => true,
            PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION
        );
        try{
            $this->dbh = new PDO($this->dsn, $this->user, $this->pass, $options);
            $this->dbh -> query ('SET NAMES utf8');
            //$this->dbh -> query ('SET CHARACTER_SET utf8_unicode_ci');
        }
        catch(PDOException $e){
            $this->error = $e->getMessage();
            echo($this->error);
        }

    }

    public function query($query){
       $this->stmt = $this->dbh->prepare($query);
    }

    public function bind($param, $value, $type = null){
       if (is_null($type)) {
           switch (true) {
               case is_int($value):
                   $type = PDO::PARAM_INT;
                   break;
               case is_bool($value):
                   $type = PDO::PARAM_BOOL;
                   break;
               case is_null($value):
                   $type = PDO::PARAM_NULL;
                   break;
               default:
                   $type = PDO::PARAM_STR;
           }
       }
       $this->stmt->bindValue($param, $value, $type);
   }

   public function execute(){
      return $this->stmt->execute();
   }

    public function table_exists($table){
        $this->stmt->query = 'SHOW tables LIKE :table;';
        $this->stmt->bind(':table', $table);
        if($this->execute()){
          return  $this->rowCount();
        }
        return false;
    }

   public function resultset(){
      $this->execute();
      return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
   }

   public function single(){
       $this->execute();
       return $this->stmt->fetch(PDO::FETCH_ASSOC);
   }

   public function rowCount(){
       return $this->stmt->rowCount();
   }

   public function lastInsertId(){
       return $this->dbh->lastInsertId();
   }

   public function beginTransaction(){
      return $this->dbh->beginTransaction();
   }

   public function endTransaction(){
       return $this->dbh->commit();
   }

   public function cancelTransaction(){
       return $this->dbh->rollBack();
   }

   public function close(){
       return $this->dbh=NULL;
   }
}
