<?php

namespace paularf\bubbleplot;

class DataTest extends \PHPUnit\Framework\TestCase {

	public function testConstruct() {
		$chart = new Data;
		$this->assertInstanceOf(Data::class, $chart);
	}

	public function testGetRows(){
		$dato = new Data;
		$dato->data = [
			'Group' => [
				'row' => [
					'col' => 100
				]
			]
		];
		$row_names = $dato->get_row_names();
		$this->assertEquals(['row'], $row_names);
	}

	public function testGetRows2(){
		$dato = new Data;
		$dato->data = [
			'Group' => [
				'row' => [
					'col' => 100
				]
			],
			'Group2' => [
				'row_2' => [
					'col' => 200
				]
			]
		];
		$row_names = $dato->get_row_names();
		$this->assertEquals(['row', 'row_2'], $row_names);

	}

	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage Row name repetido
	 */
	public function testGetRows3(){
		$dato = new Data;
		$dato->data = [
			'Group' => [
				'row' => [
					'col' => 100
				]
			],
			'Group2' => [
				'row' => [
					'col' => 200
				]
			]
		];
		$row_names = $dato->get_row_names();

	}

	public function testGetCols(){
		$dato = new Data;
		$dato->data = [
			'Group' => [
				'row' => [
					'col' => 100
				]
			],
			'Group2' => [
				'row' => [
					'col_2' => 200
				]
			]
		];
		$col_names = $dato->get_column_names();
		$this->assertEquals(['col', 'col_2'], $col_names);

	}
	/**
	 * @expectedException Exception
	 */
	public function testGetCols2(){
		$dato = new Data;
		$dato->data = [
			'Group' => [
				'row' => [
					'col' => 100
				]
			],
			'Group2' => [
				'row' => [
					'col' => 200
				]
			]
		];
		$row_names = $dato->get_column_names();

	}

	public function testGetNotExistantValue() {
		$chart = new Data;
		$value = $chart->get_value('Group', 'row', 'col');
		$this->assertEquals(0, $value);
	}

	public function testGetExistantValue() {
		$chart = new Data;
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

	public function testFilterByRow() {
		$data = new Data;
		$data->data = [
			'Group' => [
				'row' => [
					'col' => 100
				]
			],
			'Group2' => [
				'row_2' => [
					'col' => 200
				]
			]
		];

		$filtered_data = $data->filter_by_rows(['row']);

		$this->assertEquals(['row'], $filtered_data->get_row_names());

		$expected_data = [
			'Group' => [
				'row' => [
					'col' => 100
				]
			],
			'Group2' => [
			]
		];
		$this->assertEquals($expected_data, $filtered_data->data);
	}

		public function testFilterByRow2() {
		$data = new Data;
		$data->data = [
			'Group' => [
				'row' => [
					'col' => 100
				]
			],
			'Group2' => [
				'row_2' => [
					'col' => 200
				],
				'row_3' => [
					'col' => 300
				]
			]
		];

		$filtered_data = $data->filter_by_rows(['row', 'row_3']);

		$this->assertEquals(['row', 'row_3'], $filtered_data->get_row_names());

		$expected_data = [
			'Group' => [
				'row' => [
					'col' => 100
				]
			],
			'Group2' => [
				'row_3' => [
					'col' => 300
				]
			]
		];
		$this->assertEquals($expected_data, $filtered_data->data);
	}

		public function testFilterByCol() {
		$data = new Data;
		$data->data = [
			'Group' => [
				'row' => [
					'col_1' => 100
				]
			],
			'Group2' => [
				'row_2' => [
					'col_2' => 200
				],
				'row_3' => [
					'col_3' => 300
				]
			]
		];

		$filtered_data = $data->filter_by_columns(['col_1', 'col_3']);

		$this->assertEquals(['col_1', 'col_3'], $filtered_data->get_column_names());
	}

	public function testFiltered_by_rows_and_cols(){
		$data = new Data;
		$data->data = [
			'Group' => [
				'row' => [
					'col_1' => 100
				]
			],
			'Group2' => [
				'row_2' => [
					'col_2' => 200
				],
				'row_3' => [
					'col_3' => 300
				]
			]
		];

		$row_filter = ['row', 'row_3'];
		$col_filter = ['col_3'];
		$expected_result = [
			'Group' => [
				'row' => [
				]
			],
			'Group2' => [
				'row_3' => [
					'col_3' => 300
				]
			]
		];
		$data_filtered = $data
			->filter_by_rows($row_filter)
			->filter_by_columns($col_filter);

		//$data_filtered_by_rows_and_cols = $data_filtered_by_rows->filter_by_columns($col_filter);
		$this->assertEquals($expected_result, $data_filtered->data);

	}

	public function testGetTotalByColumn() {
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

		$this->assertEquals(7, $data->get_total_by_column('G1', 'c'));
	}

	public function testAddGroupFromFile() {
		$data = new Data;
		$data->add_group_from_file('g1', __DIR__ . '/test_file.data');

		$expected = [
			'g1' => [
				'ec1' => [ 'tax1' => 1],
				'ec2' => [ 'tax2' => 2],
				'ec3' => [ 'tax3' => 3]
			]
		];
		$this->assertEquals($expected, $data->data);
	}
}
