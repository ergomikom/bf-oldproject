<?php
/*
 * Klasa: erel_addchild
 *
 * Email: ergomicomseosem@gmail.com
 */
 
  class erev_cl_new_child extends EREV{
  
    public function __construct($request, $router){
      parent::__construct($request, $router);
    }
    
    /**
    * Funkcje
    */
    public function GET_cl_new_child($erid){
      if($this->single($erid)){
        if($this->mac){
          $rendered_template = $this->cl_new_child();
          return $rendered_template;
        }
      }else{
        return $this->reroute('error/1');
      }
    }

    private function cl_new_child(){
      $layout = new Template($this->mimetype, $this->request, $this->router);
      $layout->erid = $this->erid;
      $layout->child_etype = $this->etype+1;
      return $layout->Render();
    }

    public function POST_cl_new_child($erid){
      if($this->single($erid)){
        if($this->mac){
          if(isset($this->data['emname']) && isset($this->data['etype'])){
            $created_child = $this->call('FROM_DATA', 'JSON', 'e', 'create', array($this->data));
            if($created_child){
              $parent = DataCollector::get('EREV', $erid);
              $child_erid = $created_child['EREV']['ERID'];
              $child = DataCollector::get('EREV', $child_erid);
              $create_child_rel_result = $this->call('EXEC', 'JSON', 'erel', 'create', array($parent, $child));
              if($create_child_rel_result){
                return $this->reroute('success/1');
              }else{
                //TODO: usunąć element z którym po utworzeniu nie dało się utworzyć relacji
                return $this->reroute('error/4'); // nie można utworzyć relacji
              }
            }else{
              return $this->reroute('error/3'); // element cannot be created
            }
          }else{
            return $this->reroute('error/2'); // nie można dodać kolejnego potomka
          }
        }else{
          return $this->reroute('error/1'); //
        }
      }
    }

  }
