<?php
namespace RemoteResource;

use RemoteResource\Connection;
use RemoteResource\Builder;
use RemoteResource\Collection;
use RemoteResource\Config;
use RemoteResource\Pool\ConfigPool;
use RemoteResource\Pool\ConnectionPool;

use Doctrine\Common\Inflector\Inflector;

/**
 * The main class, most of what you might concern yourself with is here.
 * Your RemoteResource models will extend this
 */
class RemoteResource {
  public static $site, 
                $resource_name, 
                $plural_resource_name,
                $format, 
                $auth_type, 
                $credentials;

  protected $id, 
            $errors = array(), 
            $persisted = false, 
            $valid = false, 
            $attributes;

  /**
   * @return Config Config instance for this resource
   */
  public static function config() {
    return ConfigPool::getInstance( get_called_class() );
  }

  /**
   * @return Connection Connection instance for this resource
   */
  public static function connection() {
    return ConnectionPool::getInstance( get_called_class() );
  }

  /**
   * @return string Resource singular name
   */
  public static function resourceName() {
    return static::$resource_name ?: Inflector::tableize( get_called_class() );
  }

  /**
   * @return string Resource plural name
   */
  public static function pluralResourceName() {
    return static::$plural_resource_name ?: Inflector::pluralize( static::resourceName() );
  }

  /**
   * Sometimes you need to set the site dynamically after the model has been autoloaded
   * @param string $site The base URL to be used for this resource
   */
  public static function setSite($site) {
    static::$site = $site;
  }

  /**
   * GET index
   * @return Collection   An iteratable collection of RemoteResource objects
   */
  public static function all() {
    $response = self::connection()->get( static::$site );
    $remote_resource_collection = new Collection(get_called_class(), $response);
    return $remote_resource_collection;
  }

  /**
   * GET index with (usually) query params, IE $product_images->where(array('product_id' => 1));
   * @param  array  $attributes A list of attributes to pass as query parameters
   * @return Collection         An iteratable collection of RemoteResource objects
   */
  public static function where($attributes = array()) {
    $response = self::connection()->get( self::wherePath(static::$site, $attributes) );
    $remote_resource_collection = new Collection(get_called_class(), $response);
    return $remote_resource_collection;
  }

  /**
   * GET show
   * @param  mixed $id Usually the primary key for the resource
   * @return RemoteResource     A RemoteResource object
   */
  public static function find($id) {
    $response = self::connection()->get( static::$site."/".$id );
    $resource = Builder::build(new static, $response[static::resourceName()]);
    return $resource;
  }

  /**
   * Create a resource
   * @param  array $attributes A list of attributes to pass to the request body
   * @return RemoteResource    A RemoteResource object
   */
  public static function create($attributes) {

    try {
      $response = self::connection()->post( static::$site, array(static::resourceName() => $attributes) );
      $resource = Builder::build(new static, $response[static::resourceName()]);
    } catch ( Exception\ResourceInvalid $e ) {
      $resource = new static($attributes);
      $resource->errors = $e->response["errors"];
    }

    return $resource;
  }

  /**
   * GET find a resource via alternate path
   * @param  string $path       The relative URL from a resource, IE [GET] /:id/clones
   * @param  array  $attributes An array of attributes to pass as GET params
   * @return array              The response body
   * @throws RemoteResource\Exception
   */
  public static function get($path, $attributes = array()) {
    return self::connection()->get( self::wherePath(static::$site.'/'.$path, $attributes) );
  }

  /**
   * POST create resource via alternate path
   * @param  string $path       The relative URL from a resource, IE [POST] /:id/clone
   * @param  array  $attributes An array of attributes to pass to the request body
   * @return array              The response body
   * @throws RemoteResource\Exception
   */
  public static function post($path, $attributes = array()) {
    return self::connection()->post( static::$site.'/'.$path, array(static::resourceName() => $attributes) );
  }

  /**
   * PATCH update this resource via specific path (alternative to updateAttributes when path needs to be explicit)
   * @param  string $path       The relative URL from the resource, IE [PATCH] /:id/increment
   * @param  array  $attributes A list of attributes to pass to the request body
   * @return array              The response body             
   * @throws RemoteResource\Exception
   */
  public function patch($path, $attributes = array()) {
    $this->attributes = array_merge($this->attributes, $attributes);
    return self::connection()->patch(
      static::$site.'/'.$this->id.'/'.$path,
      array( static::resourceName() => $this->attributes )
    );
  }

  /**
   * DELETE destroy this resource via alternate path
   * @param  string $path             The relative URL from the resource, IE [DELETE] /:id/remove_unsafe
   * @return array                    The response body
   * @throws RemoteResource\Exception
   */
  public function delete($path) {
    return self::connection()->delete(static::$site.'/'.$this->id.'/'.$path);
  }

  /**
   * RemoteResource constructor
   * @param array $attributes array of resource attributes
   */
  public function __construct($attributes=array()) {
    $this->attributes = $attributes;
  }

  /**
   * Getter override for returning attributes, IE $resource->title; // $resource->attributes["title"]
   * @param  string $attribute attribute name
   * @return mixed             attribute value
   */
  public function __get($attribute) {
    return $this->attributes[$attribute];
  }

  /**
   * Setter override for attributes, IE $resource->title = "whatever";
   * @param string $attribute attribute name
   * @param mixed  $value     attribute value
   */
  public function __set($attribute, $value) {
    $this->attributes[$attribute] = $value;
  }

  /**
   * Update attributes
   * @param  array $attributes array of attributes to update
   * @return bool              whether save was successful
   */
  public function updateAttributes($attributes) {
    if (!$this->persisted) throw new \Exception("Attempted update: RemoteResource not persisted");
    $this->attributes = array_merge($this->attributes, $attributes);
    return $this->update();
  }

  /**
   * DELETE destroy
   * @return array                    Response body
   * @throws RemoteResource\Exception
   */
  public function destroy() {
    self::connection()->delete( static::$site."/".$this->id );
  }

  /**
   * POST | PATCH save
   * @return bool  Whether save (update or create) was successful
   */
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

  /**
   * @return bool validity of this resource instance
   */
  private function instanceCreate() {
    $resource_to_merge = self::create($this->attributes);
    Builder::merge($this, $resource_to_merge);
    return $this->valid() ? true : false;
  }

  /**
   * @return bool whether the resource was successfully updated
   */
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

  /**
   * @param  string $path       base url
   * @param  array  $attributes associative array of query parameters
   * @return string             the resulting path
   */
  private static function wherePath($path, $attributes) {
    if (!empty($attributes)) {
      $path = $path."?".http_build_query($attributes);
    }
    return $path;
  }
}
