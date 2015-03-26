<?php
require 'lib/Adder.php';

class AdderTest extends \Codeception\TestCase\Test
{
    public function testAdd()
    {
      $adder = new Adder();

      $this->assertEquals( $adder->add(2, 2), 4 ); 
    }
}
