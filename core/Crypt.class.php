<?php
/*
 *
 * Email: ergomicomseosem@gmail.com
 */
class Crypt {
  private $algorithm;
  private $mode;
  private $random_source;

  private $cleartext;
  private $ciphertext;
  private $iv;

  /**
    * Funkcja
     */
  public function getClear() {
    return $this->cleartext;
  }
  
  /**
    * Funkcja
     */
  public function getCipher() {
    return $this->ciphertext;
  }

  /**
    * Funkcja
     * @param type algorithm
     * @param type mode
     * @param type random_sourve
     */
  public function __construct($algorithm = MCRYPT_BLOWFISH, $mode = MCRYPT_MODE_CBC, $random_source = MCRYPT_DEV_URANDOM) {
    $this->algorithm = $algorithm;
    $this->mode = $mode;
    $this->random_source = $random_source;
  }

  /**
    * Funkcja
     */
  public function generate_iv() {
    $this->iv = mcrypt_create_iv(mcrypt_get_iv_size($this->algorithm, $this->mode), $this->random_source);
  }

  /**
    * Funkcja
     */
  public function encrypt() {
    $this->ciphertext = mcrypt_encrypt($this->algorithm, $_SERVER['CRYPT_KEY'], $this->cleartext, $this->mode, $this->iv);
  }

  /**
    * Funkcja
     */
  public function decrypt() {
    $this->cleartext = mcrypt_decrypt($this->algorithm, $_SERVER['CRYPT_KEY'], $this->cleartext, $this->mode, $this->iv);
  }

}
