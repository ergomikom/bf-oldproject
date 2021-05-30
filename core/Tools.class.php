<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Narzędziowe funkcje statyczne
 *
 * *
 * Wzorzec projektowy (Software design): Property
 */
class Tools {

  public static function reduce_duplicate_in_array($array){
    if(is_array($array)){
      $reduced_array = array();
      foreach($array as $row){
        if(!in_array($row, $reduced_array)){
          $reduced_array[] = $row;
        }
      }
      return $reduced_array;
    }else{
      return false;
    }
  }
  
  //Wydruk tablicy
  public static function pArr($array){
    echo print_r($array, true);
  }
  
  //Liczba elementów tablicy
  public static function cArr($array){
    echo count($array);
  }
  
}
