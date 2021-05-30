<?php
/*
 *
 * Email: ergomicomseosem@gmail.com
 */

  class erev_cl_new_parent extends EREV{

    public function __construct($request, $router){
      parent::__construct($request, $router);
    }

    public function GET_cl_new_parent($erid){
      if($this->single($erid)){
        if($this->map){
          $rendered_template = $this->cl_new_parent();
          return $rendered_template;
        }
      }else{
        return $this->reroute('error/1');
      }
    }

    private function cl_new_parent(){
      $layout = new Template($this->mimetype, $this->request, $this->router);
      $layout->erid = $this->erid;
      if($this->etype-1==0){
        $layout->parent_etype = 1;
      }else{
        $layout->parent_etype = $this->etype-1;
      }
      return $layout->Render();
    }

    public function POST_cl_new_parent($child_erid){
      if($this->single($child_erid)){
        if($this->map){
          if(isset($this->data['emname']) && isset($this->data['etype'])){
            //DESC: tu tworzony jest E i pierwszy jego EREV
            //Zwracana jest tablica danych z utworzonych rekordów
            $created_parent = $this->call('FROM_DATA', 'JSON', 'e', 'create', array($this->data));
            if($created_parent){
              $child = DataCollector::get('EREV', $child_erid);
              $parent_erid = $created_parent['EREV']['ERID'];
              $parent = DataCollector::get('EREV', $parent_erid);
              $create_parent_rel_result = $this->call('EXEC', 'JSON', 'erel', 'create', array($parent, $child));
              if($create_parent_rel_result){
                return $this->reroute('success/1');
              }else{
                //TODO:nie da się utworzyć powiązania, przekazujemy wyżej wynik
                return $this->reroute('error/4');
              }
              //echo print_r($createRel_resultt,1);
            }else{
              //TODO: nie da się utworzyć elementu lub jego rewizji.
              return $this->reroute('error/3');
            }
          }else{
            //brak danych z formularza
            return $this->reroute('error/2');
          }
        }else{
          //TODO: dodać renderowanie prawidłowego błędus
          return $this->reroute('error/1');
        }
      }
    }

  }
