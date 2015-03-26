<?php

class ProductImageResource extends RemoteResource {
  public static function site() {
    "http://localhost:3006/product_images"
  }

  //public static function clone_image($id, $product_id) {
   // return self::get( (self::site()."/".$id."/clone/".$product_id) );
  //}
}
