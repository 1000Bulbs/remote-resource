<?php
require_once 'lib/RemoteResource.php';

class RemoteResourceTest extends \Codeception\TestCase\Test
{
  public function testAll() {
    $subject = new RemoteResource();
    $response = $subject->all();
    $this->assertEquals( $response->status_code, 200 );
  }

  public function testWhere() {
    $subject = new RemoteResource();
    $hash_of_attributes = array('product_id' => 15, 'name' => 'foobar');
    $response = $subject->where($hash_of_attributes);
    $this->assertEquals( $response->status_code, 200 );
  }

  public function testFind() {
    $subject = new RemoteResource();
    $id = 15;
    $response = $subject->find($id);
    $this->assertEquals( $response->status_code, 404 );
  }

  public function testCreate() {
    $subject = new RemoteResource();
    $hash_of_attributes = array('product_id' => 15, 'name' => 'foobar');
    $response = $subject->create($hash_of_attributes);
    $this->assertEquals( $response->status_code, 200 );
  }

  public function testUpdate() {
    $subject = new RemoteResource();
    $hash_of_attributes = array('product_id' => 15, 'name' => 'foobar');
    $response = $subject->update($hash_of_attributes);
    $this->assertEquals( $response->status_code, 405 );
  }

  public function testDestroy() {
    $subject = new RemoteResource();
    $response = $subject->destroy();
    $this->assertEquals( $response->status_code, 405 );
  }

  public function testSave() {
    $subject = new RemoteResource();
    $response = $subject->save();
    $this->assertEquals( $response->status_code, 405 );
  }
}
