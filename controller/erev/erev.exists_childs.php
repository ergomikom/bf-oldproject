<?php
/*
 * Klasa: erel_addchild
 *
 * Email: ergomicomseosem@gmail.com
 */
 
  class erev_exists_childs extends EREV{
  
    public function __construct($request, $router){
      parent::__construct($request, $router);
    }
    
    /**
    * Funkcje
    */
    public function GET_exists_childs($erid){
      if($this->single($erid)){
        if($this->mac){
          $rendered_template = $this->exists_childs_layout();
          return $rendered_template;
        }
      }else{
        return $this->reroute('error/1');
        //throw new Exception("Childs could not be loaded.");
      }
    }

    private function exists_childs_layout(){
      $layout = new Template($this->mimetype, $this->request, $this->router);
      $layout->erid = $this->erid;
      $layout->echilds = $this->select_available_children();
      $layout->SetStoraged(false);
      if(count($layout->echilds)){
        return $layout->Render();
      }
      return "";
    }

    private function select_available_children(){
      $result = array();
      $etype = $this->etype+1;
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
