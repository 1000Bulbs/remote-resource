<?php
class ProductImage extends RemoteResource\RemoteResource {
  public static $site = "http://localhost:3000/api/product_images";
}

class StaticPage extends RemoteResource\RemoteResource {
  public static $site = "http://localhost:3001/api/static_pages";
  public static $resource_name = 'page';
  public static $format = 'json';
  public static $auth_type = 'basic';
  public static $credentials = 'foo:bar';
}

class Brand extends RemoteResource\RemoteResource {
  public static $site = "http://localhost:3002/api/brands";
  public static $plural_resource_name = 'manufacturers';
  public static $auth_type = 'basic';
  public static $credentials = 'user:password';
}

class RemoteResourceTest extends PHPUnit_Framework_TestCase {
  protected $client;

  protected function setUp() {
    // set client to MockClient
    ProductImage::connection()->setClient(new MockClient());
    $this->client = ProductImage::connection()->client();
  }

  // multiple remote resource instances
  /**
   * @group current
   */
  public function testsMultipleInstances() {
    // sites
    $this->assertEquals(ProductImage::$site, "http://localhost:3000/api/product_images");
    $this->assertEquals(StaticPage::$site, "http://localhost:3001/api/static_pages");
    $this->assertEquals(Brand::$site, "http://localhost:3002/api/brands");
    $this->assertEquals(ProductImage::$site, "http://localhost:3000/api/product_images");
    $this->assertEquals(StaticPage::$site, "http://localhost:3001/api/static_pages");
    $this->assertEquals(Brand::$site, "http://localhost:3002/api/brands");

    // resource names
    $this->assertEquals(ProductImage::resourceName(), 'product_image');
    $this->assertEquals(StaticPage::resourceName(), 'page');
    $this->assertEquals(Brand::resourceName(), 'brand');

    // plural resource names
    $this->assertEquals(ProductImage::pluralResourceName(), 'product_images');
    $this->assertEquals(StaticPage::pluralResourceName(), 'pages');
    $this->assertEquals(Brand::pluralResourceName(), 'manufacturers');

    // formats
    $this->assertEquals(ProductImage::config()->format(), 'json');
    $this->assertEquals(StaticPage::config()->format(), 'json');
    $this->assertEquals(Brand::config()->format(), 'json');

    // auth types
    $this->assertEquals(ProductImage::config()->authType(), 'none');
    $this->assertEquals(StaticPage::config()->authType(), 'basic');
    $this->assertEquals(Brand::config()->authType(), 'basic');

    // credentials
    $this->assertEquals(ProductImage::config()->credentials(), null);
    $this->assertEquals(StaticPage::config()->credentials(), 'Basic '.base64_encode('foo:bar'));
    $this->assertEquals(Brand::config()->credentials(), 'Basic '.base64_encode('user:password'));
  }

  // CREATE 422
  public function testCreate_422() {
    $this->client->setResponseParams(422, array('errors' => array("File can't be blank", "Product can't be blank")));

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
    $this->client->setResponseParams(500);

    $attributes = array('file' => 'file');

    // it should throw a RemoteResourceServerError
    $this->setExpectedException('RemoteResource\Exception\ServerError');

    $product_image = ProductImage::create($attributes);
  }

  // CREATE 201
  public function testCreate_201() {
    $this->client->setResponseParams(201, array('product_image' => array('id' => 12, 'sizes_and_urls' => array())));

    $file = 'tests/fixtures/cube.png';
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
    $this->assertNotNull($product_image->id);
    $this->assertNotNull($product_image->sizes_and_urls);

    // it should _not_ have any errors
    $this->assertEquals($product_image->errors(), array());

    // it should be marked as valid
    $this->assertTrue($product_image->valid());

    // it should be marked as persisted
    $this->assertTrue($product_image->persisted());
  }

  // SAVE CREATE 422
  public function testSave_create_422() {
    $this->client->setResponseParams(422, array('errors' => array("File can't be blank")));

    $product_image = new ProductImage;

    $product_image->name = "cool image";
    $product_image->product_id = 15;

    $product_image->save();

    // the product image should not be valid
    $this->assertFalse($product_image->valid());

    // the product image should not be persisted
    $this->assertFalse($product_image->persisted());

    // the product image should have errors
    $this->assertEquals($product_image->errors(), array("File can't be blank"));
  }

  // SAVE CREATE 201
  public function testSave_create_201() {
    $this->client->setResponseParams(201, array('product_image' => array('id' => 2, 'sizes_and_urls' => array())));

    $file = 'tests/fixtures/cube.png';
    $file_content_type = mime_content_type($file);
    $file_data = base64_encode(file_get_contents($file));
    $file_data_uri = "data:".$file_content_type.";base64,".$file_data;

    $attributes = array('product_id' => 15, 'file' => $file_data_uri);

    $product_image = new ProductImage;

    $product_image->product_id = 15;
    $product_image->file = $file_data_uri;

    $product_image->save();

    // the product image should be valid
    $this->assertTrue($product_image->valid());

    // the product image should be persisted
    $this->assertTrue($product_image->persisted());

    // the product image should have attributes returned from the remote resource
    $this->assertNotNull($product_image->sizes_and_urls);
  }

