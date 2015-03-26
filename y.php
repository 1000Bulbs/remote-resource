<?php
require_once 'Requests/Requests.php';
Requests::register_autoloader();

class BasicRemoteResource {
  private $client;

  // GET
  public static function get($path) {
    return Requests::get($path, self::headers());
  }

  // POST
  public static function post($path, $hash_of_attributes = array()) {
    return Requests::post($path, $hash_of_attributes, self::headers());
  }

  // PATCH
  public static function patch($path, $hash_of_attributes = array()) {
    return Requests::patch($path, $hash_of_attributes, self::headers());
  }

  // DELETE
  public static function delete($path) {
    return Requests::delete($path, self::headers());
  }

  // SITE
  public static function site() {
    throw new Exception('Not implemented');
  }

  protected static function headers() {
    $encoded = base64_encode("daniel:password"); // TODO: refactor
    return array('Content-Type' => 'application/json', 'Authorization' => "Basic ".$encoded);
  }
}

class RemoteResource extends BasicRemoteResource {
  public $id;
  public $hash_of_attributes = array();

  // -------------------------
  // _____ CLASS METHODS _____
  // _________________________

  // GET index
  public static function all() {
    return self::get( self::site() );
  }

  // GET index w/ params
  public static function where($hash_of_attributes = array()) {
    return self::get( self::site(), $hash_of_attributes );
  }

  // GET show
  public static function find($id) {
    return self::get( (self::site()."/".$id) );
  }

  // POST create
  public static function create($hash_of_attributes = array()) {
    return self::post( self::site(), $hash_of_attributes );
  }

  // ----------------------------
  // _____ INSTANCE METHODS _____
  // ____________________________

  // PATCH update
  public function update($hash_of_attributes = array()) {
    return self::patch( (self::site()."/".$this->id), $hash_of_attributes );
  }

  // DELETE destroy
  public function destroy {
    return self::delete( (self::site()."/".$this->id), $hash_of_attributes );
  }

  public function save {
    if ($this->id) {
      return self::create($this->hash_of_attributes);
    else {
      return $this->update($this->hash_of_attributes);
    }
  }
}

class ProductImageResource extends RemoteResource {
  public static function site() {
    "http://localhost:3006/product_images"
  }

  //public static function clone_image($id, $product_id) {
   // return self::get( (self::site()."/".$id."/clone/".$product_id) );
  //}
}
