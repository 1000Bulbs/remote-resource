<?php
require_once 'lib/RemoteResource.php';

class ProductImage extends RemoteResource {
  public static $site          = "http://localhost:3006/product_images";
  public static $resource_name = "product_image";
}
