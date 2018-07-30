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
            'row' => [
                'col' => 100
            ]
		];
		$row_names = $dato->get_row_names();
		$this->assertEquals(['row'], $row_names);
	}

	public function testGetRows2(){
		$dato = new Data;
		$dato->data = [
            'row' => [
                'col' => 100
            ],
            'row_2' => [
                'col' => 200
            ]
		];
		$row_names = $dato->get_row_names();
		$this->assertEquals(['row', 'row_2'], $row_names);

	}

	public function testGetCols(){
		$dato = new Data;
		$dato->data = [
            'row' => [
                'col' => 100
            ],
            'row2' => [
                'col_2' => 200
            ]
		];
		$col_names = $dato->get_column_names();
		$this->assertEquals(['col', 'col_2'], $col_names);

	}

    public function testGetCols2(){
        $dato = new Data;
        $dato->data = [
            'r1' => ['c1' => 5, 'c2' => 10 ],
            'r2' => ['c1' => 20, 'c2' => 25 ]
        ];
        $col_names = $dato->get_column_names();
        $this->assertEquals(['c1', 'c2'], $col_names);

    }


	public function testGetNotExistantValue() {
		$chart = new Data;
		$value = $chart->get_value('row', 'col');
		$this->assertEquals(0, $value);
	}

	public function testGetExistantValue() {
		$chart = new Data;
		$chart->data = [
            'row' => [
                'col' => 100
            ]
		];
		$value = $chart->get_value( 'row', 'col');
		$this->assertEquals(100, $value);
	}

	public function testFilterByRow() {
		$data = new Data;
		$data->data = [
            'row' => [
                'col' => 100
            ],
            'row_2' => [
                'col' => 200
            ]
		];

		$filtered_data = $data->filter_by_rows(['row']);

		$this->assertEquals(['row'], $filtered_data->get_row_names());

		$expected_data = [
            'row' => [
                'col' => 100
            ]
		];
		$this->assertEquals($expected_data, $filtered_data->data);
	}

		public function testFilterByRow2() {
		$data = new Data;
		$data->data = [
            'row' => [
                'col' => 100
            ],
            'row_2' => [
                'col' => 200
            ],
            'row_3' => [
                'col' => 300
            ]
		];

		$filtered_data = $data->filter_by_rows(['row', 'row_3']);

		$this->assertEquals(['row', 'row_3'], $filtered_data->get_row_names());

		$expected_data = [
            'row' => [
                'col' => 100
            ],
            'row_3' => [
                'col' => 300
            ]
		];
		$this->assertEquals($expected_data, $filtered_data->data);
	}

		public function testFilterByCol() {
		$data = new Data;
		$data->data = [
            'row' => [
                'col_1' => 100
            ],
            'row_2' => [
                'col_2' => 200
            ],
            'row_3' => [
                'col_3' => 300
            ]
		];

		$filtered_data = $data->filter_by_columns(['col_1', 'col_3']);

		$this->assertEquals(['col_1', 'col_3'], $filtered_data->get_column_names());
	}

    public function testGetTotalByRow()
    {
        $data = new Data;
        $data->data = [
            'row' => [
                'col_1' => 100
            ],
            'row_2' => [
                'col_2' => 200
            ],
            'row_3' => [
                'col_3' => 300
            ],
            'row_4' => [],
            'row_5' => [
                'col_4' => 0
            ],
            'row_6' => [
                'col_1' => 12,
                'col_2' => 8
            ]
        ];

        $this->assertEquals(100, $data->get_total_by_row('row'));
        $this->assertEquals(200, $data->get_total_by_row('row_2'));
        $this->assertEquals(300, $data->get_total_by_row('row_3'));
        $this->assertEquals(0, $data->get_total_by_row('row_4'));
        $this->assertEquals(0, $data->get_total_by_row('row_5'));
        $this->assertEquals(20, $data->get_total_by_row('row_6'));
    }


	public function testFiltered_by_rows_and_cols(){
		$data = new Data;
		$data->data = [
            'row' => [
                'col_1' => 100
            ],
            'row_2' => [
                'col_2' => 200
            ],
            'row_3' => [
                'col_3' => 300
            ]
		];

		$row_filter = ['row', 'row_3'];
		$col_filter = ['col_3'];
		$expected_result = [
            'row' => [
            ],
            'row_3' => [
                'col_3' => 300
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
            'r1' => [
                'c' => 2
            ],
            'r3' => [
                'c' => 5
            ],
            'r2' => [
                'c' => 3
            ]
		];

		$this->assertEquals(10, $data->get_total_by_column('c'));
	}

    public function testCleanedEmptyRows(){
        $data = new Data;
        $data->data = [
            'row' => [
                'col_1' => 100
            ],
            'row_2' => [
                'col_2' => 0
            ],
            'row_3' => [
                'col_3' => 300
            ]
        ];

        $expected_result = [
            'row' => [
                'col_1' => 100
            ],
            'row_3' => [
                'col_3' => 300
            ]
        ];
        $data_filtered = $data->clean_empty_rows();

        $this->assertEquals($expected_result, $data_filtered->data);

    }

    public function testCleanedEmptyColumns(){
        $data = new Data;
        $data->data = [
            'row' => [
                'col_1' => 100
            ],
            'row_2' => [
                'col_2' => 0
            ],
            'row_3' => [
                'col_3' => 300
            ]
        ];

        $expected_result = [
            'row' => [
                'col_1' => 100
            ],
            'row_3' => [
                'col_3' => 300
            ],
            'row_2' => []
        ];
        $data_filtered = $data->clean_empty_columns();

        $this->assertEquals($expected_result, $data_filtered->data);

    }

    public function testCleanedEmptyColumns2(){
        $data = new Data;
        $data->data = [
            'row' => [
                'col_1' => 100
            ],
            'row_2' => [
                'col_1' => 20,
                'col_2' => 0
            ],
            'row_3' => [
                'col_2' => 0,
                'col_3' => 300,
                'col_4' => 12
            ]
        ];

        $expected_result = [
            'row' => [
                'col_1' => 100
            ],
            'row_3' => [
                'col_3' => 300,
                'col_4' => 12
            ],
            'row_2' => [
                'col_1' => 20
            ]
        ];
        $data_filtered = $data->clean_empty_columns();

        $this->assertEquals($expected_result, $data_filtered->data);

    }

    public function testCleanedEmptyColumns3(){
        $data = new Data;
        $data->data = [
            'row' => [
                'col_1' => 100,
                'col_2' => 10
            ],
            'row_2' => [
                'col_1' => 20,
                'col_2' => 0
            ]
        ];

        $expected_result = [
            'row' => [
                'col_1' => 100,
                'col_2' => 10
            ],
            'row_2' => [
                'col_1' => 20,
                'col_2' => 0
            ]
        ];
        $data_filtered = $data->clean_empty_columns();

        $this->assertEquals($expected_result, $data_filtered->data);

    }
}
