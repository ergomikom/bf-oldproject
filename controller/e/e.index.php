<?php

/*
 * Klasa: e_index
 * Klasa dla kontrolera akcji E->INDEX (e/index)
 *
 * Email: ergomicomseosem@gmail.com
 */
 
  class e_index extends E{
    
    public function __construct($request, $router){
      parent::__construct($request, $router);
    }
    
    /**
    * Funkcja obsługi żądania GET dla e,index
    */
    public function GET_index(){
      $template = $this->layout();
      if($this->ifRootRsp()){
        $this->response->response($template);
      }else{
        return $template;
      }
    }
    
    /**
    * Funkcja dostarcza zmienne do szablonu
    */
    private function layout(){
      $layout = new Template($this->mimetype, $this->request, $this->router);
      $layout->title = "List of elements";
      $layout->rows = $this->getEIDs();
      $layout->elist_count = sizeof($layout->rows);
      $layout->etypes = $this->etypes;
      $layout->SetStoraged(true);
      $layout->SetStorageFileName('e_index');
      $layout->SetCache('CACHE1');
      //$layout->SetTheme('two_column');
      return $layout->Render();
    }
    
    /**
    * Funkcja zwraca listę pól EID z tablicy E
    */
    private function getEIDs(){
      $result = array();
      $query = "SELECT EID FROM E";
      
      //Sortowanie
      if(isset($this->query['sort'])){
      $sortCol = $this->query['sort'];
        $query .=" ORDER BY $sortCol ASC";
      }
      
      $this->db->query($query);
      if($this->db->execute() && $this->db->rowCount()){
        $rows = $this->db->resultset();
        while($row = array_shift($rows)){
          $result[] = $row;
        }
      }
      return $result;
      
    }

  }
