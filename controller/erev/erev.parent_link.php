<?php
/*
 * Klasa: erel_addchild
 *
 * Email: ergomicomseosem@gmail.com
 */
 
class erev_parent_link extends EREV{
  
  public function __construct($request, $router){
    parent::__construct($request, $router);
  }

  public function POST_parent_link($erid){
    $parent_to_link = array($this->data['parent_to_link']);
    if(isset($erid) && is_numeric($erid)){
      if($this->single($erid)){
        $child = DataCollector::get('EREV', $erid);
        if($child && isset($child['EREV']['ERID']) && is_numeric($child['EREV']['ERID'])){
          if(isset($this->data['parent_to_link']) && is_numeric($this->data['parent_to_link']) && $this->data['parent_to_link']>0){
            $parent = $this->call('FROM_EID', 'JSON', 'erev', 'create', $parent_to_link);
            if($parent){
              $create_parent_rel_result = $this->call('EXEC', 'JSON', 'erel', 'create', array($parent, $child));
              if($create_parent_rel_result){
                $this->redirect('/erev/view/'.$erid, 'location', 301);
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
            //throw new Exception("Wartość przekazana jako parent_to_link jest nieprawidłowa.");
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
