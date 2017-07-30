<?php

use \Mockery as m;

class Temperature
{
    public function __construct($service)
    {
        $this->_service = $service;
    }

    public function average()
    {
        $total = 0;
        for ($i = 0; $i < 3; ++$i) {
            $total += $this->_service->readTemp();
        }

        return $total / 3;
    }
}

class MockTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testGetsAverageTemperatureFromThreeServiceReadings()
    {
        $service = m::mock('service');
        $service->shouldReceive('readTemp')->times(3)->andReturn(10, 12, 14);

        $temperature = new Temperature($service);

        $this->assertEquals(12, $temperature->average());
    }
}
