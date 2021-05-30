<?php
/*
 *
 * Email: ergomicomseosem@gmail.com
 */
 
  class EREV extends Controller{
    //DB link
    protected $db;
    //Elememnt revision id
    protected $erid;
    //Elememnt revision create time
    protected $ercr;
    //Elememnt revision relation to element id
    protected $eid_rel;
    //Elememnt revision verified status
    protected $erv;
    //Elememnt revision confirm status
    protected $erc;
    //Elememnt revision used status
    protected $eru;
    //Tablica rodziców
    protected $parents;
    protected $parents_type;
    protected $count_parents;
    //Tablica dzieci
    protected $childs;
    protected $childs_type;
    protected $count_childs;
    
    protected $map;
    protected $mac;
    //etype from e
    protected $etype;

    //TODO: zbudować API do tego EREV
    
    // PRZEROBIĆ TEN PLIK
    public function __construct($request, $router){
      parent::__construct($request, $router);
      $this->db = new Database('models');
    }
    
    //TODO: dodać obsługę klasy kolektora danych
    protected function single($erid){
      if($erid && is_numeric($erid) && $erid>0 && $this->db){
        if(!DataCollector::name_id_exist('EREV',$erid)){
          $this->db->query('
            SELECT ERID, ERCR, EID_rel, ERV, ERC, ERU, ETYPE, EMNAME
              FROM EREV 
              JOIN E ON E.EID = EREV.EID_rel
              WHERE ERID=:erid'
            );
            $this->db->bind(':erid', $erid);
            
          if($this->db->execute() && $this->db->rowCount()){
            $row = $this->db->single();
            $result['EREV']['ERID'] = $row['ERID'];
            $result['EREV']['ERCR'] = $row['ERCR'];
            $result['EREV']['EID_rel'] = $row['EID_rel'];
            $result['EREV']['ERV'] = $row['ERV'];
            $result['EREV']['ERC'] = $row['ERC'];
            $result['EREV']['ERU'] = $row['ERU'];
            $result['EREV']['ETYPE'] = $row['ETYPE'];
            $result['EREV']['EMNAME'] = $row['EMNAME'];
            DataCollector::set('EREV', $erid, $result);
          }else{
            return false;
          }
        }else{
          $result = DataCollector::get('EREV', $erid);
        }
        
        //Definicja danych obiektu
        $this->erid           = $result['EREV']['ERID'];
        $this->ercr           = $result['EREV']['ERCR'];
        $this->eid_rel        = $result['EREV']['EID_rel'];
        $this->erv            = $result['EREV']['ERV'];
        $this->erc            = $result['EREV']['ERC'];
        $this->eru            = $result['EREV']['ERU'];
        $this->parents        = $this->getParents($this->erid);
        $this->count_parents  = count($this->parents);
        $this->childs         = $this->getChilds($this->erid);
        $this->count_childs   = count($this->childs);
        $this->etype          = $result['EREV']['ETYPE'];
        $this->emname         = $result['EREV']['EMNAME'];
        $this->map            = $this->mayAddParent();
        $this->mac            = $this->mayAddChild();
        return true;
      }
      return false;
    }
    
    
    private function getParents($erid){
      $result = array();
        $query = 'SELECT * FROM EREL WHERE ERCH=:erev';
        $this->db->query($query);
        $this->db->bind(':erev', $erid);
        if($this->db->execute() && $this->db->rowCount()){
          $rows = $this->db->resultset();
          while($row = array_shift($rows)){
            $result[] = $row;
          }
        }
      return $result;
    }

    private function getChilds($erid){
      $result = array();
          $query = 'SELECT * FROM EREL WHERE ERPA=:erev';
          $this->db->query($query);
          $this->db->bind(':erev', $erid);
          if($this->db->execute() && $this->db->rowCount()){
            $rows = $this->db->resultset();
            while($row = array_shift($rows)){
              $result[] = $row;
            }
          }
      return $result;
    }

    // czy zwalidowany / prawidłowy
    protected function isValid(){
      //sprawdzenie stanu erv
      return $this->erv;
    }

    // czy potwierdzony
    protected function isConfirm(){
      //sprawdzenie stanu erc
      return $this->erc;
    }

    // czy jest w użyciu
    protected function isUsed(){
      //sprawdzenie stanu eru
      return $this->eru;
    }
    
    protected function getLast(){}
    protected function getFirst(){}
    protected function getFromDate(){}
    
    //sprawdzenie stanu możliwości dodania rodzica
    protected function mayAddParent(){
      $etype = $this->etype;
      $container = 1;
      $fieldgroup = 2;
      $field = 3;
      $datatype = 4;
      $another = array(2,3);
      if($etype){
        switch($etype){
          case ($etype==$container):
              return false;
            break;
          case ($etype==$fieldgroup):
              if($this->count_parents==0){
                return true;
              }
            break;
          case ($etype==$field):
              if($this->count_parents==0){
                return true;
              }
            break;
          case 4:
              return true;
            break;
        }
      }
      return false;
    }
    
    //sprawdzenie stanu możliwości dodania dziecka
    protected function mayAddChild(){
      $etype = $this->etype;
      $another = array(2,3);
      if($etype){
        switch($etype){
          case ($etype==1):
            return true;
            break;
          case ($etype==2):
            return true;
            break;
          case ($etype==3):
          //TODO: by dało się dodać wiele takich samych modeli, ale żeby nie dało się kolejnych valid
            if($this->count_childs==0){
              return true;
            }
            break;
          }
      }
      return false;
    }

  }
