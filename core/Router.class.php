<?php


/**
 * Description of Router
 *
 * @author marcin
 */

 require (Config::get("CONFIG_PATH")."router.config.php");

class Router{
  private $route;
  private $mimetype;
  private $controller;
  private $action;
  private $params;
  private $query;
  private $method;
  private $route_signature;
  private $context;
  private $pcontroller;
  private $paction;
  private $ptoken;
  private $pcontext;

  /**
   * Konstruktor routera
   * @param Request $request
   * @param type $method
   * @param type $controller
   * @param type $action
   * @param type $context
   * @param type $params
   */
  public function __construct(Request $request,
                                      $mimetype=null, 
                                      $method=null, 
                                      $controller=null, 
                                      $action=null, 
                                      $params=array(0), 
                                      $token=null, 
                                      $context=null, 
                                      $pcontroller=null, 
                                      $paction=null, 
                                      $ptoken=null, 
                                      $pcontext=null){
    $this->request      = $request;
    $this->context      = $context;
    $this->pcontroller  = $pcontroller;
    $this->paction      = $paction;
    $this->ptoken       = $ptoken;
    $this->pcontext     = $pcontext;

    if($mimetype == null){
      $this->mimetype = "html";
    }else{
      $this->mimetype = $mimetype;
    }

    if($token==null){ $this->token=Config::get("ROOT_TOKEN"); }else{ $this->token=$token; }

    if($this->request->getIsHttpMethodAccepted()){
      if($method && $controller && $action){
        $this->method     = $method;
        $this->controller = $controller;
        $this->action     = $action;
        $this->params     = $params;
      }else{
        //ustawianie zmiennych routingu
        $this->method = $this->request->getMethod();
        if(!$this->route($this->request->getURI())){
          throw new Exception(__FILE__.__LINE__.' (@Routing error!)');
        }else{
          $this->method = $this->request->getMethod();
        }
      }
    } else {
      throw new Exception(__FILE__.__LINE__.' (@Another router method!)');
      // TODO: Another method
    }
  }

  /**
   * Deserializer url
   * @param type $_url
   * @return type
   */
  public function route($_url){
    //TODO: tu dorobić obsługę CLEAN URL
    
    $url = $this->request->getRequestScheme().'://';
    
    $server_name = $this->request->getServerName();
    $server_addr = $this->request->getServerAddr();
    if($server_name){
      $url .= $server_name;
    }else if($server_addr){
      $url .= $server_addr;
    }

    $this->url_parsed = parse_url($url);
    $this->url_parsed['path'] = explode('/', trim($this->request->getPathInfo(),'/'));
    $this->url_parsed['query'] = $this->request->getGet();

    //Sprawdzenie czy pierwszy argument to datatype czy controller\
    //Sprawdza na podstawie allowed_controllers    
    
    $mimetype   = array_shift($this->url_parsed['path']);
    //FIXIT: utworzyć tablicę statyczną w klasie Config
    $allowedControllers = array('e','erev','erel','o');
    if(isset($mimetype) && in_array($mimetype, $allowedControllers)){
      $this->controller = $mimetype;
    }else{
      $this->mimetype = $mimetype;
      $this->controller = array_shift($this->url_parsed['path']);
    }
    $this->action     = array_shift($this->url_parsed['path']);
    $this->params = $this->url_parsed['path'];
    $this->query = $this->url_parsed['query'];
    return true;
  }

