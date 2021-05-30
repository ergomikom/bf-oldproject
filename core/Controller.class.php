<?php

/*
 *
 * Email: ergomicomseosem@gmail.com
 */

  class Controller {
    protected $request;
    protected $router;
    protected $response;
    protected $data;
    protected $token;
    protected $params;
    protected $query;
    protected $ifresponse;
    protected $mimetype;
    
    /**
    * Funkcja
     * @param type 
     * @param type 
     */
    public function __construct($request, $router){
      $this->request = $request;
      $this->router = $router;
      $this->token = $router->getToken();
      $this->response = new Response($request);
      $this->data = $this->request->getData();
      $this->params = $this->router->getParams();
      $this->query = $this->router->getQuery();
      $this->ifresponse = $this->ifRootRsp();
      $this->mimetype = $this->router->getMimeType();
    }
    
    protected function ifRootRsp(){ if($this->token==Config::get("ROOT_TOKEN")){ return true; } return false; }

    //TODO: przrenieść do prawidłowej miejscówki
    protected function call($method, $mimetype, $controller, $action, $params=array()){
      $router = new Router($this->request, $mimetype, $method, $controller, $action, $params);
      return $router->router();
    }

    protected function reroute($name){
      /* TODO */
      $datas = array();
      $datas['data'] = $this->data;
      $datas['params'] = $this->params;
      $datas['query'] = $this->query;
      
      $router = new Router($this->request, $this->mimetype, 'GET',$this->router->getController(), 'reroute', array($datas, $name));
      return $router->router();
    }

  }
