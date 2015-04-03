<?php
namespace RemoteResource;

use RemoteResource\Connection;
use RemoteResource\Builder;
use RemoteResource\Collection;
use RemoteResource\Config;
use Doctrine\Common\Inflector\Inflector;

class RemoteResource {
  public static $site, $resource_name, $plural_resource_name,
                $format, $auth_type, $credentials;

  protected $id, $errors = array(), $persisted = false, $valid = false, $attributes;

  private static $connection;

  public static function connection() {
    if (!self::$connection) {
      $config = new Config(static::$format, static::$auth_type, static::$credentials);
      self::$connection = new Connection($config);
    }

    return self::$connection;
  }

  public static function resourceName() {
    if (static::$resource_name) {
      return static::$resource_name;
    } else {
      return Inflector::tableize( get_called_class() );
    }
  }

  public static function pluralResourceName() {
    if (static::$plural_resource_name) {
      return static::$plural_resource_name;
    } else {
      return Inflector::pluralize( static::resourceName() );
    }
  }

  // GET index
  public static function all() {
    $response = self::connection()->get( static::$site );
    $remote_resource_collection = new Collection(new static, $response);
    return $remote_resource_collection;
  }

  // GET index w/ params
  public static function where($attributes = array()) {
    $response = self::connection()->get( self::wherePath(static::$site, $attributes) );
    $remote_resource_collection = new Collection(new static, $response);
    return $remote_resource_collection;
  }

  // GET show
  public static function find($id) {
    $response = self::connection()->get( static::$site."/".$id );
    $resource = Builder::build(new static, $response);
    return $resource;
  }

  // POST create
  public static function create($attributes) {

    try {
      $response = self::connection()->post( static::$site, array(static::resourceName() => $attributes) );
      $resource = Builder::build(new static, $response);
    } catch ( Exception\ResourceInvalid $e ) {
      $resource = new static($attributes);
      $resource->errors = $e->response["errors"];
    }

    return $resource;
  }

  public static function get($path, $attributes = array()) {
    return self::connection()->get( self::wherePath(static::$site.'/'.$path, $attributes) );
  }

  public static function post($path, $attributes = array()) {
    return self::connection()->post( static::$site.'/'.$path, array(static::resourceName() => $attributes) );
  }

  public function patch($path, $attributes = array()) {
    $this->attributes = array_merge($this->attributes, $attributes);
    return self::connection()->patch(
      static::$site.'/'.$this->id.'/'.$path,
      array( static::resourceName() => $this->attributes )
    );
  }

  public function delete($path) {
    return self::connection()->delete(static::$site.'/'.$this->id.'/'.$path);
  }

  public function __construct($attributes=array()) {
    $this->attributes = $attributes;
  }

  public function __get($attribute) {
    return $this->attributes[$attribute];
  }

  public function __set($attribute, $value) {
    $this->attributes[$attribute] = $value;
  }

  // update attributes
  public function updateAttributes($attributes) {
    if (!$this->persisted) throw new \Exception("Attempted update: RemoteResource not persisted");
    $this->attributes = array_merge($this->attributes, $attributes);
    return $this->update();
  }

  // DELETE destroy
  public function destroy() {
    self::connection()->delete( static::$site."/".$this->id );
  }

  // [ POST | PATCH ] save
  public function save() {
    if ($this->persisted) {
      return $this->update();
    } else {
      return $this->instanceCreate();
    }
  }

  // getters
  public function id()         { return $this->id;             }
  public function errors()     { return $this->errors;         }
  public function persisted()  { return $this->persisted;      }
  public function valid()      { return empty($this->errors);  }
  public function attributes() { return $this->attributes;     }

  // setters
  public function setId($id)                 { $this->id = $id;                 }
  public function setErrors($errors)         { $this->errors = $errors;         }
  public function setPersisted($persisted)   { $this->persisted = $persisted;   }
  public function setAttributes($attributes) { $this->attributes = $attributes; }

  // ----------------------------
  // _____ PRIVATE METHODS ______
  // ____________________________

  // POST create
  private function instanceCreate() {
    $resource_to_merge = self::create($this->attributes);
    Builder::merge($this, $resource_to_merge);
    return $this->valid() ? true : false;
  }

  // PATCH update
  private function update() {
    try {
      $response = self::connection()->patch( static::$site."/".$this->id, array( static::resourceName() => $this->attributes ) );
      $this->errors = array();
      $updated = true;
    } catch ( Exception\ResourceInvalid $e ) {
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
