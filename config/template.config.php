<?php
Config::set("PRINT_HIT_SUGGESTIONS", "0");
Config::set("PRINT_SUGGESTIONS", "0");
Config::set("PRINT_ENTITY", 1);
Config::set('ENTITY_name','entity');
Config::set("PRINT_LOOKINGFOR", 1);
//Config::set("FINDED_IN_PATH", "invalid");

Config::set("TEMPLATE_PARENT", "1");
Config::set("TEMPLATE_SGTN", "1");

Config::set("template_check_for_all", "0");

Config::set("THEME",'bootstrap');

// 0 - no cached, no cache used
// 1 - all request generate new theme file
// 2 - all request check if theme file exists
Config::set("CACHE_theme",'0');

// 0 - no cleaned tempate cache files
// 1 - removed unused cache files
Config::set("CACHE_autoclean",'1');

Config::set('possible_template_files', array());
Config::set('used_template_files', array());


//Domyślny token inicjujący renderowanie szablonu
Config::set("ROOT_TOKEN","root");

Config::set("TEMPLATE_ALL","all");
