<?php

namespace paularf\bubbleplot;

class Data {
	public $data = [];

	function get_column_names() {
  		$result = [];
  		foreach ( $this->data as $columns) {
            foreach ( $columns as $col_name => $value ) {
                $result[] = $col_name;
            }
        }
  		return $result;
	}

	function get_row_names() {
  		return array_keys($this->data);
	}

	function get_value($row_name, $col_name){
  		if (isset($this->data[$row_name][$col_name])) {
    		$value = $this->data[$row_name][$col_name];
    		return $value;
    	}
    	else return 0;
	}

	function get_total_by_row($row_name) {
        $total = 0;

	    if ( isset($this->data[$row_name]) ) {
	        $column = $this->data[$row_name];
	        foreach ( $column as $value ) {
	            $total += $value;
            }
        }
        return $total;
    }

	function get_total_by_column($col_name){
  		$total = 0;
  		foreach ($this->data as $row_name => $rows){
    		$total += $this->get_value($row_name, $col_name);
  		}
  		return $total;
	}


    /**
     * wachulin
     * @param $filtered_rows
     * @return Data
     */
	function filter_by_rows($filtered_rows) {
		$new_data = [];

        foreach ( $filtered_rows as $row ) {
            if ( isset($this->data[$row]))
                $new_data[$row] = $this->data[$row];
        }

		$new_data_object = new Data;
		$new_data_object->data = $new_data;
		return $new_data_object;
	}

    /**
     * @param $filtered_cols
     * @return Data
     */
	function filter_by_columns($filtered_cols) {
		$new_data = [];

        foreach($this->data as $row => $cols) {
            $new_data[$row] = [];
            foreach( $filtered_cols as $filtered_col ) {
                if(isset($cols[$filtered_col]))
                    $new_data[$row][$filtered_col] = $cols[$filtered_col];
            }
        }

		$new_data_object = new Data;
		$new_data_object->data = $new_data;
		return $new_data_object;
	}

    /**
     * @return Data
     */
	function clean_empty_rows() {
	    $filter_rows = [];
	    foreach ( $this->get_row_names() as $row_name ) {
	        $total = $this->get_total_by_row($row_name);
	        if ( $total > 0 ) {
	            $filter_rows[] = $row_name;
            }
        }
        return $this->filter_by_rows($filter_rows);
    }

    /**
     * @return Data
     * @throws \Exception
     */
    function clean_empty_columns() {
        $filter_columns = [];
        foreach ( $this->get_column_names() as $column_name ) {
            $total = $this->get_total_by_column($column_name);
            if ( $total > 0 ) {
                $filter_columns[] = $column_name;
            }
        }
        return $this->filter_by_columns($filter_columns);
    }


}
