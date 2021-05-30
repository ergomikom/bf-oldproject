<?php
/**
 * klasa Template
 *
 * @author marcin
 */

require (Config::get("CONFIG_PATH")."template.config.php");
require (Config::get("CONFIG_PATH")."language.config.php");

class Template{
  private $mimetype;
  private $values=array();
  private $method;
  private $controller;
  private $action;
  private $params;
  private $request;
  private $router;
  private $l10n;
  private $i18n;
  private $translator;
  
  private $storagedState;
  private $storageFileName;
  private $theme;
  private $cache;
  
  private $TemplateSugestions=array();

  private $foundExtFiles=array();
  
  private $requestGetContentType;
  private $requestGetMethod;

  private $filegenExt = [
    'html'  =>  ['scss', 'css','js'],
    'xml'   =>  ['xsl','xpath','xslt','xsd'],
    'rss'   =>  [],
    'atom'  =>  [],
    'json'  =>  [],
    'rdf'   =>  [],
  ];

  /**
   * Konstruktor szablonu
   * @param type $request
   * @param type $router
   * @param type $_method
   * @param type $_controller
   * @param type $_action
   * @param type $_data
   */
   
  public function __construct($mimetype, $request, $router, $_method=null, $_controller=null, $_action=null, $_data=array()){
  
    //inicjowanie zmiennych klasy
    $this->request = $request;
    $this->mimetype = $mimetype;
    $this->requestGetContentType = $request->get_content_type();
    $this->requestGetMethod = $request->getMethod();
    $this->router       = $router;
    $this->i18n         = $this->get_i18n();
    $this->l10n         = $this->get_l10n();
    $this->params       = $router->getParams();
    
    isset($_method)       ? $this->method     = strtolower($_method):$this->method=strtolower($this->router->getMethod());
    isset($_controller)   ? $this->controller = strtolower($_controller):$this->controller=strtolower($router->getController());
    isset($_action)       ? $this->action     = strtolower($_action):$this->action=strtolower($router->getAction());

    
    // inicjowanie domyślnych danych z konstruktora
    foreach($_data as $key=>$value){
      $this->values[$key]=$value;
    }

    if($this->router->getContext()){
      $this->TemplateSugestions[] = $this->router->getContext();
    }
    
    if(!isset($this->storageFileName)){
      $this->storageFileName = $this->controller.'_'.$this->action;
    }
    
    $this->translator = new Translate();

  }

  /**
   * Ustawia parametr dostępny w szablonie
   * @param type $key
   * @param type $value
   */
  public function __set($key, $value){
    $this->values[$key] = $value;
  }

  /**
   * Pobiera parametr dostępny w szablonie
   * @param type $key
   * @return string
   */
  public function __get($key){
    if(isset($this->values[$key])){
      return $this->values[$key];
    }else{
      return "NULL";
    }
  }
  
  /**
    * Funkcja
     * @param type data_type
     */
  public function SetMimeType($mimetype){
    if(isset($mimeType)&&is_string($mimeType)){
      $this->mimeType = $mimeType;
    }else{
      $this->mimetype = "html"; //TODO: ustalić poprawny w testach
    }
  }
  
  public function SetStoraged($storagedState){
    if(is_bool($storagedState)){
      $this->storagedState = $storagedState;
    }else{
      $this->storagedState = false;
    }
  }
  
  public function SetTheme($theme){
	$this->theme = $theme;
  }
  
  private function GetTheme(){
	if(isset($this->theme)){
      return $this->theme;
	}
	return null;
  }
  
  public function SetCache($cacheState){
    if(is_string($cacheState)){
      $this->cache = $cacheState;
    }else{
      $this->cache = 'CACHE0';
    }
  }
  
  public function SetStorageFileName($storageFileName){
    if(isset($storageFileName)&&is_string($storageFileName)){
      $this->storageFileName = $storageFileName;
    }else{
      $this->storageFileName = $this->controller.'_'.$this->action;
    }
  }
  
  private function GetStorageFileName(){
    if(isset($this->storageFileName)){
      return $this->storageFileName;
    }
    return $this->controller.'_'.$this->action;
  }
  
