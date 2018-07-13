<?php

namespace paularf\bubbleplot;

class ChartTest extends \PHPUnit\Framework\TestCase {

	public function testConstruct() {
		$chart = new Chart;
		$this->assertInstanceOf(Chart::class, $chart);
	}
}