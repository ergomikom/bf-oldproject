<?php
/*
 * Klasa: erel_addchild
 *
 * Email: ergomicomseosem@gmail.com
 */
 
class erev_child_link extends EREV{
  
  public function __construct($request, $router){
    parent::__construct($request, $router);
  }

  public function POST_child_link($erid){
    if(isset($erid) && is_numeric($erid)){
      if($this->single($erid)){
        $parent = DataCollector::get('EREV', $erid);
        if($parent && isset($parent['EREV']['ERID']) && is_numeric($parent['EREV']['ERID'])){
          if(isset($this->data['child_to_link']) && is_numeric($this->data['child_to_link']) && $this->data['child_to_link']>0){
            $child = $this->call('FROM_EID', 'JSON', 'erev', 'create', array($this->data['child_to_link']));
            if($child){
              $create_child_rel_result = $this->call('EXEC', 'JSON', 'erel', 'create', array($parent, $child));
              if($create_child_rel_result){
                return $this->reroute('success/1');
              }else{
                //TODO:nie da się utworzyć powiązania, przekazujemy wyżej wynik
                return $this->reroute('error/5');
              }
            }else{
              return $this->reroute('error/4');
            } //TODO: nie utworzono nowego EREV dla E
          }else{//TODO: nieprawidłowa struktura danych w URL
            //TODO: przekierować do renderowania błędu
            return $this->reroute('error/3');
          }
        }else{
          return $this->reroute('error/2');
        }//TODO: rodzic ma nieprawidłową strukturę
      }else{
        return $this->reroute('error/1');
      } //TODO: nie można załadować ERID
    }
  }

}
