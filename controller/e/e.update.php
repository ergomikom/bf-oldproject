<?php

/*
 * Klasa: e_update
 *
 * Email: ergomicomseosem@gmail.com
 */
 
  class e_update extends E{
    
    public function __construct($request, $router){
      parent::__construct($request, $router);
    }
    
    /**
    * Funkcja
    */
    public function GET_update($eid){
      if($this->single($eid)){
        if($this->canbeupdated){
          $template = $this->update_layout();
          if($this->ifRootRsp()){
            $this->response->response($template);
          }else{
            return $template;
          }
        }else{
          //TODO:renderuj z szablonu
          return $this->reroute('error/cnbu');
        }
      }else{
        //TODO:renderuj z szablonu
        return $this->reroute('http/404');
      }
    }
    
    /**
    * Funkcja
    */  
    private function update_layout(){
      $layout = new Template($this->mimetype, $this->request, $this->router);
      $layout->title = "Edit model ".$this->emname.".";
      $layout->eid = $this->eid;
      $layout->emname = $this->emname;
      $layout->etype = $this->etype;
      $layout->etypename = str_replace(' ', '_', strtolower($this->etypes[$this->etype]['name']));
      $layout->cbu = random_int(0,1); //$this->canbeupdated ? 1 : 0;
      
      $layout->AddSugestion('cbu'.$layout->cbu);
      $layout->SetStoraged(true);
      $layout->SetStorageFileName($layout->eid);
      return $layout->Render();
    }
    
    /**
    * Funkcja
    */
    public function PUT_update($eid){
      //echo 'PUT update: '.$this->router->getRouterSignature().'<br/>';
      if($this->single($eid)){
        if($this->canbeupdated){
          if(isset($this->data['emname']) && isset($this->data['etype'])){
            if($this->data['emname']==$this->emname){
              return $this->reroute('http/404');
            }else{
              if(!$this->check_emname_exists($this->data['emname'])){
                if($this->e_update($eid, $this->data['emname'], $this->data['etype'])){
                  return $this->reroute('success/eupdatesuccess');
                }else{
                  return $this->reroute('error/eupdateerror');
                }
              }else{
                return $this->reroute('error/emnameexists');
              }
            }
          }else{
            return $this->reroute('error/bputdata');
          }
        }else{
          return $this->reroute('error/cnbu');
        }
      }else{
        return $this->reroute('http/404');
      }
    }

    /**
    * Funkcja
    */
    private function e_update($eid, $emname, $etype){
      try {
        $this->db->beginTransaction();
        $this->db->query('UPDATE E SET EMNAME=:emname, ETYPE=:etype WHERE EID=:eid');
        $this->db->bind(':eid', $eid);
        $this->db->bind(':emname', $emname);
        $this->db->bind(':etype', $etype);
        $this->db->execute();
        $this->db->endTransaction();
        return true;
      } catch(PDOExecption $e){
        $this->db->cancelTransaction();
        return false;
      }
    }

  }