  /**
    * Funkcja
     */
  public function Render(){

    //FIXIT: usunąć po zmianie formuły dodawania sugestii do szablonu
    //if(isset($this->values['_TEMPLATESUGGESTIONS_'])){ $this->TemplateSugestions = $this->values['_TEMPLATESUGGESTIONS_']; }

    $storage_path = Config::get("STORAGE_PATH");
    $theme = null;
    if($this->getTheme()){
      $theme = $this->getTheme().'/';
    }

    $dir_signature =  $storage_path.
                  //'/'.$this->method.
                  '/'.$this->controller.
                  '/'.$this->action.'/';
    
    $token        = $this->router->getToken()       ? $this->router->getToken()     : false;
    $context      = $this->router->getContext()     ? $this->router->getContext()   : false;    
    $pcontroller  = $this->router->getPController() ?$this->router->getPController(): false;
    $paction      = $this->router->getPAction()     ?$this->router->getPAction()    : false;
    $ptoken       = $this->router->getPToken()      ? $this->router->getPToken()    : false;
    $pcontext     = $this->router->getPContext()    ? $this->router->getPContext()  : false;
    
    if($token){
      $dir_signature.= $token.'/'; 
      if($context){
        $dir_signature.= $context.'/';
      }
      
      if($pcontroller&&$paction){
        $dir_signature.='in_'.$pcontroller.'_'.$paction.'/';
        if($ptoken){
          $dir_signature.= $ptoken.'/';
          if($pcontext){
            $dir_signature.= $pcontext.'/';
          }
        }
      }
      
      if(Config::get('l10n')!=Config::get("Dl10n")){
        $dir_signature.=$this->l10n.'/'.$this->i18n.'/';
      }
    }
    
    $storageName = $this->getStorageFileName();
    $file_signature = $storageName.'.'.$this->mimetype;

    //Ustawienie tokena wg zmiennej specjalnej _TOKEN_
    if(isset($this->values['_TOKEN_'])){ $token = $this->values['_TOKEN_']; }else{ $token=$this->router->getToken(); }

    if(isset($this->values['_ROOT_TOKEN_'])){ $token=$this->values['_ROOT_TOKEN_'];}
   
   //Sprawdza czy dany REST jest STORAGED
    if(isset($this->storagedState)&&is_bool($this->storagedState)){
      if($this->storagedState){
        $storaged = true;
      }else{
        $storaged = false;
      }
    }else{
      $storaged = false;
    }
    
    // brak keszowania i generowanie za każdym razem przy ustawieniu globalnym na 0
    // i przy żądaniu szablonu z kontrolera z ustawionym poziomem keszowania na _NOCACHE_
    if($storaged==false || Config::get("CACHE_theme")==0 || (isset($this->cache) && $this->cache=='NOCACHE')){
      $schema = $this->render_token_context($token, $context, $this->values, $ptoken, $pcontext);
      //FIXIT: komentowanie renderowania hierarchicznego dla kontrolerów i akcji potomnych
      $schema = $this->find_controler_action_render_pass($schema);
      return $this->generateFileExt($schema);
    }

    $file_exists = file_exists($dir_signature.'/'.$file_signature);

    if($storaged && (!$file_exists && Config::get("CACHE_theme")==2) || Config::get("CACHE_theme")==1 || (isset($this->cache) && $this->cache=='CACHE1')){
      //echo $token;
      $schema = $this->render_token_context($token, $context, $this->values, $ptoken, $pcontext);
      if (!is_dir($dir_signature)){
        mkdir($dir_signature, 0777, true);
      }
      $schema = $this->generateFileExt($schema);
      $storage_save = file_put_contents($dir_signature.'/'.$file_signature, $schema);
      $schema = $this->find_controler_action_render_pass($schema);
      return $schema;
    }else if($file_exists && Config::get("CACHE_theme")==2){
      $schema = file_get_contents($dir_signature.'/'.$file_signature);
      return $schema;
    }
  }

  private function generateFileExt($schema){
    //TODO: funkcja czyszczenie nieużywanych plików
    $added_foundExtFiles = array();
    $public_ext_path = Config::get("PUBLIC_EXT_PATH");
    $external_public_ext_path = Config::get("EXTERNAL_PUBLIC_EXT_PATH");
    $dir_signature =  $public_ext_path.$this->controller.'/'.$this->action.'/'.$this->l10n.'/'.$this->i18n.'/';
    $external_dir_signature =  $external_public_ext_path.$this->controller.'/'.$this->action.'/'.$this->l10n.'/'.$this->i18n.'/';
    //CZEGO ŻĄDA DESIGNER
    $designer_ext_files_request = $this->designer_ext_files_request($schema);
    //echo var_dump($designer_ext_files_request);
    $this->foundExtFiles = Tools::reduce_duplicate_in_array($this->foundExtFiles);
    //echo var_dump($this->foundExtFiles).'</br>';
    foreach($designer_ext_files_request as $defiler){
    //echo var_dump($defiler);
        if($defiler['recreated']==0){

          $precreate_file_path = $dir_signature.$defiler['name'].'.'.$defiler['ext'];
          if (!is_dir($dir_signature)){
            mkdir($dir_signature, 0777, true);
          }
          $file = file_put_contents($precreate_file_path,'');

          $external_finish_file_path = $external_dir_signature.$defiler['name'].'.'.$defiler['ext'];
          $ext_link_in_schema = $this->extensionLinkRender($external_finish_file_path, $defiler);
          $schema = str_replace($defiler['replacer'],$ext_link_in_schema, $schema);
          $defiler['recreated']=1;
        }
        
        if(is_array($this->foundExtFiles) && sizeof($this->foundExtFiles)){
          foreach($this->foundExtFiles as $foundExtFile){
            //echo var_dump($foundExtFile).'</br>';
            $now_added = in_array($foundExtFile, $added_foundExtFiles);
            if($foundExtFile['name']==$defiler['name'] && $foundExtFile['ext']==$defiler['ext'] && !$now_added){

              if (!is_dir($dir_signature)){
                mkdir($dir_signature, 0777, true);
              }
              $prebuild_ext_path = $foundExtFile['path'].$foundExtFile['name'].'.'.$foundExtFile['ext'];
              $content = file_get_contents($prebuild_ext_path);
              $finish_file_path = $dir_signature.$foundExtFile['name'].'.'.$foundExtFile['ext'];
              if($defiler['recreated']==0){
                $file = file_put_contents($finish_file_path,$content);
              }else{
                $file = file_put_contents($finish_file_path,$content, FILE_APPEND | LOCK_EX);
              }
              $added_foundExtFiles[] = $foundExtFile;
            }
          }
        }
    }
    return $schema;
  }