  public function classInit($controller, $action){
    $controller_path = "CONTROLLER_PATH";
    $controller_action_path = "DEV_CONTROLLER_PATH";
    if($controller){
      if( $this->_controller_exists($controller_path)){
        if($this->_action_controller_exists($controller_path)){
          $class_name = $controller.'_'.$action;
          if($this->_class_exists($class_name)){
            $obj = new $class_name($this->request, $this);
            return $obj;
          }
        }
      }
    }
    return false;
  }
  /**
   * Wywołanie żądanego zasobu
   * @return type
   */
  public function router(){
    $controller_path = "CONTROLLER_PATH";
    $controller_action_path = "DEV_CONTROLLER_PATH";
    if($this->controller){
      if( $this->_controller_exists($controller_path)){
        if($this->_action_controller_exists($controller_path)){
          $class_name = $this->controller.'_'.$this->action;
          if($this->_class_exists($class_name)){
            $obj = new $class_name($this->request, $this);
            $called_method = $this->method.'_'.$this->action;
              if(method_exists($obj, $called_method)){
                //if(count($this->params)){
                  return call_user_func_array( array($obj, $called_method), $this->params );
                //}else{
                //  throw new Exception("ERROR: Params not added.");
                //}
              }else{
                throw new Exception("ERROR: Action method [$called_method] not exists in $this->controller controller.");
              }
          }else{
            throw new Exception("ERROR: Class not exists in $this->controller controller.");
          }
        }else{
          throw new Exception("ERROR: Action $this->action not implement in core $this->controller controller.");
        }
        //TODO: czy tgo poniżej to nie trzeba skasować?
      }elseif($this->_controller_exists($controller_action_path)){
        if($this->_action_controller_exists($controller_action_path)){
          $class_name = $this->controller.'_'.$this->action;
          if($this->_class_exists($class_name)){
            $obj = new $class_name($this->request, $this);
            $called_method = $this->method.'_'.$this->action;
              if(method_exists($obj, $called_method)){
                if(count($this->params)){
                  return call_user_func_array( array($obj, $called_method), $this->params );
                }else{
                  throw new Exception("ERROR: Params not added.");
                }
              }else{ 
                throw new Exception("ERROR: Action method [$called_method] not exists in dev $this->controller controller.");
              }
          }else{
            throw new Exception("ERROR: Class not exists in dev $this->controller controller.");
          }
        }else{
          throw new Exception("ERROR: Action not implement in dev $this->controller controller.");
        }
      }else{
        throw new Exception("Controller not exists.");
        exit();
      }
    }
  }
  
  private function _class_exists($class_name){
      if(class_exists($this->controller)){
        return $class_name;
      }
    return false;
  }
  
  private function _controller_exists($type_path){
    $controller_file = $this->controller.'/'.$this->controller.'.controller.php';
      if(file_exists(Config::get($type_path).$controller_file)){
        require_once(Config::get($type_path).$controller_file);
        return true;
      }
    return false;
  }
  
  private function _action_controller_exists($type_path){
    $controller_action_file = $this->controller.'/'.$this->controller.'.'.$this->action.'.php';
      if(file_exists(Config::get($type_path).$controller_action_file)){
        require_once(Config::get($type_path).$controller_action_file);
        return true;
      }
    return false;
  }

  /**
   * Publiczny dostęp do nazwy kontrolera
   * @return type
   */
  public function getMimetype(){
    return $this->mimetype;
  }

  /**
   * Publiczny dostęp do nazwy kontrolera
   * @return type
   */
  public function getController(){
    return $this->controller;
  }

  /**
   * Publiczny dostęp do nazwy akcji
   * @return type
   */
  public function getAction(){
    return $this->action;
  }

  /**
   * Publiczny dostęp do przekazanych paramtrów
   * @return type
   */
  public function getParams(){
    return $this->params;
  }

  /**
   * Publiczny dostęp do przekazanych query
   * @return type
   */
  public function getQuery(){
    return $this->query;
  }
  
  /**
   * Publiczny dostęp do zawartości metody
   * @return type
   */
  public function getMethod(){
    return $this->method;
  }


  /**
   * Publiczny dostęp do kontekstu
   * @return type
   */
  public function getContext(){
    return $this->context;
  }

  /**
   * Publiczny dostęp do token
   * @return type
   */
  public function getToken(){
    return $this->token;
  }

  /**
   * Publiczny dostęp do parent_token
   * @return type
   */
  public function getPToken(){
    return $this->ptoken;
  }

  /**
   * Publiczny dostęp do parent_context
   * @return type
   */
  public function getPContext(){
    return $this->pcontext;
  }

  /**
   * Publiczny dostęp do nazwy parent kontrolera
   * @return type
   */
  public function getPController(){
    return $this->pcontroller;
  }

  /**
   * Publiczny dostęp do nazwy parent akcji
   * @return type
   */
  public function getPAction(){
    return $this->paction;
  }
  
  /**
   * Publiczny dostęp do parent_context
   * @return is_request_init?
   */
  public function initRequest(){
    return $this->getToken()=='root';
  }

}
