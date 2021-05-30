<?php

/*
 * Klasa: e_delete
 *
 * Email: ergomicomseosem@gmail.com
 */
 
  class e_delete extends E{
    
    public function __construct($request, $router){
      parent::__construct($request, $router);
    }
    
    /**
    * Funkcja
    */
    public function GET_delete($eid){
      if($this->single($eid)){
        if($this->canbedeleted){
          $template = $this->delete_layout();
          if($this->ifRootRsp()){
            $this->response->response($template);
          }else{
            return $template;
          }
        } else {
          return $this->reroute('http/405');
        }
      } else {
        return $this->reroute('http/404');
      }
    }

    /**
    * Funkcja
    */
    public function DELETE_delete($eid){
      if($this->single($eid)){
        if($this->canbedeleted){
          if(!$this->delete($eid)){
            return $this->reroute('error/notdeleted');
          }else{
            return $this->reroute('success/edeleted');
          }
        }else{
          return $this->reroute('error/cnbd');
        }
      }
      return $this->reroute('http/404');
    }
    
    /**
    * Funkcja
    */
    private function delete_layout(){
      $layout = new Template($this->mimetype, $this->request, $this->router);
      $layout->title      = "Do you want to delete the model: ".$this->emname.".";
      $layout->description = "This operation is <b style=\"color:red\">irreversible</b>.";
      $layout->eid        = $this->eid;
      $layout->emname     = $this->emname;
      $layout->etype      = $this->etype;
      $layout->etypename = str_replace(' ', '_', strtolower($this->etypes[$this->etype]['name']));
      $layout->cbd = random_int(0,1);//$this->canbedeleted ? 1 : 0;
      $layout->AddSugestion('cbd'.$layout->cbd);
      $layout->SetStoraged(false);
      return $layout->Render();
    }

    //TODO: czy to potrzebne 
    public function EXEC_delete($eid){
      if($eid && is_numeric($eid)){
        $e_delete_result = $this->destroy($eid);
        if($e_delete_result){
          return true;
        }
      }
      return false;
    }

    /**
    * Funkcja
    */
    private function delete($eid){
      try{
        $this->db->beginTransaction();
        $this->db->query('DELETE FROM E WHERE EID=:eid');
        $this->db->bind(':eid', $eid);
        $this->db->execute();
        $this->db->query('DELETE FROM EREV WHERE EID_rel=:eid');
        $this->db->bind(':eid', $eid);
        $this->db->execute();
        $this->db->endTransaction();
        DataCollector::destroy('E', $eid);
        //FIXME: czy na pewno?
        //TODO: dodać usuwanie danych powiązanych z pamięci po usunięciu o ile to potrzebne
        return true;
      }catch(PDOExecption $e){
        $this->db->cancelTransaction();
        return false;
      }
    }

  }