  private function extensionLinkRender($path, $defiler){
    switch($defiler['ext']){
	    case 'scss':
          $config = '';
          if(isset($defiler['config'])){
            $config = $defiler['config'];
          }
          $path = str_replace(".scss",".css",$path);
          return '<link rel="stylesheet" type="text/css" href="'.$path.'" '.$config.'>';
        break;
      case 'css':
          $config = '';
          if(isset($defiler['config'])){
            $config = $defiler['config'];
          }
          return '<link rel="stylesheet" type="text/css" href="'.$path.'" '.$config.'>';
        break;
      case 'js':
          return '<script src="'.$path.'"></script>';
        break;
    }
  }

  //zwraca tablicę znalezionych żądań zewnętrznych do plików
  private function ext_in_schema($schema){
    $re = '/\{\ *\((\w+)\) *\[(\w+)\]\ *([\(|\[].+[\)|\]])* *\}{0,}/';
    $str = $schema;
    preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
    if(sizeof($matches)){
      return $matches;
    }
    return null;
  }

    /**
     * @param $schema
     * @return array|bool
     */
    private function designer_ext_files_request($schema){
    $ext_files_request = $this->ext_in_schema($schema);
    $return = array();
    $iterator = 0;
    //FIXIT
    if(is_array($ext_files_request) && sizeof($ext_files_request)){
      foreach($ext_files_request as $efr_row){
        $return[$iterator]['replacer'] = $efr_row[0];
        $return[$iterator]['recreated'] = 0;
        $return[$iterator]['name'] = $efr_row[1];
        $return[$iterator]['ext'] = $efr_row[2];
        if(isset($efr_row[3])){
          $config = str_replace('[','',$efr_row[3]);
          $config = str_replace(']','',$config);
          $return[$iterator]['config'] = $config;
        }else{
          $config = NULL;
        }
        $iterator++;
      }
    }
    $return = Tools::reduce_duplicate_in_array($return);
    return $return;
  }

  private function get_i18n(){
    $i18n = Config::get('i18n');
    if($i18n){return $i18n;
    }else{return Config::get('Di18n');
    }
  }

  private function get_l10n(){
    $l10n = Config::get('l10n');
    if($l10n){return $l10n;
    }else{return Config::get('Dl18n');
    }
  }

/**
   * Wyszukuje zmienne VALUES
   * @param type $schema
   * @param type $args
   * @return type
   */
  private function find_prn($schema){
    $pattern = '/\{\*prn\}/';
    $out = preg_replace_callback(
      $pattern,
      function($matches){
        return $this->prnValues();
      },
      $schema);
    return $out;
  }

  //Renderuje zmienne VALUES
  private function prnValues(){
    $return = "";
    foreach($this->values as $key=>$value){
      if(!is_array($value)){
        $return ."$key == $value, ";
      }else{
        $return ."$key == array(";
        
        //foreach($value as $val){
        //  $return ."$val,";
        //}
        $return ."),";
      }
      
    }
    return $return;
  }

  /**
   * Wyszukuje instrukcje specjalne
   * @param type $schema
   * @param type $args
   * @return type
   */
  private function find_si($schema, $args = null, $ptoken=null, $pcontext=null){
    $pattern = '/\{\{(\w+)\}\}/';
    $out = preg_replace_callback(
      $pattern,
      function($matches) use($args, $ptoken, $pcontext){
        return "{&variables&}";
      },
      $schema);
    return $out;
  }
  
