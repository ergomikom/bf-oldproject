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
class Config {
  private static $conf = array();

  /**
    * Funkcja
    */
  public static function set($name, $value) {
      self::$conf[$name]=$value;
  }

  /**
    * Funkcja
    */
  public static function get($name) {
    if(isset(self::$conf[$name])){
      return self::$conf[$name];
    }else{
      return null;
    }
  }

  /**
    * Funkcja
    */
  public static function exist($name) {
      return isset(self::$conf[$name]);
  }
}
