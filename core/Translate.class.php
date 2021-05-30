<?php
class Translate{
    private $dbt;
    private $di18n;

    public function __construct(){
        $this->dbt = new Database('i18n');
        $this->di18n = Config::get('Di18n');
    }

    public function tt($string, $i18n){
        if($this->di18n!=$i18n){
            return $this->get_translation($string, $i18n);
        }else{
            return $string;
        }
    }

    private function get_translation($string, $i18n){
        $dt = $this->check_tt_exists($string);
        $dtid = $dt['tid'];
        if($dtid){
            $translation=$this->check_transation_exists($dtid, $i18n);
            if($translation){
                return $translation;
            }
            return $string;
        }else{
            if($this->insert_tt($string)){
                return $string;
            }
            return false;
        }
    }

    private function check_transation_exists($dtid, $i18n){
        $this->dbt->query('SELECT * FROM translations WHERE ttid=:dtid AND ti18n=:i18n');
        $this->dbt->bind(':dtid', $dtid);
        $this->dbt->bind(':i18n', $i18n);
        $translation = $this->dbt->single();
        if($translation){
            return $translation['tstring'];
        }
        return false;
    }

    private function check_tt_exists($string){
        $this->dbt->query('SELECT * FROM translations WHERE tstring=:string');
        $this->dbt->bind(':string', $string);
        return  $this->dbt->single();
    }

    private function insert_tt($string){
        $this->dbt->query('INSERT INTO translations (ti18n, tstring) VALUES (:i18n, :string)');
        $this->dbt->bind(':i18n', $this->di18n);
        $this->dbt->bind(':string', $string);
        $this->dbt->execute();
        return $this->dbt->lastInsertId();
    }

}
