<?php

/*
 * Klasa: erev_view
 *
 * Email: ergomicomseosem@gmail.com
 */

  class result_view extends RESULT{

    public function __construct($request, $router){
      parent::__construct($request, $router);
    }

    /**
    * Funkcja
    */
    public function RESULT_view($controller, $action, $code=null){
      if(isset($controller)){
        $template = $this->result_layout($controller, $action, $code);
        if($this->ifRootRsp()){
          $this->response->response($template);
        }else{
          return $template;
        }
      }else{
        //TODO: error default
      }
    }

    private function result_layout($controller, $action, $code){
      $layout = new Template($this->mimetype, $this->request, $this->router);
      if($controller){
        $layout->controller = $controller;
        if($action){
          $layout->action = $action;
          if($code){
            $layout->code = $code;
            $layout->AddSugestion($controller.'/'.$action.'/'.$code);
          }
          $layout->AddSugestion($controller.'/'.$action);
        }
        $layout->AddSugestion($controller);
      }
      $layout->AddSugestion($code);
      $layout->SetStoraged(false);
      return $layout->Render();
    }

  }
