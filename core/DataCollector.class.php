<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Konfiguracja zmiennych globalnych aplikacji
 *
 * *
 * Wzorzec projektowy (Software design): Property
 */
class DataCollector {
  private static $data = array();

  /**
    * Funkcja
    */
  public static function set($name, $id, $array) {
      self::$data[$name][$id]=$array;
  }

  public static function destroy($name, $id){
    unset(self::$data[$name][$id]);
  }

  //TODO: dodać usuwanie danych powiązanych z pamięci po usunięciu o ile to potrzebne
  public static function destroyAll($name, $id, $value){

  }
  
  public static function isSet($name, $id, $variable){
    if(isset(self::$data[$name][$id][$variable])){
      return true;
    }
    return false;
  }
  
  /**
    * Funkcja
    */
  public static function get($name, $id) {
    if(isset(self::$data[$name][$id])){
      return self::$data[$name][$id];
    }else{
      return false;
    }
  }
  
  public static function getAll(){
    return self::$data;
  }

  /**
    * Funkcja
    */
  public static function name_exist($name) {
      return isset(self::$data[$name]);
  }
  
  public static function name_id_exist($name,$id) {
      return isset(self::$data[$name][$id]);
  }
}
