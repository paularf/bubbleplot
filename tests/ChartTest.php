<?php

namespace paularf\bubbleplot;

class ChartTest extends \PHPUnit\Framework\TestCase {

	public function testConstruct() {
		$chart = new Chart;
		$this->assertInstanceOf(Chart::class, $chart);
	}

	public function testGetNotExistantValue() {
		$chart = new Chart;
		$value = $chart->get_value('Group', 'row', 'col');
		$this->assertEquals(0, $value);
	}

	public function testGetExistantValue() {
		$chart = new Chart;
		$chart->data = [
			'Group' => [
				'row' => [
					'col' => 100
				]
			]
		];
		$value = $chart->get_value('Group', 'row', 'col');
		$this->assertEquals(100, $value);
	}
}