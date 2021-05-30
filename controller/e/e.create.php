<?php

/*
 * Klasa: e_create
 *
 * Email: ergomicomseosem@gmail.com
 */
 
  class e_create extends E{
    
    public function __construct($request, $router){
      parent::__construct($request, $router);
    }

    /**
    * Funkcja obsługująca żądanie typu GET dla elementu E
    */
    public function GET_create(){
      $template = $this->create_layout();
      if($this->ifRootRsp() ){
        $this->response->response($template);
      }else{
        return $template;
      }
    }
    
    /**
    * Funkcja deklarująca i inicjalizująca zmienne i renderująca 
    * szablon tworzenia elementu e
    */
    private function create_layout(){
      $layout = new Template($this->mimetype, $this->request, $this->router);
      $layout->title = "Create a new element";
      $layout->etypes = $this->etypes;
      $layout->SetStoraged(true);
      $layout->SetStorageFileName('e_create');
      return $layout->Render();
    }
    
    /**
    * Funkcja przetwarzająca dane z żądania typu POST
    */
    public function POST_create(){
      //TODO: zamienić by zmienna wchodziła przez funkcję
      if(isset($this->data['emname']) && isset($this->data['etype'])){
        $_emname = $this->data['emname'];
        $_etype = $this->data['etype'];
        
        $e_emname_exists = $this->check_emname_exists($_emname);
        if(!$e_emname_exists){
          //TODO: check valid jako funkcja
          if((is_numeric($_etype) && ($_etype>=1 && $_etype<=5))){
            $e_create_result = $this->e_creator($_emname, $_etype);
            if(isset($e_create_result) && isset($e_create_result['E']['EID'])){
              $erev_create_result = $this->call('FROM_EID', 'JSON', 'erev', 'create', array($e_create_result['E']['EID']));
              if($erev_create_result){
                return $this->reroute($this->controller, $this->action, 'success/1');
              }else{
                $eid = $e_create_result['E']['EID'];
                $e_delete_result = $this->call('EXEC', 'JSON', 'e', 'delete', array($eid));
                if($e_delete_result){
                  return $this->reroute('error/6');
                }else{
                  return $this->reroute('error/5');
                }
              }
            }else{
              $eid = $e_create_result['E']['EID'];
                $e_delete_result = $this->call('EXEC', 'JSON', 'e', 'delete', array($eid));
                if($e_delete_result){
                  return $this->reroute('error/6');
                }else{
                  return $this->reroute('error/5');
                }
            }
          }else{
            return $this->reroute('error/3');
          }
        }else{
          return $this->reroute('error/2');
        }
      }else{
        return $this->reroute('error/1');
      }
    }

    public function FROM_DATA_create($_params){
      if(isset($_params['emname']) && isset($_params['etype'])){
        $emname = $_params['emname'];
        $etype = $_params['etype'];
        $e_emname_exists = $this->check_emname_exists($emname);
        if(!$e_emname_exists){
          if((is_numeric($etype) && ($etype>=1 && $etype<=5))){
            $e_create_result = $this->e_creator($emname, $etype);
              if($e_create_result){
                $erev_create_result = $this->call('FROM_EID', 'JSON', 'erev', 'create', array($e_create_result['E']['EID']));
                if($erev_create_result){
                  $merge = array_merge($e_create_result, $erev_create_result);
                  return $merge;
                }else{
                  $eid = $e_create_result['E']['EID'];
                  $e_delete_result = $this->call('EXEC', 'JSON', 'e', 'delete', array($eid));
                }
              }
          }
        }
      }
      return false;
    }

    /**
    * Funkcja
    */
    private function e_creator($_emname, $_etype){
        try{
          $result = array();
          $this->db->beginTransaction();
          $this->db->query('INSERT INTO E (EMNAME, ETYPE) VALUES (:emname, :etype)');
          $this->db->bind(':emname', $_emname);
          $this->db->bind(':etype', $_etype);
          $this->db->execute();
          
          $eid = $this->db->lastInsertId();
          $result['E']['EID']       = $eid;
          $result['E']['EMNAME']    = $_emname;
          $result['E']['ETYPE']     = $_etype;
          $result['E']['ETYPENAME'] = $this->etypes[$_etype]['name'];
          DataCollector::set('E', $eid, $result);

          $this->db->endTransaction();
          // zwraca tablicę utworzonych elementów
          return $result;
        } catch(PDOExecption $e){
          $this->db->cancelTransaction();
          return false;
        }
      return false;
    }
  
  }
