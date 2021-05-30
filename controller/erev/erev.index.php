<?php

/*
 * Klasa: erev_index
 * Klasa dla kontrolera akcji EREV->INDEX (erev/index)
 *
 * Email: ergomicomseosem@gmail.com
 */
 
  class erev_index extends EREV{
    
    public function __construct($request, $router){
      parent::__construct($request, $router);
    }
    
    /**
    * Funkcja obsługi żądania GET dla e,index
    */
    public function GET_index($eid_rel){
      if($eid_rel && is_numeric($eid_rel) && $eid_rel>0){
        $template = $this->layout($eid_rel);
        if($this->ifRootRsp()){
          $this->response->response($template);
        }else{
          return $template;
        }
      }else{
        throw new Exception("Parametr oznacząjący identyfikator modelu nie jest prawidłowy.");
      }
      //TODO: else co zrobić
    }
    
    /**
    * Funkcja dostarcza zmienne do szablonu
    */
    private function layout($eid_rel){
      $layout = new Template($this->mimetype, $this->request, $this->router);
      $layout->title = "List of elements ".$eid_rel;
      $layout->eid = $eid_rel;
      $layout->rows = $this->getEREVs($eid_rel);
      $layout->erevlist_count = sizeof($layout->rows);
      $layout->SetStorageFileName('erev_index_'.$layout->eid);
      $layout->SetStoraged(true);
      $layout->SetCache('CACHE1');
      return $layout->Render();
    }

    /**
    * Funkcja zwraca listę pól EID z tablicy E
    */
    //TODO: pobrać zmienną z [data] o wartości eid
    //by przekazać do funkcji pobierającej listę
    private function getEREVs($eid_rel){
      $result = array();
      $query = "SELECT ERID FROM EREV";
      $query .= " WHERE eid_rel=".$eid_rel;
      //Sortowanie
      if(isset($this->query['sort'])){ 
        $sortCol = $this->query['sort']; 
        $query .=" ORDER BY $sortCol ASC"; 
      }
      $this->db->query($query);
      if($this->db->execute() && $this->db->rowCount()){ 
        $rows = $this->db->resultset(); 
        while($row = array_shift($rows)){ $result[] = $row; } 
      }
      return $result;
    }

  }
 
