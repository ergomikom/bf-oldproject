<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Config::set('http_method_accepted', array('GET', 'POST', 'PUT', 'DELETE'));
Config::set('http_method_req_has_body', array('POST', 'PUT', 'CONNECT', 'OPTIONS','PATCH'));
Config::set('http_method_res_has_body', array('GET', 'POST', 'PUT', 'DELETE', 'CONNECT', 'OPTIONS', 'TRACE', 'PATCH'));
Config::set('http_method_save', array('GET', 'HEAD', 'OPTIONS', 'TRACE'));
Config::set('http_method_not_save', array('POST', 'PUT', 'DELETE', 'CONNECT', 'PATCH'));
Config::set('http_method_idempotent', array('GET', 'HEAD', 'PUT', 'DELETE', 'OPTIONS', 'TRACE'));
Config::set('http_method_cacheable', array('GET', 'HEAD', 'POST', 'PATCH'));
