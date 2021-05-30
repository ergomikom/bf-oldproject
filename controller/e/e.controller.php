<?php

/*
 *
 * Email: ergomicomseosem@gmail.com
 */
 
  require_once (Config::get("CONFIG_PATH")."model.config.php");

  class E extends Controller{
    //DB link
    protected $db;
    //Element id
    protected $eid;
    //Element name
    protected $emname;
    //Element type
    protected $etype;
    //Table of element types from model.config.php
    protected $etypes;

    public function __construct($request, $router){
      parent::__construct($request, $router);
      $this->db = new Database('models');
      $this->etypes = Config::get('model_element_types');
    }

    /**
    * Funkcja
    */
    public function single($eid){
      if($eid && is_numeric($eid) && $eid>0 && $this->db){
        $row = array();
        if(!DataCollector::name_id_exist('E',$eid)){
          $this->db->query('
            SELECT EID, EMNAME, ETYPE 
            FROM E 
            WHERE EID=:eid'
          );
          $this->db->bind(':eid', $eid);
          if($this->db->execute() && $this->db->rowCount()){
            
            $row = $this->db->single();
            $result['E']['EID'] = $row['EID'];
            $result['E']['EMNAME'] = $row['EMNAME'];
            $result['E']['ETYPE'] = $row['ETYPE'];
            $result['E']['ETYPENAME'] = $this->etypes[$row['ETYPE']]['name'];

            DataCollector::set('E', $eid, $result);
          }else{
            return false;
          }
        }else{
          $result = DataCollector::get('E', $eid);
        }

        $this->eid    = $result['E']['EID'];
        $this->emname = $result['E']['EMNAME'];
        $this->etype  = $result['E']['ETYPE'];
        $this->etypes[$this->etype]['selected'] = 'selected';
        $this->canbeupdated = $this->canBeUpdated();
        $this->canbedeleted = $this->canBeDeleted();
        return true;
      }
      return false;
    }
    
    /**
    * Funkcja sprawdza czy element może zostać zaktualizowany
    */
    private function canBeUpdated(){
      if($this->eid){
        $this->db->query('
          SELECT ERID 
          FROM EREV 
          WHERE EID_rel=:eid AND ERC=1'
        );
        $this->db->bind(':eid', $this->eid);
        if($this->db->execute() && !$this->db->rowCount()){
          return true;
        }
      }
      return false;
    }

    /**
    * Funkcja sprawdza czy element może zostać usunięty
    */
    private function canBeDeleted(){
      if($this->eid){
        $this->db->query('
          SELECT ERID 
          FROM EREV 
          WHERE EID_rel=:eid AND ERC=1'
        );
        $this->db->bind(':eid', $this->eid);
        if($this->db->execute() && !$this->db->rowCount()){
          return true;
        }
      }
      return false;
    }
    
    /**
    * Funkcja zlicza
    */
    protected function count_Erev_in_E($eid){
      if(is_int($this->eid) && $this->eid>0){
        $this->db->query('
          SELECT ERID 
          FROM EREV 
          WHERE EID_rel=:eid'
        );
        $this->db->bind(':eid', $eid);
        if($this->db->execute()){
            $count = $this->db->rowCount();
            return $count;
          return $count;
        }
      }
      return 0;
    }

    /**
    * Funkcja
    */
    protected function check_emname_exists(String $_emname){
      if(is_string($_emname)){
        $this->db->query('
          SELECT eid 
          FROM E WHERE 
          EMNAME=:emname
        ');
        $this->db->bind(':emname', $_emname);
        if($this->db->execute() && $this->db->rowCount()){
          return true;
        } else {
          return false;
        }
      }
      return false;
    }

  }
