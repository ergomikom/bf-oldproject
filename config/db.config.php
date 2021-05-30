<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Config::set("DATA", array());

Config::set("PDO",
        array(
         'sessions'  => array(
           'dsn'       =>  'mysql:host=localhost;dbname=bfsessions;encoding=utf8',
           'username'  =>  '',
           'password'  =>  ''
         ),
         'users'     => array(
           'dsn'       =>  'mysql:host=localhost;dbname=bfusers;encoding=utf8',
           'username'  =>  '',
           'password'  =>  ''
         ),
         'models'     => array(
           'dsn'       =>  'mysql:host=localhost;dbname=bfmodel;encoding=utf8',
           'username'  =>  '',
           'password'  =>  ''
         ),
         'objects'     => array(
           'dsn'       =>  'mysql:host=localhost;dbname=bfobject;encoding=utf8',
           'username'  =>  '',
           'password'  =>  ''
         ),
         'i18n'     => array(
           'dsn'       =>  'mysql:host=localhost;dbname=bfi18n;encoding=utf8',
           'username'  =>  '',
           'password'  =>  ''
         )
       )
);
