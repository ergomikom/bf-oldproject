<?php

/*
 * Klasa: e_create
 *
 * Email: ergomicomseosem@gmail.com
 */
 
  class erev_create extends EREV{
    
    public function __construct($request, $router){
      parent::__construct($request, $router);
    }

    public function FROM_EID_create($eid){
      if(isset($eid) && is_numeric($eid)){
        //TODO: warto dodać dodatkowe testy dla powyższeg if
        $erev_create_result = $this->erev_creator($eid);
        if($erev_create_result){
          return $erev_create_result;
        }else{
          return false;
        }
      }
    }

    private function erev_creator($_eid_rel){
        try{
          $result = array();
          $eid_rel = $_eid_rel;
          $this->db->beginTransaction();
            //TODO: przenieść utworzenie EREV do erev controllera
            $this->db->query('INSERT INTO EREV (EID_rel) VALUES (:eid_rel)');
            $this->db->bind(':eid_rel', $eid_rel);
            $this->db->execute();
            $erid = $this->db->lastInsertId();
            $result['EREV']['ERID']     = $erid;
            $result['EREV']['ERCR']     = $this->request->getRequestTime();
            $result['EREV']['EID_rel']  = $eid_rel;
            $result['EREV']['ERV']      = 0;
            $result['EREV']['ERC']      = 0;
            $result['EREV']['ERU']      = 0;
            DataCollector::set('EREV', $result['EREV']['ERID'], $result);
          $this->db->endTransaction();
          return $result;
        } catch(PDOExecption $e){
          $this->db->cancelTransaction();
        }
      return false;
    }
    
  }