  /**
   * Konwersja UNIX timestamp to PHP Date
   * date time format
   * @param type $schema
   * @return type
   */
  private function find_render_dtc($schema){
    $pattern = '/\{dtf\((\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})\)\((.+)\)\}/';
    $out = preg_replace_callback($pattern,
      function($matches){
        $date = $matches[1].'-'.$matches[2].'-'.$matches[3].' '.$matches[4].':'.$matches[5].':'.$matches[6];
        $date = new DateTime($date);
        return $date->format($matches[7]);
        //return date($matches[2], );
      },$schema);
    return $out;
  }

  /**
  * Tłumaczenie ciągu
  * @param type $schema
  * @return type
  */
  private function find_render_tt($schema, $context, $args, $ptoken=null, $pcontext=null){
    $pattern = '/\{tt\((.[^()]*)\)\}/';
    $out = preg_replace_callback($pattern,
      function($matches) use($context, $args, $ptoken, $pcontext){
        
        // Jeśli lokalizacja i język domyślny, to ten sam co użytkownika
        if(Config::get('l10n')==Config::get("Dl10n") && Config::get('i18n')==Config::get("Di18n")){
          return $matches[1];
        }

        if(strlen($matches[1])>=2){
          $translation = $this->translator->tt($matches[1], $this->i18n);
          $translate_args = array('translation'=>$translation, 'i18n'=>$this->i18n);
          $args = array_merge($args, $translate_args);
          //echo var_dump($args);
            if($context){
            return $this->render_schema('{&translation('.$context.')&}', $args, $ptoken, $pcontext);
          }else{
            return $this->render_schema('{&translation&}', $args, $ptoken, $pcontext);
          }
        }
      },$schema);
    return $out;
  }

  /**
   * Wyszujuje controller-action
   * @param type $schema
   * @return type
   */
  //TODO: poprawić działanie tego patternu, by można było
  //nie podawać kontekstu i sprawdzić przekazywanie przez wartość
   private function find_controler_action_schema_pass($schema, $ptoken=null, $pcontext=null){
     //$pattern = '/\{\!\((\w+)\)\((\w+)\)(\((\w+)\)*)* *((\S+))* *\!\}/';
     $pattern = '/\{\! *\((\w+)\){1} *\((\w+)\){1} *(\((\w+)\))? *(\S+)? *\!\}/';
       $out = preg_replace_callback(
         $pattern,
         function($matches) use($ptoken, $pcontext){
           return $this->render_controler_action_schema_pass($matches, $ptoken, $pcontext);
         },
       $schema);
     return $out;
   }

  private function find_controler_action_render_pass($schema){
  //return "<< ".$schema." >>";
    $pattern = '/\{\!(\S+)*\!\}/';
      $out = preg_replace_callback(
      $pattern,
      function($matches){
        return $this->render_controler_action_render_pass($matches);
      },
      $schema);
    return $out;
  }

  /**
   * Renderuje controller-action
   * @param type $matches
   * @return type
   */
   private function render_controler_action_schema_pass($matches, $ptoken=null, $pcontext=null){
     //echo var_dump($matches);
     $controller = $matches[1];
     $action = $matches[2];
     $context = isset($matches[4])?$matches[4]:null;
     $params = isset($matches[5])?$matches[5]:array();
     //WHATIS: - 
     $ptoken = isset($ptoken)?$ptoken:"";
     $pcontext = isset($pcontext)?$pcontext:"";

     if(isset($matches[4])){
        $return = '{!'.$controller.','.$action.','.$context.','.$ptoken.','.$pcontext.','.$params.'!}';
        return $return;
     }
    return null;
   }

  private function render_controler_action_render_pass($matches){
    $data = explode(',',$matches[1]);
    $controller   = $data[0];
    $action       = $data[1];
    $context      = isset($data[2])?$data[2]:null;
    $ptoken       = isset($data[3])?$data[3]:null;
    $pcontext     = isset($data[4])?$data[4]:null;
    $pcontroller  = $this->controller;
    $paction      = $this->action;
    

    if(isset($data[5])){
      $params=array_values(explode(',', trim($data[5],'{}')));
    }else{
      $params=array();
    }
    //ustawia token do renderowania schematu jako startowego: domyślnie=>entity
    $token = Config::get('ENTITY_name');
    $router = new Router($this->request, $this->mimetype, $this->requestGetMethod, $controller, $action, $params, $token, $context, $pcontroller, $paction, $ptoken, $pcontext);
    return $router->router();
  }
  
  /**
   * Wyszukiwanie linków dynamicznych
   */
  private function find_links($schema){
    $pattern = '/\{lb\((\w+)\)\((\w+)\)(\[(\S*)\]){0,1}(\{(\S*)\}){0,1}\}/';
    $out = preg_replace_callback(
      $pattern,
      function($matches){
        return $this->render_links($matches);
      },
      $schema);
    return $out;
  }
  
