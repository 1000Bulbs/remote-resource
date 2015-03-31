<?php
require_once 'lib/BasicRemoteResource.php';

class BasicRemoteResourceTest extends \Codeception\TestCase\Test
{
  //public function testHeaders()
  //{
    //$subject = new BasicRemoteResource();

    //$this->assertEquals(
      //$subject->headers(),
      //array(
        //'Content-Type' => 'application/json',
        //'Authorization' => 'Basic '.base64_encode('user:password')
      //)
    //);
  //}

  //public function testGet()
  //{
    //$subject = new BasicRemoteResource();
    //$path = 'http://example.com/';

    //$get_response = $subject->get($path);
    //$this->assertEquals( $get_response->status_code, 200 );
  //}

  //public function testPost()
  //{
    //$subject = new BasicRemoteResource();
    //$path = 'http://example.com/';
    //$hash_of_attributes = array('product_id' => 15);

    //$post_response = $subject->post($path, $hash_of_attributes);
    //$this->assertEquals( $post_response->status_code, 200 );
  //}

  //public function testPatch()
  //{
    //$subject = new BasicRemoteResource();
    //$path = 'http://example.com/';
    //$hash_of_attributes = array('product_id' => 15);

    //$patch_response = $subject->patch($path, $hash_of_attributes);
    //$this->assertEquals( $patch_response->status_code, 405 );
  //}

  //public function testDelete()
  //{
    //$subject = new BasicRemoteResource();
    //$path = 'http://example.com/';

    //$delete_response = $subject->delete($path);
    //$this->assertEquals( $delete_response->status_code, 405 );
  //}
}
