<?php

/*
 * Klasa: e_view
 *
 * Email: ergomicomseosem@gmail.com
 */
 
  class e_view extends E{
    
    public function __construct($request, $router){
      parent::__construct($request, $router);
    }
    
    /**
    * Funkcja
    */
    public function GET_view($eid){
      if($this->single($eid)){
        $template = $this->view_layout();
        if($this->ifRootRsp()){
          $this->response->response($template);
        }else{
          return $template;
        }
      }else{
        return $this->reroute('http/404');
      }
    }
    
    /**
    * Funkcja
    */
    private function view_layout(){
      $layout = new Template($this->mimetype, $this->request, $this->router);
      $layout->title = $this->emname;
      $layout->eid = $this->eid;
      $layout->etype = $this->etype;
      $layout->emname = $this->emname;
      $layout->etypename = str_replace(' ', '_', strtolower($this->etypes[$this->etype]['name']));
      $layout->count_erevs = $this->count_Erev_in_E($this->eid);
      //ZMIENNE SPECJALNE
      $layout->AddSugestion($layout->etypename);
      //$layout->SetTheme('basic');
      $layout->SetStoraged(true);
      $layout->SetStorageFileName($layout->eid);
      return $layout->Render();
    }

  }