  private function render_links($matches){
    $url = "";
    if(isset($matches[1]) && isset($matches[2])){
      $controller = $matches[1];
      $action     = $matches[2];
      $params = '';
      $url = "/".$controller."/".$action;
      
      if(isset($matches[4])){
        $params = str_replace(',','/',$matches[4]);
        $url .= "/".$params;
      }
      
      if(isset($matches[6])){
        $query = str_replace(',','&',$matches[6]);
        $url .= "?".$query;
      }
      return $url;
      
    }else{
      //TODO: tu można dodać np. {!(error)(link)!}
      return "/error/link";
    }
    
  }
  

  /**
   * Wyszukiwanie zmiennych
   * @param type $schema
   * @param type $args
   * @return type
   */
  private function find_vars($schema, $args = null, $ptoken=null, $pcontext=null){
    $pattern = '/\{\%(\w+)\%([\(|\[].+[\)|\]])*\}/';
    $out = preg_replace_callback(
      $pattern,
      function($matches) use($args, $ptoken, $pcontext){
        //echo '<br/>Matches: '.print_r($matches,true).' - Args: '.print_r($args,true).'<br/><br/>';
        return $this->render_vars($matches, $args, $ptoken, $pcontext);
      },
      $schema);
    return $out;
  }

  /**
   * Renderowanie zmiennych
   * @param type $matchesrender_varsrender_varsrender_varsvrender_varsrender_varsrender_vars
   * @param type $args
   * @return type
   */
  private function render_vars($matches, $args, $ptoken=null, $pcontext=null){
    $varName = $matches[1];
    $varInfo    = $this->getVarInfo($varName, $args);
    $varStatus  = $varInfo[0];
    $varValue   = $varInfo[1];
    if($varStatus=='single'){
      return $varValue;
    }
    else if($varStatus=='array' && !isset($matches[2])){
      return $this->render_array($matches, $varValue, $ptoken, $pcontext);
    }
    else if($varStatus=='array' && isset($matches[2])){
      $contextInfo = $this->getContextInfo($matches, $args);
      switch($contextInfo[1]){
        case 'context':
            $matches[2] = $contextInfo[0];
            return $this->render_array($matches, $varValue, $ptoken, $pcontext);
          break;
      }
    }
    return null;
  }

  /**
   * Renderowanie wartości z wykrytych dopasowań
   * @param type $match
   * @param type $args
   * @return type
   */
  private function renderValueFromMatch($match, $args){
    if(isset($args[$match])){
      if(!is_array($args[$match])){
        return $args[$match];
      }
    }elseif(isset($this->values[$match])){
      if(!is_array($this->values[$match])){
        return $this->values[$match];
      }
    }else{
      return null;
    }
  }

  /**
   * Renderowanie tablicy
   * @param type $matches
   * @param type $array
   * @return type
   */
   //TODO: Dodać do tablicy first, last (jakoś to zrobić by dało się utworzyć szablon nazwy)
  private function render_array($matches, $array, $ptoken=null, $pcontext=null){
    $return = "";
      if(!isset($matches[2])){ $matches[2]=null; }
      if(isset($array)&&is_array($array)){
        $i = 0;
        $sizeof = sizeof($array);
        foreach($array as $key=>$value){
            $local_array = array();
            if(is_array($value)){
                $local_array['row_number']=$i;
                $local_array['oddeven']=($i % 2 ? 'even' : 'odd');
                $local_array['key']=$key;
                $local_array['rows_count']=$sizeof;
                $local_array['first']= $i==0?1:0;
                $local_array['last']= $i==($sizeof-1)?1:0;
                foreach($value as $key=>$value){
                    $local_array[$key]=$value;
                }
                $local_array=array_change_key_case($local_array);
                $return .= $this->render_token_context($matches[1], $matches[2], $local_array, $ptoken, $pcontext);
            }else{
                $local_array['row_number'] = $i;
                $local_array['oddeven'] = ($i % 2 ? 'even' : 'odd');
                $local_array['key']=strtolower($key);
                $local_array['row_numbers']=$sizeof;
                $local_array['first']= $i==0?1:0;
                $local_array['last']= $i==($sizeof-1)?1:0;
                $local_array[$key]=$value;
                $return .= $this->render_token_context($matches[1], $matches[2], $local_array, $ptoken, $pcontext);
            }
            $i++;
        }
      }
    return $return;
  }

