  <?php

/*
 * Klasa: erel_view_parents
 *
 * Email: ergomicomseosem@gmail.com
 */
 
  class erev_view_parents extends EREV{
    
    public function __construct($request, $router){
      parent::__construct($request, $router);
    }
    
    /**
    * Funkcje
    */
    public function GET_view_parents($erid){
      if($this->single($erid)){
        $template = $this->parents_layout();
        if($this->ifRootRsp()){
          $this->response->response($template);
        }else{
          return $template;
        }
      }
    }
    
    private function parents_layout(){
      $layout = new Template($this->mimetype, $this->request, $this->router);
      $layout->erid = $this->erid;
      $layout->parents = $this->parents;
      echo print_r($layout->parents,1);
      $layout->parents_count = count($this->parents);
      $parents_exists = $layout->parents_count ? "parents_exists" : "parents-no-exists";
      $layout->AddSugestion($parents_exists);
      $layout->SetStorageFileName('view_parents_'.$this->erid);
      $layout->SetStoraged(true);
      return $layout->Render();
    }

  }
