<?php
/*
 *
 * Email: ergomicomseosem@gmail.com
 */
 
  class erev_exists_parents extends EREV{
  
    public function __construct($request, $router){
      parent::__construct($request, $router);
    }
    
    /**
    * Funkcje
    */
    public function GET_exists_parents($erid){
      if($this->single($erid)){
        if($this->map){
          $rendered_template = $this->exists_parents_layout();
          return $rendered_template;
        }
      }else{
        return $this->reroute('error/1');
      }
    }

    private function exists_parents_layout(){
      $layout = new Template($this->mimetype, $this->request, $this->router);
      $layout->erid = $this->erid;
      $layout->eparents = $this->select_available_parents($this->erid);
      $layout->SetStoraged(false);
      if(count($layout->eparents)){
        return $layout->Render();
      }
      return "";
    }

    private function select_available_parents($erid){
      $result = array();
      if($this->etype>1)  { $etype = $this->etype-1; }
      if($this->etype==1) { $etype = 1; }
      if($etype && is_numeric($etype) && $etype>0 && $this->db){
        $this->db->query('SELECT eid,emname FROM E WHERE E.ETYPE = :etype');
        $this->db->bind(':etype', $etype);
        $rows = $this->db->resultset();
        while($row = array_shift($rows)){
          $result[] = $row;
        }
      }
      return $result;
    }

  }
