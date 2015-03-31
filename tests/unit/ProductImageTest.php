<?php
require_once 'lib/ProductImage.php';

class ProductImageTest extends \Codeception\TestCase\Test
{
  // CREATE 422
  public function testCreate_422() {
    $attributes = array('name' => 'foo');

    $product_image = ProductImage::create($attributes);

    // it should return a ProductImage instance
    $this->assertInstanceOf('ProductImage', $product_image);

    // it should _not_ have an id
    $this->assertNull($product_image->id());

    // it should have the attributes which have been passed in
    $this->assertEquals($product_image->attributes(), $attributes);

    // it should have an errors array
    $this->assertEquals($product_image->errors(), array("File can't be blank", "Product can't be blank"));

    // it should be marked as invalid
    $this->assertFalse($product_image->valid());

    // it should be marked as not persisted
    $this->assertFalse($product_image->persisted());
  }

  // CREATE 500
  public function testCreate_500() {
    $attributes = array('file' => 'file');

    // it should throw a RemoteResourceServerError
    $this->setExpectedException('RemoteResourceServerError');

    $product_image = ProductImage::create($attributes);
  }

  // CREATE 201
  public function testCreate_201() {
    $file = 'fixtures/cube.png';
    $file_content_type = mime_content_type($file);
    $file_data = base64_encode(file_get_contents($file));
    $file_data_uri = "data:".$file_content_type.";base64,".$file_data;

    $attributes = array('product_id' => 15, 'file' => $file_data_uri);

    $product_image = ProductImage::create($attributes);

    // it should return a ProductImage instance
    $this->assertInstanceOf('ProductImage', $product_image);

    // it should have an id
    $this->assertNotNull($product_image->id());

    // it should have attributes returned from the remote resource
    $this->assertNotEquals($product_image->attributes(), $attributes);
    $attributes = $product_image->attributes();
    $this->assertNotNull($attributes["id"]);
    $this->assertNotNull($attributes["sizes_and_urls"]);

    // it should _not_ have any errors
    $this->assertEquals($product_image->errors(), array());

    // it should be marked as valid
    $this->assertTrue($product_image->valid());

    // it should be marked as persisted
    $this->assertTrue($product_image->persisted());
  }

  //public function testUpdateAttributes_204() {
    // TODO: write this AFTER create and find have been tested
  //}

  //public function testUpdateAttributes_noId() {
    //$product_image = new ProductImage;

    //$this->setExpectedException('NoIdAssignedException');

    //$product_image->updateAttributes(array('product_id' => 15));
  //}

  //public function testUpdateAttributes_404() {
    //$product_image = new ProductImage;
    //$product_image->id = 10;

    //$result = $product_image->updateAttributes(array('product_id' => 15));

    //$this->assertEquals( $result->status_code, 404 );
  //}
}