  /**
   * Pobiera informacje o zmiennej
   * @param type $varName
   * @param type $args
   * @return type
   */
  private function getVarInfo($varName,$args){
        if(isset($args[$varName])){
          // czy występuje w tablicy pomocniczej args
          if(is_array($args[$varName])){
          //echo '>>'.$varName.'[ tablica ]';
            $varStatus = 'array';
            $varValue = $args[$varName];
          }else{
           //echo '>>'.$varName.'[ single ]';
            $varStatus = 'single';
            $varValue = $args[$varName];
          }
        }else if(isset($this->values[$varName])){
          // czy występuje w tablicy wartości klasy values
          if(is_array($this->values[$varName])){
            $varStatus = 'array';
            $varValue = $this->values[$varName];
          }else{
            $varStatus = 'single';
            $varValue = $this->values[$varName];
          }
        }else{
          $varStatus = 'name';
          $varValue = null;
        }

    return array($varStatus, $varValue);
  }

  /**
   * Pobiera informacje o kontekście
   * @param type $matches
   * @param type $args
   * @return type
   */
  private function getContextInfo($matches, $args){
    //rozpoznawanie kontekstu
    if(preg_match('/([\(|\[])(.+)([\)|\]])/', $matches[0], $context_matches)){
      if($context_matches[1]=='(' && $context_matches[3]==')'){
        if(preg_match('/\((\w+)\)/', $context_matches[0], $match_matches)){
          return array($match_matches[1], 'context');
        }
        else if(preg_match('/\{\%(\w+)\%\}/', $context_matches[2], $match_matches)){
          $value = $this->renderValueFromMatch($match_matches[1], $args);
          if($value){
            return array($value, 'context');
          }else{
            return null;
          }
        }
      }
    }
    return null;
  }
  
/**
   * Renderuje wyszukany token i context z/lub bez parametrów
   * @param type $token
   * @param type $context
   * @param type $args
   * @return type
   */
  private function render_token_context($token, $context=null, $args=null, $ptoken=null, $pcontext=null){
    //Dziedziczenie kontekstu ze zmiennej pcontext w context
    if($context==null && $pcontext!=null){
      $context = $pcontext;
      //echo "Token=".$token." Context=".$context." PToken=".$ptoken." PContext=".$pcontext."</br>";
    }
    $schema_sugestion = $this->schema_sugestion($token, $context, $ptoken, $pcontext);

            //Add new variable to template
            $this->values['_I18N_']=$this->i18n;
            $this->values['_L10N_']=$this->l10n;
            $this->values['_METHOD_']=$this->method;
            $this->values['_CONTROLLER_']=$this->controller;
            $this->values['_ACTION_']=$this->action;
            $this->values['_TOKEN_']=$token;
            $this->values['_CONTEXT_']=$context;
            $this->values['_PTOKEN_']=$ptoken;
            $this->values['_PCONTEXT_']=$token;
            if($args && isset($args['row_number'])){
              $this->values['_ARR_ROW_NUMBER_']=$args['row_number'];
              $this->values['_ARR_ROW_ODDEVEN_']=$args['oddeven'];
              $this->values['_ARR_ROW_KEY_']=$args['key'];
              $this->values['_ARR_ROWS_COUNT_']=$args['rows_count'];
              $this->values['_ARR_IS_FIRST_']=$args['first'];
              $this->values['_ARR_IS_LAST_']=$args['last'];
              $this->values['_ARR_JSON_COMMA_']=$args['last']==1?"":",";
            }

    //echo 'SCHEMAT: '. $schema_sugestion[0].$schema_sugestion[1].'<br/><br/>';
    if(file_exists($schema_sugestion[0].$schema_sugestion[1])){
      $schema = file_get_contents($schema_sugestion[0].$schema_sugestion[1]);
      //if(Config::get("APP_ENV") == 'dev'){
      //  $schema = '<!-- '. $schema_sugestion[0].$schema_sugestion[1] .' -->'.$schema;
      //}
      $schema = $this->find_prn($schema);
      $schema = $this->find_si($schema, $args, $ptoken, $pcontext);
      $schema = $this->find_vars($schema, $args, $token, $context);
      $schema = $this->find_render_dtc($schema);
      $schema = $this->find_render_tt($schema, $context, $args, $token, $context);
      $schema = $this->find_links($schema);
      $schema = $this->find_controler_action_schema_pass($schema, $token, $context);
      //echo $this->debug($schema_sugestion[0].$schema_sugestion[1]);
      $schema = $this->render_schema($schema, $args, $token, $context,$ptoken,$pcontext);
      return $schema;
    }else{
      //return $str_token = 'Render error loading template scheme: '.$token.' ('.$context.') in '.$ptoken.' ('.$pcontext.')';
      //TODO: dodaj do zmiennej z wynikami dla developera
      return ""; //"error: ".$this->mimetype.'/'.$this->router->getMethod().'/'.$this->router->getController().'/'.$this->router->getAction().'/'.$token.'/'.$context.' - template file not exists.';
    }
  }

