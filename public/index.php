<?php
/*
 *
 * Email: ergomicomseosem@gmail.com
 */
  if (version_compare(phpversion(), '7.0.0', '<')===true) {
    echo  '<h2>Whoops, it looks like you have an invalid PHP version.</h2>';
    exit;
  }

  define('APP_ROOT', '/var/www/html/projekt');
  define('DOMAIN_NAME', 'http://www.dev1.pl');
  require_once (APP_ROOT."/core/"."Config.class.php");
  Config::set("APP_ENV", "dev");

  if(Config::get("APP_ENV") == 'dev'){
      error_reporting(E_ALL);
      ini_set("display_errors", 1);
  }

  Config::set('SITE_PATH', APP_ROOT);
  Config::set("CORE_PATH", APP_ROOT."/core/");

  // Inicjalizacja systemu
  require Config::get("CORE_PATH").'App.class.php';
