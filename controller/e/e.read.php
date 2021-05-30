<?php

/*
 * Klasa: e_read
 
 * Email: ergomicomseosem@gmail.com
 */
 
  class e_read extends E{
    
    public function __construct($request, $router){
      parent::__construct($request, $router);
    }
    
    //TODO: albo inna funkcja dla zwrócenia return, albo jakiś ukryty automat wykrywający np. w response
    /**
    * Funkcja
    */
    public function GET_read($eid){
      if($this->single($eid)){
        $template = $this->layout();
        if($this->ifRootRsp()){
          $this->response->response($template, 200, 'application/json');
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
    public function layout(){
      $layout = new Template($this->mimetype, $this->request, $this->router);
      $layout->title = $this->emname;
      $layout->eid = $this->eid;
      $layout->etype = $this->etype;
      $layout->emname = $this->emname;
      $layout->etypename = str_replace(' ', '_', strtolower($this->etypes[$this->etype]['name']));
      $layout->cbu = $this->canbeupdated ? 1 : 0;
      $layout->cbd = $this->canbedeleted ? 1 : 0;
      $layout->count_erevs = $this->count_Erev_in_E($this->eid);
      
      
      //ZMIENNE SPECJALNE
      $layout->AddSugestion($this->emname);
      $layout->AddSugestion($layout->etypename);
      $layout->AddSugestion('cbu'.$layout->cbu);
      $layout->AddSugestion('cbd'.$layout->cbd);
      $layout->SetTheme('basic');
      $layout->SetStoraged(true);
      $layout->SetStorageFileName($layout->eid);
      return $layout->Render();
    }

  }