  private function debug($data){
    if(!is_array($data)){
      return '<script type="text/javascript">console.log("'.$data.'");</script>';
    }else{
      return '<script type="text/javascript">console.log('.json_encode($data).');</script>';
    }
  }

  /**
   * Renderuje wyszukany schemat
   * @param type $schema
   * @param type $args
   * @return type
   */
  private function render_schema($schema, $args=null, $ptoken=null, $pcontext=null){
    $pattern = '/\{\&(\w+)([\(|\[].+[\)|\]])*\&\}/';
    $out = preg_replace_callback(
      $pattern,
      function($matches) use($args, $ptoken, $pcontext){
        //echo print_r($matches, 1);
        if(isset($matches[2])){
          $context = trim($matches[2],'()');
          return $this->render_token_context($matches[1], $context, $args, $ptoken, $pcontext);
        }else{
          return $this->render_token_context($matches[1], null, $args, $ptoken, $pcontext);
        }
      },
      $schema);

    return $out;
  }

  /**
   * Zwraca tablicę ścieżek do szablonów
   * @param type $token
   * @param type $context
   * @return string
   */
  private function get_template_paths($token, $context, $ptoken=false, $pcontext=false, $rct){
    $controller=$this->controller;
    $action=$this->action;
    $l10n = strtolower($this->l10n);
    $for_all=Config::get("TEMPLATE_ALL");

      $dev_themes_path=Config::get("THEME_PATH");
      $dev_theme_path=strtolower(Config::get("THEME"));
      $dev=$dev_themes_path.$dev_theme_path;


  	$paths=array();
      if(Config::get('l10n')!=Config::get("Dl10n")){
		if($this->getTheme()){
          $theme = $this->getTheme();
          $paths[]=$dev.'/l10n/'.$l10n.'/'.$controller.'/'.$action.'/'.$theme.'/'.$rct.'/'.$token;
          $paths[]=$dev.'/l10n/'.$l10n.'/'.$controller.'/'.$theme.'/'.$for_all.'/'.$rct.'/'.$token;
          $paths[]=$dev.'/l10n/'.$l10n.'/'.$for_all.'/'.$theme.'/'.$rct.'/'.$token;
		}//else{
          $paths[]=$dev.'/l10n/'.$l10n.'/'.$controller.'/'.$action.'/'.$rct.'/'.$token;
          $paths[]=$dev.'/l10n/'.$l10n.'/'.$controller.'/'.$for_all.'/'.$rct.'/'.$token;
          $paths[]=$dev.'/l10n/'.$l10n.'/'.$for_all.'/'.$rct.'/'.$token;
        //}
      }

      if($this->getTheme()){
          $theme = $this->getTheme();
          $paths[]=$dev.'/g11n/'.$controller.'/'.$action.'/'.$theme.'/'.$rct.'/'.$token;
          $paths[]=$dev.'/g11n/'.$controller.'/'.$theme.'/'.$for_all.'/'.$rct.'/'.$token;
          $paths[]=$dev.'/g11n/'.$for_all.'/'.$theme.'/'.$rct.'/'.$token;
      }//else{
        $paths[]=$dev.'/g11n/'.$controller.'/'.$action.'/'.$rct.'/'.$token;
        $paths[]=$dev.'/g11n/'.$controller.'/'.$for_all.'/'.$rct.'/'.$token;
        $paths[]=$dev.'/g11n/'.$for_all.'/'.$rct.'/'.$token;
      //}
      
    return $paths;
  }

  /**
   * Zwraca tablicę helperów do podkatalogów
   * @param type $token
   * @param type $context
   * @param type $ptoken
   * @param type $pcontext
   * @return string
   */
  private function get_subtemplate_paths($token, $context=false, $ptoken=false, $pcontext=false){
    $helper_file = array();
    
    if($token){
      $pcontroller  = $this->router->getPController() ?$this->router->getPController(): false;
      $paction      = $this->router->getPAction()     ?$this->router->getPAction()    : false;
      $pc_pa='_in_/'.$pcontroller.'/'.$paction;
      
      if(Config::get("TEMPLATE_PARENT")==1){
      
        if($pcontroller&&$paction){
          if($context){
            if($ptoken&&$pcontext){
            $helper_file[]=strtolower('/'.$context.'/'.$pc_pa.'/'.$ptoken.'/'.$pcontext.'/');
            }
            if($ptoken){
              $helper_file[]=strtolower('/'.$context.'/'.$pc_pa.'/'.$ptoken.'/');
            }
            $helper_file[]=strtolower('/'.$context.'/'.$pc_pa.'/');
          }
          
          
          if(!$context&&$ptoken){
            if($pcontext){
              $helper_file[]=strtolower('/'.$pc_pa.'/'.$ptoken.'/'.$pcontext.'/');
            }else{
              $helper_file[]=strtolower('/'.$pc_pa.'/'.$ptoken.'/');
            }
          }
        
        }
      }
            
      if($context){
        $helper_file[]=strtolower('/'.$context.'/');
      }
      $helper_file[]='/';
    }
    return $helper_file;
  }
    