  // CUSTOM METHOD 201
  public function testCustomMethod_get_201() {
    $this->client->setResponseParams(201, array('product_image' => array('id' => 3, 'product_id' => 12)));

    // check output for validity
    $product_id = 12;
    $id = 2;
    $cloned_product_image_hash = ProductImage::get($id."/clone/".$product_id);
    $this->assertEquals($product_id, $cloned_product_image_hash['product_image']['product_id']);
  }

  // SAVE UPDATE 422
  public function testSave_update_422() {
    $product_image = new ProductImage;

    $file_data_uri = "data:image/png;base64,...";
    $product_image->product_id = 15;
    $product_image->file = $file_data_uri;

    $this->client->setResponseParams(201, array('product_image' => array('id' => 5, 'product_id' => $product_image->product_id, 'file' => $product_image->file)));
    $product_image->save(); // created

    $string_too_long = 'llllllllllllllllllllllllllllllllllllllllllllllllllll';
    $string_too_long = $string_too_long . 'llllllllllllllllllllllllllllllllllllllllllllllllllll';
    $string_too_long = $string_too_long . 'llllllllllllllllllllllllllllllllllllllllllllllllllll';
    $string_too_long = $string_too_long . 'llllllllllllllllllllllllllllllllllllllllllllllllllll';
    $string_too_long = $string_too_long . 'llllllllllllllllllllllllllllllllllllllllllllllllllll';

    $product_image->name = $string_too_long;

    $this->client->setResponseParams(422, array('errors' => array('Name is too long (maximum is 255 characters)')));
    $product_image->save(); // updated

    // the product image should not be valid
    $this->assertFalse($product_image->valid());

    // the product image should be persisted
    $this->assertTrue($product_image->persisted());

    // the product image should have errors
    $this->assertEquals($product_image->errors(), array("Name is too long (maximum is 255 characters)"));
  }

  // SAVE UPDATE 204
  public function testSave_update_204() {
    $file = 'tests/fixtures/cube.png';
    $file_content_type = mime_content_type($file);
    $file_data = base64_encode(file_get_contents($file));
    $file_data_uri = "data:".$file_content_type.";base64,".$file_data;

    $attributes = array('product_id' => 15, 'file' => $file_data_uri);

    $product_image = new ProductImage;

    $product_image->product_id = 15;
    $product_image->file = $file_data_uri;

    $this->client->setResponseParams(201, array('product_image' => array('id' => 5, 'product_id' => $product_image->product_id, 'file' => $product_image->file)));
    $product_image->save(); // created

    $product_image->name = 'cool new name';

    $this->client->setResponseParams(204, array('product_image' => array('id' => 5, 'name' => 'cool new name')));
    $product_image->save(); // updated

    // the product image should be valid
    $this->assertTrue($product_image->valid());

    // the product image should be persisted
    $this->assertTrue($product_image->persisted());

    // the product image should _not_ have errors
    $this->assertEquals($product_image->errors(), array());

    // the product image should have updated attributes
    $this->assertEquals($product_image->name, 'cool new name');
  }

  // ALL 200
  public function testAll_200() {
    $this->client->setResponseParams(200, array('product_images' => array()));
    $product_images = ProductImage::all();

    // it should return a RemoteResource\Collection instance
    $this->assertInstanceOf('RemoteResource\Collection', $product_images);
  }

  // WHERE 200
  public function testWhere_200() {
    $this->client->setResponseParams(200, array('product_images' => array(array('id' => 5, 'product_id' => 15), array('id' => 6, 'product_id' => 15))));

    $product_images = ProductImage::where(array('product_id' => 15));

    // it should return a RemoteResource\Collection instance
    $this->assertInstanceOf('RemoteResource\Collection', $product_images);

    // the RemoteResource\Collection should contain ProductImage instances
    $this->assertInstanceOf('ProductImage', $product_images->first());
    $this->assertInstanceOf('ProductImage', $product_images->last());

    // the ProductImage instances are properly formatted
    $product_image_sample = $product_images->first();
    $this->assertNotNull($product_image_sample->id());
    $this->assertTrue($product_image_sample->valid());
    $this->assertTrue($product_image_sample->persisted());

    // the product_id matches the product_id searched against
    $this->assertEquals($product_image_sample->product_id, 15);
  }

  // WHERE noMatches
  public function testWhere_noMatches() {
    $this->client->setResponseParams(200, array('product_images' => array()));

    $product_images = ProductImage::where(array());

    // it should return a RemoteResource\Collection instance
    $this->assertInstanceOf('RemoteResource\Collection', $product_images);

    // it should have a count of zero
    $this->assertEquals($product_images->count(), 0);
  }

