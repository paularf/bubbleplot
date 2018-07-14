<?php

namespace paularf\bubbleplot;

class ChartTest extends \PHPUnit\Framework\TestCase {

	public function testConstruct() {
		$chart = new Chart;
		$this->assertInstanceOf(Chart::class, $chart);
	}

	public function testDrawRowNames() {
		$data = new Data;
		$data->data = [
			'G1' => [
				'r1' => [
					'c' => 2
				],
				'r3' => [
					'c' => 5
				]
			],
			'G2' => [
				'r2' => [
					'c' => 3
				]
			]
		];

		$chart = new Chart;

		$chart->data = $data;

		ob_start();
		$chart->draw_row_names(0, 0);
		$output = ob_get_clean();

		$this->assertContains('r1', $output);
		$this->assertContains('r3', $output);
		$this->assertContains('r2', $output);
	}
}