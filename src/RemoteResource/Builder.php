<?php
namespace RemoteResource;

class Builder {
  public static function build($resource, $attributes) {
    $resource_class = get_class($resource);

    $resource->setPersisted(true);
    $resource->setId($attributes["id"]);
    $resource->setAttributes(array_merge($resource->attributes(), $attributes));
    $resource->setErrors(array());

    return $resource;
  }

  public static function buildForCollection($resource, $attributes) {
    $resource->setPersisted(true);
    $resource->setId($attributes["id"]);
    $resource->setAttributes(array_merge($resource->attributes(), $attributes));
    $resource->setErrors(array());

    return $resource;
  }

  public static function merge($resource, $resource_to_merge) {
    $resource->setId($resource_to_merge->id());
    $resource->setAttributes(array_merge($resource->attributes(), $resource_to_merge->attributes()));
    $resource->setPersisted($resource_to_merge->persisted());
    $resource->setErrors($resource_to_merge->errors());
  }
}
