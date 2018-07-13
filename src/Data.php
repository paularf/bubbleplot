<?php

namespace paularf\bubbleplot;

class Data {
	public $data;

	function get_column_names() {
  		$result = [];
  		foreach ( $this->data as $rows ) {
    		foreach ( $rows as $columns ) {
      			foreach ( $columns as $col_name => $value ) {
        			if ( in_array($col_name, $result) ) throw new \Exception("Col name repetido");
        			;
        			$result[] = $col_name;
      			}
    		}
  		}
  		return $result;
	}

	function get_row_names() {
  		$result = [];
  		foreach ( $this->data as $rows ) {
    		foreach ( $rows as $row_name => $row ) {
        		if ( in_array($row_name, $result) ) throw new \Exception("Row name repetido");
        		$result[] = $row_name;
    		}
  		}
  		return $result;
	}
	function get_value($big_group, $row_name, $col_name){
  		if (isset($this->data[$big_group][$row_name][$col_name])) {
    		$value = $this->data[$big_group][$row_name][$col_name];
    		return $value;
    	}
    	else return 0;
	}

	function filter_by_rows($filtered_rows) {
		$new_data = [];
		foreach ( $this->data as $group => $rows ) {
			$new_data[$group] = [];
			foreach ( $filtered_rows as $row ) {
				if ( isset($rows[$row]))
					$new_data[$group][$row] = $rows[$row];
			}
		}

		$new_data_object = new Data;
		$new_data_object->data = $new_data;
		return $new_data_object;
	}

	function filter_by_columns($filtered_cols){
		$new_data = [];
		foreach($this->data as $group => $rows){
			foreach($rows as $row => $cols){
				$new_data[$group][$row] = [];
				foreach($filtered_cols as $filtered_col){
					if(isset($cols[$filtered_col]))
						$new_data[$group][$row][$filtered_col] = $cols[$filtered_col];
				}
			}
		}
		$new_data_object = new Data;
		$new_data_object->data = $new_data;
		return $new_data_object;
	}


}
