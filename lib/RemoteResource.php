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
    // align $attributes
    $body = json_decode( $this->remote_resource_response->body );
    $status_code = $this->remote_resource_response->status_code;

    $this->remote_resource->id = $body->id;
    $this->remote_resource->attributes = $body;
    $this->remote_resource->is_persisted = true;
    // leverage status_code for validity...
    return $body;
  }
}

class RemoteResource extends BasicRemoteResource {
  public static $site, $resource_name;
  public $id, $valid, $persisted, $status_code, $errors = array(), $attributes;
  public $raw;

  // -------------------------
  // _____ CLASS METHODS _____
  // _________________________

  // GET index
  public static function all() {
    return self::get( self::$site );
  }

  // GET index w/ params
  public static function where($attributes = array()) {
    return self::get( self::wherePath(self::$site, $attributes) );
    //$body = json_decode( $response->body )->product_images; # TODO: refactor
  }

  // GET show
  public static function find($id) {
    return self::get( self::$site."/".$id );
  }

  // POST create
  public static function create($attributes) {
    $product_image = new ProductImage($attributes);

    try {
      $response = self::post( static::$site, array(static::$resource_name => $attributes) );
      $product_image->valid = true;
      $product_image->persisted = true;
      $product_image->id = $response[static::$resource_name]["id"];
      $product_image->attributes = array_merge($product_image->attributes, $response[static::$resource_name]);
    } catch ( RemoteResourceResourceInvalid $e ) {
      $product_image->errors = $e->response["errors"];
      $product_image->valid = false;
      $product_image->persisted = false;
    }

    return $product_image;
  }

  // ----------------------------
  // _____ INSTANCE METHODS _____
  // ____________________________

  public function __construct($attributes=array()) {
    $this->attributes = $attributes;
  }

  // update attributes
  public function updateAttributes($attributes) {
    if (!$this->id) throw new Exception("ID not assigned");

    $this->attributes = array_merge(
      $this->attributes, $attributes
    );

    return $this->update();
  }

  // DELETE destroy
  public function destroy() {
    return self::delete( self::$site."/".$this->id );
  }

  // [ POST | PATCH ] save
  public function save() {
    if ($this->id) {
      return self::create($this->attributes);
    } else {
      return $this->update($this->attributes);
    }
  }

  // ----------------------------
  // _____ PRIVATE METHODS ______
  // ____________________________

  // PATCH update
  private function update() {
    return self::patch( static::$site."/".$this->id, array( static::$resource_name => $this->attributes ) );
  }

  private static function converge($response) {
    $converger = new RemoteResourceResponseConverger(self, $response);
    return $converger->converge();
  }

  private static function wherePath($path, $attributes) {
    if (!empty($attributes)) {
      $path = $path."?".http_build_query($attributes);
    }
    return $path;
  }
}
