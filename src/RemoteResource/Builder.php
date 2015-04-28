<?php
namespace RemoteResource;

class Builder {
  /**
   * @param  string $resource   the resource to build
   * @param  array  $attributes the resource attributes, associative array
   * @return RemoteResource     the resulting resource
   */
  public static function build($resource, $attributes) {
    $resource_class = get_class($resource);

    $resource->setPersisted(true);
    $resource->setId($attributes["id"]);
    $resource->setAttributes(array_merge($resource->attributes(), $attributes));
    $resource->setErrors(array());

    return $resource;
  }

  /**
   * @param  RemoteResource $resource          The base resource
   * @param  RemoteResource $resource_to_merge The resource to merge
   * @return RemoteResource                    The merged result
   */
  public static function merge($resource, $resource_to_merge) {
    $resource->setId($resource_to_merge->id());
    $resource->setAttributes(array_merge($resource->attributes(), $resource_to_merge->attributes()));
    $resource->setPersisted($resource_to_merge->persisted());
    $resource->setErrors($resource_to_merge->errors());

    return $resource;
  }
}
