<?php

/*
 * Klasa: e_types
 *
 * Email: ergomicomseosem@gmail.com
 */
 
  class e_types extends E{
    public function __construct($request, $router){
      parent::__construct($request, $router);
    }

    /**
    * Funkcja obsługująca żądanie typu GET dla elementu E
    */
    public function GET_types($eid=0){
      if($eid && is_numeric($eid)){
        if($this->single($eid)){
          $template = $this->get_types_layout();
          if($this->ifRootRsp()){
            $this->response->response($template);
          }else{
            return $template;
          }
        }else{
          return $this->reroute('http/404');
        }
      }else{
        $template = $this->get_types_layout();
          if($this->ifRootRsp()){
            $this->response->response($template);
          }else{
            return $template;
          }
      }
    }
    
    /**
    * Funkcja deklarująca i inicjalizująca zmienne i renderująca 
    * szablon tworzenia elementu e
    */
    private function get_types_layout(){
      $layout = new Template($this->mimetype, $this->request, $this->router);
      $layout->etypes = $this->etypes;
      if($this->etype){
        $layout->etype = $this->etype;
      }
      $layout->SetStoraged(false);
      return $layout->Render();
    }
      
  }
