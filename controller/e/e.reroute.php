<?php

/*
 * Klasa: erev_view
 *
 * Email: ergomicomseosem@gmail.com
 */

  class e_reroute extends E{

    public function __construct($request, $router){
      parent::__construct($request, $router);
    }

    /**
    * Funkcja
    */
    public function GET_reroute($data, $code=null){
      if(isset($controller)){
        $template .= $this->reroute_layout($data, $code);
        if($this->ifRootRsp()){
          $this->response->response($template);
        }else{
          return $template;
        }
      }else{
        return $this->reroute('error/nsetController');
      }
    }

    private function reroute_layout($data, $code){
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
