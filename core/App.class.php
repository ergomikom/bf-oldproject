<?php
/*
 *
 * Email: ergomicomseosem@gmail.com
 */
  // Konfiguracja ścieżek
  Config::set("CONTROLLER_PATH", APP_ROOT."/controller/");
  Config::set("DEV_CONTROLLER_PATH", Config::get("BACKEND_PATH")."controller/");
  Config::set("BACKEND_PATH", APP_ROOT."/dev/backend/");
  Config::set("FRONTEND_PATH", APP_ROOT."/dev/frontend/");
  Config::set("STORAGE_PATH", APP_ROOT."/storage/");
  Config::set("PUBLIC_EXT_PATH", APP_ROOT."/public/");
  Config::set("EXTERNAL_PUBLIC_EXT_PATH", "/");
  Config::set("THEME_PATH", Config::get("FRONTEND_PATH"));
  Config::set("ROOT_THEME_PATH", APP_ROOT."/themes/");
  Config::set("OBJECT_PATH", APP_ROOT."/core/object/");
  Config::set("CONFIG_PATH", APP_ROOT."/config/");
  Config::set("PUBLIC_PATH", APP_ROOT."/public/");
  
  Config::set("MAIN_PAGE_URL", '/e,index');
  Config::set("NOW", time());
  
  // Ładowanie klas
  require_once (Config::get("CORE_PATH")."Tools.class.php");
  require_once (Config::get("CORE_PATH")."Controller.class.php");
  require_once (Config::get("CORE_PATH")."Database.class.php");
  require_once (Config::get("CORE_PATH")."DataCollector.class.php");
  require_once (Config::get("CORE_PATH")."Request.class.php");
  require_once (Config::get("CORE_PATH")."Session.class.php");
  require_once (Config::get("CORE_PATH")."Authentication.class.php");
  require_once (Config::get("CORE_PATH")."Response.class.php");
  require_once (Config::get("CORE_PATH")."Router.class.php");
  require_once (Config::get("CORE_PATH")."Security.class.php");
  require_once (Config::get("CORE_PATH")."Template.class.php");
  require_once (Config::get("CORE_PATH")."Translate.class.php");

  class App {
    /**
    * Funkcja
    */
    public function __construct(){
      
        $sessionHandler = new SessionDBHandler();
        $request = new Request();
        $auth = new Authentication($request);
        $router = new Router($request);
        $router->router();
    }
  }

  // Start
  $app = new App();

  //Tools::pArr(DataCollector::getAll());
