<?php
require_once 'lib/BasicRemoteResource.php';

class RemoteResourceResponseConverger {
  public $remote_resource;
  public $remote_resource_response;

  public function __construct(RemoteResource $remote_resource, $remote_resource_response) {
    $this->remote_resource = $remote_resource;
    $this->remote_resource_response = $remote_resource_response;
  }

  public function converge() {
    // Add $is_valid, and $is_persisted, and $status_code, $id
    // align $hash_of_attributes
    $body = json_decode( $this->remote_resource_response->body );
    $status_code = $this->remote_resource_response->status_code;

    $this->remote_resource->id = $body->id;
    $this->remote_resource->hash_of_attributes = $body;
    $this->remote_resource->is_persisted = true;
    // leverage status_code for validity...
    return $body;
  }
}

class RemoteResource extends BasicRemoteResource {
  public $id, $is_valid, $is_persisted, $status_code, $errors;
  public $hash_of_attributes = array();
  public static $site = "http://localhost:3006/product_images";
  public static $resource_name = 'product_image';

  // -------------------------
  // _____ CLASS METHODS _____
  // _________________________

  // GET index
  public static function all() {
    return self::get( self::$site );
  }

  // GET index w/ params
  public static function where($hash_of_attributes = array()) {
    return self::get( self::wherePath(self::$site, $hash_of_attributes) );
    //$body = json_decode( $response->body )->product_images; # TODO: refactor
  }

  // GET show
  public static function find($id) {
    return self::get( self::$site."/".$id );
  }

  // POST create
  public static function create($hash_of_attributes = array()) {
    return self::post( self::$site, $hash_of_attributes );
  }

  // ----------------------------
  // _____ INSTANCE METHODS _____
  // ____________________________

  // PATCH update
  public function update($hash_of_attributes = array()) {
    return self::patch( self::$site."/".$this->id, array( self::$resource_name => $hash_of_attributes ) );
  }

  // DELETE destroy
  public function destroy() {
    return self::delete( self::$site."/".$this->id );
  }

  // [ POST | PATCH ] save
  public function save() {
    if ($this->id) {
      return self::create($this->hash_of_attributes);
    } else {
      return $this->update($this->hash_of_attributes);
    }
  }

  // ----------------------------
  // _____ PRIVATE METHODS ______
  // ____________________________

  private static function converge($response) {
    $converger = new RemoteResourceResponseConverger(self, $response);
    return $converger->converge();
  }

  private static function wherePath($path, $hash_of_attributes) {
    if (!empty($hash_of_attributes)) {
      $path = $path."?".http_build_query($hash_of_attributes);
    }
    return $path;
  }
}
