<?php
require_once 'lib/RemoteResource.php';

class ProductImage extends RemoteResource {
  public static $site                 = "http://localhost:3000/api/product_images";
  public static $resource_name        = "product_image";
  public static $plural_resource_name = "product_images";
}
