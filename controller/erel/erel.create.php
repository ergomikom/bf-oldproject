<?php
/*
 * Klasa: erel_addchild
 *
 * Email: ergomicomseosem@gmail.com
 */
 
  class erel_create extends EREL{
  
    public function __construct($request, $router){
      parent::__construct($request, $router);
    }

    public function EXEC_create($_parent, $_child){
      if($_parent && isset($_parent['EREV']['ERID']) && isset($_parent['EREV']['EID_rel'])){
        if($_child && isset($_child['EREV']['ERID']) && isset($_child['EREV']['EID_rel'])){
          $erel_creator = $this->erel_creator($_parent, $_child);         
          if($erel_creator){
            return $erel_creator;
          }else{
            return false;
          }
        }
      }
      return false;
    }

    private function erel_creator($_parent, $_child){
    
        $epa    = $_parent['EREV']['EID_rel'];
        $erpa   = $_parent['EREV']['ERID'];
        $ech    = $_child['EREV']['EID_rel'];
        $erch   = $_child['EREV']['ERID'];
        $erlcr  = $this->request->getRequestTime();
        $erlwt  = 1; //TODO: pobrać właściwy lwt;

        try{
          $result = array();
          $this->db->beginTransaction();
          $this->db->query('INSERT INTO EREL (ERLCR, EPA, ERPA, ECH, ERCH, ERLWT) VALUES (:erlcr, :epa, :erpa, :ech, :erch, :erlwt)');
          $this->db->bind(':erlcr', $erlcr);
          $this->db->bind(':epa', $epa);
          $this->db->bind(':erpa', $erpa);
          $this->db->bind(':ech', $ech);
          $this->db->bind(':erch', $erch);
          $this->db->bind(':erlwt', $erlwt);
          $this->db->execute();
          
          $erlid = $this->db->lastInsertId();
          $result['EREL']['ERLID']  = $erlid;
          $result['EREL']['ERLCR']  = $erlcr;
          $result['EREL']['EPA']    = $epa;
          $result['EREL']['ERPA']   = $erpa;
          $result['EREL']['ECH']    = $ech;
          $result['EREL']['ERCH']   = $erch;
          $result['EREL']['ERLWT']  = $erlwt;

          DataCollector::set('EREL', $erlid, $result);
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