  // DESTROY 204
  public function testDestroy_204() {
    $file = 'tests/fixtures/cube.png';
    $file_content_type = mime_content_type($file);
    $file_data = base64_encode(file_get_contents($file));
    $file_data_uri = "data:".$file_content_type.";base64,".$file_data;

    $attributes = array('product_id' => 15, 'file' => $file_data_uri, 'id' => 10);

    $this->client->setResponseParams(201, array('product_image' => $attributes));
    $product_image = ProductImage::create($attributes);

    $this->client->setResponseParams(204);
    $result = $product_image->destroy();

    // it destroys the product_image
    $this->client->setResponseParams(404);
    $this->setExpectedException('RemoteResource\Exception\ResourceNotFound');
    ProductImage::find($product_image->id());
  }

  // DESTROY 404
  public function testDestroy_404() {
    $this->client->setResponseParams(404);

    $product_image = new ProductImage;

    // it should throw a RemoteResourceResourceNotFound
    $this->setExpectedException('RemoteResource\Exception\ResourceNotFound');

    $product_image->destroy();
  }

  // FIND 404
  public function testFind_404() {
    $this->client->setResponseParams(404);

    // it should throw a RemoteResourceResourceNotFound
    $this->setExpectedException('RemoteResource\Exception\ResourceNotFound');

    ProductImage::find(9999);
  }

  // FIND 200
  public function testFind_200() {
    $file = 'tests/fixtures/cube.png';
    $file_content_type = mime_content_type($file);
    $file_data = base64_encode(file_get_contents($file));
    $file_data_uri = "data:".$file_content_type.";base64,".$file_data;

    $attributes = array('product_id' => 15, 'file' => $file_data_uri, 'id' => 2);
    $this->client->setResponseParams(201, array('product_image' => $attributes));

    $product_image_created = ProductImage::create($attributes);

    $this->client->setResponseParams(200, array('product_image' => array('id' => $product_image_created->id(), 'product_id' => 15)));
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
    $file = 'tests/fixtures/cube.png';
    $file_content_type = mime_content_type($file);
    $file_data = base64_encode(file_get_contents($file));
    $file_data_uri = "data:".$file_content_type.";base64,".$file_data;

    $attributes = array('product_id' => 15, 'file' => $file_data_uri, 'id' => 5, 'name' => 'cool name');
    $this->client->setResponseParams(201, array('product_image' => $attributes));

    $product_image = ProductImage::create($attributes);
    $attributes = $product_image->attributes();
    $previous_name_value = $attributes["name"];

    $this->client->setResponseParams(204);
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
    $file = 'tests/fixtures/cube.png';
    $file_content_type = mime_content_type($file);
    $file_data = base64_encode(file_get_contents($file));
    $file_data_uri = "data:".$file_content_type.";base64,".$file_data;

    $attributes = array('product_id' => 15, 'file' => $file_data_uri, 'id' => 5);
    $this->client->setResponseParams(201, array('product_image' => $attributes));
    $product_image = ProductImage::create($attributes);

    // it should throw a RemoteResourceServerError
    $this->setExpectedException('RemoteResource\Exception\ServerError');

    $this->client->setResponseParams(500);
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
    $file = 'tests/fixtures/cube.png';
    $file_content_type = mime_content_type($file);
    $file_data = base64_encode(file_get_contents($file));
    $file_data_uri = "data:".$file_content_type.";base64,".$file_data;

    $attributes = array('product_id' => 15, 'file' => $file_data_uri, 'id' => 3);
    $this->client->setResponseParams(201, array('product_image' => $attributes));
    $product_image = ProductImage::create($attributes);

    $string_too_long = 'llllllllllllllllllllllllllllllllllllllllllllllllllll';
    $string_too_long = $string_too_long . 'llllllllllllllllllllllllllllllllllllllllllllllllllll';
    $string_too_long = $string_too_long . 'llllllllllllllllllllllllllllllllllllllllllllllllllll';
    $string_too_long = $string_too_long . 'llllllllllllllllllllllllllllllllllllllllllllllllllll';
    $string_too_long = $string_too_long . 'llllllllllllllllllllllllllllllllllllllllllllllllllll';

    $this->client->setResponseParams(422, array('errors' => array('Name is too long (maximum is 255 characters)')));
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

  // test to make sure inflection gets stuff correct
  public function testResourceName() {
    $this->assertEquals('product_image', ProductImage::resourceName());
    $this->assertEquals('product_images', ProductImage::pluralResourceName());
  }

  // test adding a dynamic site name
  public function testSetDynamicSite() {
    ProductImage::setSite("localhost"."/whatever");
    $this->assertEquals('localhost/whatever', ProductImage::$site);
  }
}
