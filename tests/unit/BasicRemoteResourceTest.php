<?php
require 'lib/BasicRemoteResource.php';

class BasicRemoteResourceTest extends \Codeception\TestCase\Test
{

  protected $subject;
  protected $path;
  protected $hash_of_attributes;

  protected function setUp()
  {
    $this->subject = new BasicRemoteResource();
    $this->path = 'http://example.com/';
    $this->hash_of_attributes = array('product_id' => 15);
  }

  public function testHeaders()
  {
    $this->assertEquals(
      $this->subject->headers(),
      array(
        'Content-Type' => 'application/json',
        'Authorization' => 'Basic '.base64_encode('user:password')
      )
    );
  }

  public function testGet()
  {
    $get_response = $this->subject->get($this->path);
    $this->assertEquals( $get_response->status_code, 200 );
  }

  public function testPost()
  {
    $post_response = $this->subject->post($this->path, $this->hash_of_attributes);
    $this->assertEquals( $post_response->status_code, 200 );
  }

  public function testPatch()
  {
    $patch_response = $this->subject->patch($this->path, $this->hash_of_attributes);
    $this->assertEquals( $patch_response->status_code, 405 );
  }

  public function testDelete()
  {
    $delete_response = $this->subject->delete($this->path);
    $this->assertEquals( $delete_response->status_code, 405 );
  }

  public function testSite()
  {
    try {
      $this->subject->site();
       $this->fail("Expected an Exception to be thrown");
    } catch( Exception $e ) {
      $this->assertEquals( $e->getMessage(), "Not implemented" );
    }
  }
}