  public function AddSugestion($sugestion){
    if(!is_null($sugestion) && is_string($sugestion)){
      array_push($this->TemplateSugestions, $sugestion);
    }
  }

  /**
   * Odnajduje sugestie renderowanych schematów
   * @param type $token
   * @param type $context
   * @return string
   */
  private function schema_sugestion($token, $context=false, $ptoken=false, $pcontext=false){
    if(!$ptoken){
      $ptoken = $this->router->getPToken();
    }
    if(!$pcontext){
      $pcontext = $this->router->getPContext();
    }
    
    $rct=$this->mimetype;
    $paths = $this->get_template_paths($token, $context, $ptoken, $pcontext, $rct);
    $subpaths = $this->get_subtemplate_paths($token, $context, $ptoken, $pcontext);
    
    //echo '-> '.$this->router->getController().'/'.$this->router->getAction().'----------------------------------------------------------------</br>';
    if(Config::get("TEMPLATE_SGTN")==1){
      //Wyszukiwanie z sugestiami
      if(isset($this->TemplateSugestions) && is_array($this->TemplateSugestions) && count($this->TemplateSugestions)>0){
      
        $for_all=Config::get("TEMPLATE_ALL");
        
        foreach($this->TemplateSugestions as $sgtn){
          foreach($paths as $path){
            if (!substr_count($path, '/'.$for_all.'/')) {
              foreach($subpaths as $subpath){
                if(Config::get("PRINT_SUGGESTIONS")==1){
                  $fullPath = $path.$subpath.$sgtn.'/'.$token.'.'.$rct.'<br/>';
                  echo $this->getPath($fullPath);
                }
                if(file_exists($path.$subpath.$sgtn.'/'.$token.'.'.$rct)){
                  if(Config::get("PRINT_HIT_SUGGESTIONS")==1){
                    $fullPath = $path.$subpath.$sgtn.'/'.$token.'.'.$rct.'<br/>';
                    echo $this->getPath($fullPath);
                  }
                  $this->set_filelist_by_extension($path.$subpath, $this->filegenExt[$rct]);
                  return array($path.$subpath.$sgtn.'/', $token.'.'.$rct);
                }
              }
            }
          }
        }
      }
    }

    //Wyszukiwanie bez sugestii
    foreach($paths as $path){
      foreach($subpaths as $subpath){
        if(Config::get("PRINT_SUGGESTIONS")==1){
          $fullPath = $path.$subpath.$token.'.'.$rct.'<br/>';
          echo $this->getPath($fullPath);
        }
        if(file_exists($path.$subpath.$token.'.'.$rct)){
          if(Config::get("PRINT_HIT_SUGGESTIONS")==1){
            $fullPath = $path.$subpath.$token.'.'.$rct.'<br/>';
            echo $this->getPath($fullPath);
          }
          $this->set_filelist_by_extension($path.$subpath, $this->filegenExt[$rct]);
          return array($path.$subpath, $token.'.'.$rct);
        }
      }
    }
  }
  
  //TODO: dodać wyrażenia regularne by dało się wyszukać tylko pełne wyrazy: np. valid i by się nie wyświetlały wtedy np. invalid
  private function getPath($path){
    $entity = Config::get('ENTITY_name');
    $lookingfor=Config::get("FINDED_IN_PAYH");
    
    if(Config::get("PRINT_LOOKINGFOR") && strpos($path, $lookingfor)){
      if(Config::get("PRINT_ENTITY") && strpos($path, '/'.$entity.'/')){
        return $path;
      }
    }
    return $path;
  }

  /**
   * Zwraca tablicę plików w katalogu wg ścieżki i tablicy rozszerzeń
   * @param type $path
   * @param type $extensions
   * @return array
   */
  private function set_filelist_by_extension($path, $extensions){
    if (is_dir($path)){
      if ($directory_files = opendir($path)){
        while (($file = readdir($directory_files)) !== false){
          $file_parts = pathinfo($file);
          if(isset($file_parts['extension'])){
            if(in_array($file_parts['extension'], $extensions)){
              //tworzymy tablicę określającą znaleziony plik z rozszrzeniem pasującym kryteriom
              $found_ext_file = array('path'=>$path, 'name'=>$file_parts['filename'], 'ext'=>$file_parts['extension']);
              if(!in_array($found_ext_file, $this->foundExtFiles)){
                $this->foundExtFiles[] = $found_ext_file;
              }
            }
          }
        }
        closedir($directory_files);
      }
    }
  }

}
