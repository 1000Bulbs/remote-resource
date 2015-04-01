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

  // DESTROY 204
  public function testDestroy_204() {
    $file = 'fixtures/cube.png';
    $file_content_type = mime_content_type($file);
    $file_data = base64_encode(file_get_contents($file));
    $file_data_uri = "data:".$file_content_type.";base64,".$file_data;

    $attributes = array('product_id' => 15, 'file' => $file_data_uri);

    $product_image = ProductImage::create($attributes);

    $result = $product_image->destroy();

    // it destroys the product_image
    $this->setExpectedException('RemoteResourceResourceNotFound');
    ProductImage::find($product_image->id());
  }

  // DESTROY 404
  public function testDestroy_404() {
    $product_image = new ProductImage;

    // it should throw a RemoteResourceResourceNotFound
    $this->setExpectedException('RemoteResourceResourceNotFound');

    $product_image->destroy();
  }

  // FIND 404
  public function testFind_404() {
    // it should throw a RemoteResourceResourceNotFound
    $this->setExpectedException('RemoteResourceResourceNotFound');

    ProductImage::find(999);
  }

  // FIND 200
  public function testFind_200() {
    $file = 'fixtures/cube.png';
    $file_content_type = mime_content_type($file);
    $file_data = base64_encode(file_get_contents($file));
    $file_data_uri = "data:".$file_content_type.";base64,".$file_data;

    $attributes = array('product_id' => 15, 'file' => $file_data_uri);

    $product_image_created = ProductImage::create($attributes);

    $product_image_found = ProductImage::find($product_image_created->id());

    // it shound return a ProductImage instance
    $this->assertInstanceOf('ProductImage', $product_image_found);

    // it should be flagged as persisted
    $this->assertTrue($product_image_found->persisted());

    // it should be valid
    $this->assertTrue($product_image_found->valid());

    // it should have _no_ errors
    $this->assertEquals($product_image_found->errors(), array());

    // it should have the same attributes as the previously created resource
    $product_image_created_attributes = $product_image_created->attributes();
    unset($product_image_created_attributes["file"]); // unset the temporary file attribute
    $this->assertEquals($product_image_created_attributes, $product_image_found->attributes());
  }

  // UPDATE 204
  public function testUpdateAttributes_204() {
    $file = 'fixtures/cube.png';
    $file_content_type = mime_content_type($file);
    $file_data = base64_encode(file_get_contents($file));
    $file_data_uri = "data:".$file_content_type.";base64,".$file_data;

    $attributes = array('product_id' => 15, 'file' => $file_data_uri);

    $product_image = ProductImage::create($attributes);
    $attributes = $product_image->attributes();
    $previous_name_value = $attributes["name"];

    $result = $product_image->updateAttributes(array('name' => 'new name'));

    // it should return _true_
    $this->assertTrue($result);

    // it should have updated the updated attributes
    $attributes = $product_image->attributes();
    $this->assertEquals($attributes["name"], "new name");
    $this->assertNotEquals($attributes["name"], $previous_name_value);

    // it should remain flagged as persisted
    $this->assertTrue($product_image->persisted());

    // it should be valid
    $this->assertTrue($product_image->valid());

    // it should _not_ have any errors
    $this->assertEquals($product_image->errors(), array());
  }

  // UPDATE 500
  public function testUpdateAttributes_500() {
    $file = 'fixtures/cube.png';
    $file_content_type = mime_content_type($file);
    $file_data = base64_encode(file_get_contents($file));
    $file_data_uri = "data:".$file_content_type.";base64,".$file_data;

    $attributes = array('product_id' => 15, 'file' => $file_data_uri);

    $product_image = ProductImage::create($attributes);

    // it should throw a RemoteResourceServerError
    $this->setExpectedException('RemoteResourceServerError');

    $result = $product_image->updateAttributes(array('file' => 'file'));
  }

  // UPDATE not persisted
  public function testUpdateAttributes_notPersisted() {
    $product_image = new ProductImage;

    $this->setExpectedException('Exception', 'Attempted update: RemoteResource not persisted');

    $product_image->updateAttributes(array('product_id' => 15));
  }

  // UPDATE 422
  public function testUpdateAttributes_422() {
    $file = 'fixtures/cube.png';
    $file_content_type = mime_content_type($file);
    $file_data = base64_encode(file_get_contents($file));
    $file_data_uri = "data:".$file_content_type.";base64,".$file_data;

    $attributes = array('product_id' => 15, 'file' => $file_data_uri);

    $product_image = ProductImage::create($attributes);

    $string_too_long = 'llllllllllllllllllllllllllllllllllllllllllllllllllll';
    $string_too_long = $string_too_long . 'llllllllllllllllllllllllllllllllllllllllllllllllllll';
    $string_too_long = $string_too_long . 'llllllllllllllllllllllllllllllllllllllllllllllllllll';
    $string_too_long = $string_too_long . 'llllllllllllllllllllllllllllllllllllllllllllllllllll';
    $string_too_long = $string_too_long . 'llllllllllllllllllllllllllllllllllllllllllllllllllll';

    $result = $product_image->updateAttributes(array('name' => $string_too_long));

    // it should return _false_
    $this->assertFalse($result);

    // it should have errors
    $this->assertEquals($product_image->errors(), array('Name is too long (maximum is 255 characters)'));

    // it should be invalid
    $this->assertFalse($product_image->valid());

    // it should still be listed as persisted
    $this->assertTrue($product_image->persisted());
  }
}