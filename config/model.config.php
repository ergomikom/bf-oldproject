<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


Config::set('model_element_types', array(
  '1'=>array('name'=>'CONTAINER'),
  '2'=>array('name'=>'FIELD GROUP'),
  '3'=>array('name'=>'FIELD'),
  '4'=>array('name'=>'DATA TYPE')
));


Config::set('max_erev_parents', 1);

// -1 = infinity
// 0  = 0
// 1  = 1
// ...
// n  = n
Config::set('erev_minmax_childs', array(
  '1'=>array('max'=>-1, 'min'=>-1),
  '2'=>array('max'=>-1, 'min'=>1),
  '3'=>array('max'=>1, 'min'=>1),
  '4'=>array('max'=>0, 'min'=>0),
));

