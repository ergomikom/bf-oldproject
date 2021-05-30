<?php

/*
 * Klasa: erel_view_childs
 *
 * Email: ergomicomseosem@gmail.com
 */
 
  class erev_view_childs extends EREV{
  
    public function __construct($request, $router){
      parent::__construct($request, $router);
    }
    
    /**
    * Funkcje
    */
    public function GET_view_childs($erid){
      if($this->single($erid)){
        $template = $this->childs_layout();
        if($this->ifRootRsp()){
          $this->response->response($template);
        }else{
          return $template;
        }
      }
    }
    
    private function childs_layout(){
      $layout = new Template($this->mimetype, $this->request, $this->router);
      $layout->erid = $this->erid;
      $layout->childs = $this->childs;
      $layout->childs_count = count($this->childs);
      if($this->etype!=4){
        $childs_exists = $layout->childs_count ? "childs_exists" : "childs-no-exists";
        $layout->AddSugestion($childs_exists);
      }
      $layout->SetStorageFileName('view_childs_'.$this->erid);
      $layout->SetStoraged(true);
      return $layout->Render();
    }

  }
