<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 
require (Config::get("CONFIG_PATH")."authentication.config.php");

class Authentication {
  private $sessionSalt;
  private $passwordSalt;
  private $dbu;
  private $cookieAuth;
  private $sessionAuth;
  private $cookie;
  private $post;

  private $request;

  private $now;
  private $lifetime;
  private $isLogged;

  private static $userid;
  private static $username;
  private static $userlang;

  /**
    * Funkcja
    */
  public function __construct(Request $request) {
    $this->request = $request;
    $this->cookie = $this->request->getCookie();
    $this->now = Config::get('NOW');
    $this->sessionSalt = Config::get("sessionSalt");
    $this->passwordSalt = Config::get("passwordSalt");
    $this->lifetime = Config::get("sessionLifeTime");

    if(isset($this->cookie['auth'])) {
      $this->dbu = new Database('users');
      $this->post = $this->request->getData();
      $this->cookieAuth = $this->cookie['auth'];
      $this->sessionAuth = session_id();

      if(isset($_SESSION['timeout']) && time() > $_SESSION['timeout']) {
        session_unset();
        session_destroy();
        session_start();
        session_regenerate_id(true);
        $_SESSION['uid'] = 0;
      }
      if(!isset($_SESSION['uid'])) {
        $_SESSION['uid'] = 0;
      }

      $this->isLogged = $this->is_logged();

      if(!$this->isLogged) {
        if($this->check_login_form()) {
          Response::redirect(Config::get("MAIN_PAGE_URL"), 'location', 200);
        }
      }
      $this->setSessionTimeout();
    }
  }

  /**
    * Funkcja
    */
  private function setSessionTimeout() {
    $timeout = $this->now + $this->lifetime;
    $_SESSION['timeout'] = $timeout;
  }

  /**
    * Funkcja
    */
  private function is_logged() {
      $uid = $_SESSION['uid'];
      if($this->cookieAuth == $this->sessionAuth) {
        $this->dbu->query('SELECT userid, username, password, status, lang FROM users WHERE userid=:uid and status = 1');
        $this->dbu->bind(':uid', $uid);
        if($this->dbu->execute() && $this->dbu->rowCount()){
          $row = $this->dbu->single();
          $this->setUserSession($row);
          return true;
        }
      }
    return false;
  }

  /**
    * Funkcja
    */
  private function check_login_form() {
    if(isset($this->post['username']) && isset($this->post['password'])) {
      $username = $this->post['username'];
      $password = md5($this->post['password']);
      $this->dbu->query('SELECT userid, username, password FROM users WHERE username = :username and password = :password and status = 1');
      $this->dbu->bind(':username', $username);
      $this->dbu->bind(':password', $password);
      if($this->dbu->execute() && $this->dbu->rowCount()){
        $row = $this->dbu->single();
        $this->setUserSession($row);
        return true;
      }
    } else {
      $_SESSION['uid'] = 0;
    }
    return false;
  }

  /**
    * Funkcja
    */
  public function isLogged() {
    return $this->isLogged;
  }

  /**
    * Funkcja
    */
  private function setUserSession ($row){
    $_SESSION['uid'] = $row['userid'];
    Config::set("_AUTH_userid", $row['userid']);
    Config::set("_AUTH_username", $row['username']);
    Config::set("_AUTH_userlang", $row['lang']);
    
    self::$userid = $row['userid'];
    self::$username = $row['username'];
    self::$userlang = $row['lang'];
    $this->setSessionTimeout();
  }

  /**
    * Funkcja
    */
  public static function getUserID() {
    return self::$userid;
  }
  
  /**
    * Funkcja
    */
  public static function getUsername() {
    return self::$username;
  }
  
  /**
    * Funkcja
    */
  public static function getUserlang() {
    return self::$userlang;
  }

  /**
    * Funkcja
    */
  private function unsetUserSession() {
    $_SESSION['uid'] = 0;
  }

  /**
    * Funkcja
    */
  private function getIdentifier() {
    return md5($this->$sessionSalt.$this->username.$this->$sessionSalt);
  }

  /**
    * Funkcja
    */
  private function getRandomHash() {
    return md5(uniqid(rand(), TRUE));
  }

  /**
    * Funkcja
    */
  private function getSessionTimeout() {
    return time() + 60 * 60 * 24 * 7;
  }

  /**
    * Funkcja
    */
  private function getPasswordHash($nonHashPassword) {
    return md5($this->passwordSalt.$nonHashPassword.$this->passwordSalt);
  }

  /**
    * Funkcja
    */
  private function get_new_auth2_cookie() {
    $identifier = $this->getIdentifier();
    $token = $this->getRandomHash();
    $timeout = $this->getSessionTimeout();
    $auth2 = "$identifier:$token";
    setcookie('auth2', $auth2, $timeout);
    return $auth2;
  }

}
