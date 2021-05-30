<?php

/*
 * Klasa: erev_view
 *
 * Email: ergomicomseosem@gmail.com
 */

  class erev_reroute extends EREV{

    public function __construct($request, $router){
      parent::__construct($request, $router);
    }

    /**
    * Funkcja
    */
    public function GET_reroute($controller, $action, $code=null){
      if(isset($controller)){
        $template = "<h1>EREV REROUTE</h1>";
        $template .= $this->reroute_layout($controller, $action, $code);
        if($this->ifRootRsp()){
          $this->response->response($template);
        }else{
          return $template;
        }
      }else{
        //TODO: error default
      }
    }

    private function reroute_layout($controller, $action, $code){
      $layout = new Template($this->mimetype, $this->request, $this->router);
        if($action){
          $layout->action = $action;
          $layout->AddSugestion($action.'/'.$code);
          if($code){
            $layout->code = $code;
            $layout->AddSugestion($code);
          }
        }          
      $layout->SetStoraged(false);
      return $layout->Render();
    }

  }
