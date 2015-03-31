<?php
require_once 'lib/BasicRemoteResource.php';

class RemoteResource extends BasicRemoteResource {
  public static $site, $resource_name;
  protected $id, $errors = array(), $persisted = false, $valid = false, $attributes;

  // -------------------------
  // _____ CLASS METHODS _____
  // _________________________

  // GET index
  public static function all() {
    return self::get( self::$site );
  }

  // GET index w/ params
  public static function where($attributes = array()) {
    return self::get( self::wherePath(static::$site, $attributes) );
  }

  // GET show
  public static function find($id) {
    $response = self::get( static::$site."/".$id );

    $product_image = new ProductImage;
    $product_image->persisted = true;
    $product_image->id = $response[static::$resource_name]["id"];
    $product_image->attributes = $response[static::$resource_name];

    return $product_image;
  }

  // POST create
  public static function create($attributes) {
    $product_image = new ProductImage($attributes);

    try {
      $response = self::post( static::$site, array(static::$resource_name => $attributes) );
      $product_image->persisted = true;
      $product_image->id = $response[static::$resource_name]["id"];
      $product_image->attributes = array_merge($product_image->attributes, $response[static::$resource_name]);
      $product_image->errors = array();
    } catch ( RemoteResourceResourceInvalid $e ) {
      $product_image->errors = $e->response["errors"];
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
    if (!$this->persisted) throw new Exception("Attempted update: RemoteResource not persisted");
    $this->attributes = array_merge($this->attributes, $attributes);
    return $this->update();
  }

  // DELETE destroy
  public function destroy() {
    return self::delete( self::$site."/".$this->id );
  }

  // [ POST | PATCH ] save
  public function save() {
    if ($this->persisted) {
      return $this->update($this->attributes);
    } else {
      return self::create($this->attributes);
    }
  }

  // getters & flags
  public function id()         { return $this->id;             }
  public function errors()     { return $this->errors;         }
  public function persisted()  { return $this->persisted;      }
  public function valid()      { return empty($this->errors);  }
  public function attributes() { return $this->attributes;     }

  // ----------------------------
  // _____ PRIVATE METHODS ______
  // ____________________________

  // PATCH update
  private function update() {
    try {
      $response = self::patch( static::$site."/".$this->id, array( static::$resource_name => $this->attributes ) );
      $this->errors = array();
      $updated = true;
    } catch ( RemoteResourceResourceInvalid $e ) {
      $this->errors = $e->response["errors"];
      $updated = false;
    }
    return $updated;
  }

  private static function wherePath($path, $attributes) {
    if (!empty($attributes)) {
      $path = $path."?".http_build_query($attributes);
    }
    return $path;
  }
}
