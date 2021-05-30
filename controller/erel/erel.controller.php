<?php

/*
 *
 * Email: ergomicomseosem@gmail.com
 */
 
  require_once (Config::get("CONFIG_PATH")."model.config.php");

  class EREL extends Controller{
    //DB link
    protected $db;
    //Relation id
    protected $erlid;
    //Element creation time
    protected $erlcr;
    //Element e parent
    protected $epa;
    //Element erev parent
    protected $erpa;
    //Element e child
    protected $ech;
    //Element erev child
    protected $erch;
    //Element erel weight
    protected $erlwt;
    

    public function __construct($request, $router){
      parent::__construct($request, $router);
      $this->db = new Database('models');
      $this->etypes = Config::get('model_element_types');
    }

    /**
    * Funkcja
    */
    public function single($erlid){
      if($erlid && is_numeric($erlid) && $erlid>0 && $this->db){
        $row = array();
        if(!DataCollector::name_id_exist('EREL',$erlid)){
          $this->db->query('
            SELECT ERLID, ERLCR, EPA, ERPA, ECH, ERCH, ERLWT 
            FROM EREL 
            WHERE ERLID=:erlid'
          );
          $this->db->bind(':erlid', $erlid);
          if($this->db->execute() && $this->db->rowCount()){
            $row = $this->db->single();
            $result['EREL']['ERLID']  = $row['ERLID'];
            $result['EREL']['ERLCR']  = $row['ERLCR'];
            $result['EREL']['EPA']    = $row['EPA'];
            $result['EREL']['ERPA']   = $row['ERPA'];
            $result['EREL']['ECH']    = $row['ECH'];
            $result['EREL']['ERCH']   = $row['ERCH'];
            $result['EREL']['ERLWT']  = $row['ERLWT'];

            DataCollector::set('EREL', $erlid, $result);
          }else{
            return false;
          }
        }else{
          $result= DataCollector::get('EREL', $erlid);
        }

        $this->erlid    = $result['EREL']['ERLID'];
        $this->erlcr    = $result['EREL']['ERLCR'];
        $this->epa      = $result['EREL']['EPA'];
        $this->erpa     = $result['EREL']['ERPA'];
        $this->ech      = $result['EREL']['ECH'];
        $this->erch     = $result['EREL']['ERCH'];
        $this->erlwt    = $result['EREL']['ERLWT'];
        
        return true;
      }
      return false;
    }

  }
