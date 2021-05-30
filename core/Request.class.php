<?php
/*
 *
 * Email: ergomicomseosem@gmail.com
 */
  // Inicjacja konfiguracji żądania
  require (Config::get("CONFIG_PATH")."request.config.php");

    interface SanitizeInterface {
        public function getSanitizedData();
    }

    class Sanitize implements SanitizeInterface {
      private $taintedData = array();
      private $sanitizedData = array();
      private $magicVariable = null;
      private $allowMagicVariables = array('get','post','data','cookie');

      public function __construct($_magicVariable, $_tainedData) {
        $this->taintedData = $_tainedData;
        $this->magicVariable = $_magicVariable;
        if(in_array($this->magicVariable, $this->allowMagicVariables)) {
          foreach($this->taintedData as $key => $value) {
            if($key && $value) {
              $this->sanitizedData[htmlentities($key)] = htmlentities($value);
            }
          }
        }
      }
      public function getSanitizedData() {
        return $this->sanitizedData;
      }
    }

    class Request{
      private $uri;
      private $method;
      private $get;
      private $data;
      private $cookie;
      private $server_protocol;
      private $server_name;
      private $server_addr;
      private $server_port;
      private $remote_addr;
      private $documentroot;
      private $request_scheme;
      private $context_prefix;
      private $context_document_root;
      private $server_admin;
      private $redirect_url;
      private $redirect_status;
      private $request_time;
      private $http_referer;
      private $path_info;
      private $query_string;

      public  $request_content_type;

        private $is_http_method_save;
        private $is_http_method_accepted;
        private $is_http_method_idempotent;
        private $is_http_method_cacheable;

        public function __construct($uri=''){
        
        $this->uri                    = $uri ? $uri : $_SERVER['REQUEST_URI'];
        $this->method                 = isset($_SERVER['REQUEST_METHOD'])?$_SERVER['REQUEST_METHOD']:null;
        $this->server_protocol        = $_SERVER['SERVER_PROTOCOL'];
        $this->server_name            = $_SERVER['SERVER_NAME'];
        $this->server_addr            = $_SERVER['SERVER_ADDR'];
        $this->server_port            = $_SERVER['SERVER_PORT'];
        $this->remote_addr            = $_SERVER['REMOTE_ADDR'];
        $this->documentroot           = $_SERVER['DOCUMENT_ROOT'];
        $this->request_scheme         = $_SERVER['REQUEST_SCHEME'];
        $this->context_prefix         = $_SERVER['CONTEXT_PREFIX'];
        $this->context_document_root  = $_SERVER['CONTEXT_DOCUMENT_ROOT'];
        $this->server_admin           = $_SERVER['SERVER_ADMIN'];
        $this->redirect_url           = isset($_SERVER['REDIRECT_URL'])?$_SERVER['REDIRECT_URL']:null;
        $this->redirect_status        = isset($_SERVER['REDIRECT_STATUS'])?$_SERVER['REDIRECT_STATUS']:null;
        $this->request_time           = $_SERVER['REQUEST_TIME'];
        $this->http_referer           = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
        $this->path_info              = isset($_SERVER['PATH_INFO']) ?$_SERVER['PATH_INFO'] : null;
        $this->query_string           = isset($_SERVER['QUERY_STRING']) ?$_SERVER['QUERY_STRING'] : null;;
        
        //TODO: czy to musi tu być z tym html?
        $this->request_content_type   = 'html';
        
        $this->is_http_method_accepted      = in_array($this->method, Config::get("http_method_accepted"));
        if($this->is_http_method_accepted){
          $this->is_http_method_save        = in_array($this->method, Config::get("http_method_save"));
          $this->is_http_method_idempotent  = in_array($this->method, Config::get("http_method_idempotent"));
          $this->is_http_method_cacheable   = in_array($this->method, Config::get("http_method_cacheable"));

            if(isset($_SERVER['CONTENT_TYPE'])){
              // JSON data
              if(stripos($_SERVER['CONTENT_TYPE'], 'application/json')===0){
                $this->request_content_type = 'json';
                $json_data = json_decode(file_get_contents('php://input'));
                json_last_error() == JSON_ERROR_NONE;
                //TODO: dodać parser i validator
                $this->data = $json_data;
                //echo(var_dump($this->data));
              }
              // XML data
              if(stripos($_SERVER['CONTENT_TYPE'], 'application/xml')===0){
                $this->request_content_type = 'xml';
                $xml_data = file_get_contents('php://input');
                //TODO: dodać parser i validator
                $this->data = $xml_data;
                //echo(var_dump($this->data));
              }
            }
            
            switch($this->method){
              
              case 'GET':
                  $sanitizer = new Sanitize('get', $_GET);
                  $this->get = $sanitizer->getSanitizedData();
                break;
                
              case 'POST':
                  $sanitizer = new Sanitize('post', $_POST);
                  $this->data = $sanitizer->getSanitizedData();
                  // Convert HTTP method over POST _method data
                  if(isset($this->data['_method'])){
                    if(in_array($this->data['_method'], Config::get("http_method_accepted"))){
                      $this->method = $this->data['_method'];
                    }
                  }
                break;
                
              case 'PUT':
              case 'DELETE':
                  parse_str(file_get_contents("php://input"), $post_vars);
                  $sanitizer = new Sanitize('data', $post_vars);
                  $this->data = $sanitizer->getSanitizedData();
                break;
            }

            if($_COOKIE){
                $sanitizer = new Sanitize('cookie', $_COOKIE);
                $this->cookie = $sanitizer->getSanitizedData();
            }
            //TODO: ???
            if(isset($this->data->_res_type)){
              $this->res_content_type = $this->data->_res_type;
            }

          }else{
            // Exception - błąd żadania
            $this->method = null;
          }
          //echo var_dump($this);
          
        }

        public function getServerName(){
          return $this->server_name;
        }
        
        public function getServerAddr(){
          return $this->server_addr;
        }
        
        public function getPathInfo(){
          return $this->path_info;
        }
        
        public function getQueryString(){
          return $this->query_string;
        }
        
        public function getMethod(){
          return $this->method;
        }

        public function getIsHttpMethodAccepted(){
          return $this->is_http_method_accepted;
        }

        public function getIsHttpMethodSave(){
          return $this->is_http_method_save;
        }

        public function getIsHttpMethodIdempotent(){
          return $this->is_http_method_idempotent;
        }

        public function getIsHttpMethodCacheable(){
          return $this->is_http_method_cacheable;
        }

        public function getGet(){
          return $this->get;
        }
        
        public function getData(){
          return $this->data;
        }

        public function getPost(){
          return $this->data;
        }

        public function getCookie(){
          return $this->cookie;
        }

        public function getURI(){
          return $this->uri;
        }
        
        public function getRequestScheme(){
          return $this->request_scheme;
        }

        public function get_content_type(){
          return $this->request_content_type;
        }

        public function get_fcgi_server_version(){
          return $this->fcgi_server_version;
        }

        public function get_server_protocol(){
          return $this->server_protocol;
        }

        public function get_redirect_url(){
          return $this->redirect_url;
        }

        public function get_http_referer(){
          return $this->http_referer;
        }

        public function get_path_info(){
          return $this->patch_info;
        }
        
        public function getRequestTime(){
          $this->request_time;
        }

    }


