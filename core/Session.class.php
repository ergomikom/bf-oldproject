<?php
/*
 *
 * Email: ergomicomseosem@gmail.com
 */
//require(Config::get("CORE_PATH")."Crypt.class.php");

require_once (Config::get("CONFIG_PATH")."session.config.php");

class SessionDBHandler implements SessionHandlerInterface{

  private $db = null;
  protected $session_cache_expire_minutes = 120;

  // Konstruktor
  public function __construct() {
    // Set handler to overide SESSION

    session_set_save_handler(
      array($this, "open"),
      array($this, "close"),
      array($this, "read"),
      array($this, "write"),
      array($this, "destroy"),
      array($this, "gc")
    );
    session_name('auth');
    session_start();
  }

  public function open($save_path, $session_name) {
    $this->db = new Database('sessions');
    if(isset($this->db)){
      return true;
    }
    return false;
  }

  public function close() {
    if($this->db->close()){
      return true;
    }
    return false;
  }

  // Odczytywanie danych sesji
  public function read($id) {
    DataCollector::set('session', 'id', $id);
    if(isset($this->db)) {
      $this->db->query('SELECT data FROM sessions WHERE id = :id');
      $this->db->bind(':id', $id);
      if($this->db->execute()){
        $row = $this->db->single();
        return (string)$row['data'];
      }
    }
    return '';
  }

  // Zapisywanie danych sesji
  public function write($id, $data) {
    if(isset($this->db)) {
      $access = Config::get('NOW');
      $this->db->query('REPLACE INTO sessions VALUES (:id, :access, :data)');
      $this->db->bind(':id', $id);
      $this->db->bind(':access', $access);
      $this->db->bind(':data', $data);
      if($this->db->execute()){
        return true;
      }
    }
    return false;
  }

  // Nieszczenie danych sesji
  public function destroy($id) {
    if(isset($this->db)) {
      $this->db->query('DELETE FROM sessions WHERE id = :id');
      $this->db->bind(':id', $id);
      if($this->db->execute()){
        return true;
      }
    }
    return false;
  }

  // Sprzątanie pozostawionych danych sesji
  public function gc($max) {
    if(isset($this->db)) {
      $now = Config::get('NOW');
      $old = $now - $max;
      $this->db->query('DELETE * FROM sessions WHERE access < :old');
      $this->db->bind(':old', $old);
      if($this->db->execute()){
        return true;
      }
    }
    return false;
  }

  // Nowy ID sesji
  //public function create_sid() {
  //  return session_regenerate_id();
  //}
}
