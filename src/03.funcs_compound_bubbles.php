<?php
function get_carbon_number_from_compound_name_2($name){
	$final_name = "";
	$count = 0;
	$first_explode = explode(":", $name);
	$second_explode = explode("-", $first_explode[0]);
	$count = count($second_explode) - 1;
	$final_name = $second_explode[$count];
	return $final_name;
}

function make_id_bound_compound_counts($file, $arr_filter, $carbon = false){
	$id_bound_counts = [];
	$f = fopen($file, "r");
	while($line = fgets($f)){
		$line = trim($line);
		$cols = explode(",", $line);
		$id = $cols[0];
		$compound = $cols[1];
		$total = $cols[9];
		if($total == "total") continue;
		if($id == "3_01R") continue;
		if($total == "NA") continue;
		if(strpos($compound, ":") == false) continue;
		if($carbon == false) $bound = parse_compound_name($compound);
		if($carbon == true) $bound = get_carbon_number_from_compound_name_2($compound);
		//if(!in_array($bound, $arr_filter)) continue;
		if(!isset($id_bound_counts[$id][$bound][$compound])) $id_bound_counts[$id][$bound][$compound] = $total;
		else $id_bound_counts[$id][$bound][$compound] += $total;

	}
	fclose($f);
	return($id_bound_counts);
}

function get_class_from_compound_name($name){
	$final_name = "";
	$explode_name = explode("-", $name);
	$final_name = $explode_name[0];
	return $final_name;
}

function make_id_class_compound_counts($file, $arr_filter){
	$id_bound_counts = [];
	$f = fopen($file, "r");
	while($line = fgets($f)){
		$line = trim($line);
		$cols = explode(",", $line);
		$id = $cols[0];
		$compound = $cols[1];
		$total = $cols[9];
		$class = $cols[14];
		if($total == "total") continue;
		if($id == "3_01R") continue;
		if($total == "NA") continue;
		//if(strpos($compound, ":") == false) continue;
		$bound = parse_compound_name($compound);
		if($class == "BL") $class = get_class_from_compound_name($compound);
		//if(!in_array($class, $arr_filter)) continue;
		if(!isset($id_bound_counts[$id][$class][$compound])) $id_bound_counts[$id][$class][$compound] = $total;
		else $id_bound_counts[$id][$class][$compound] += $total;

	}
	fclose($f);
	return($id_bound_counts);
}
function make_total_class_compound_counts($id_class_compound_counts){
	$total_class_compound_counts = [];
	foreach($id_class_compound_counts as $class_compound_counts){
		foreach($class_compound_counts as $class => $compound_counts){
			foreach($compound_counts as $compound => $counts){
				if(!isset($total_class_compound_counts[$class][$compound])) $total_class_compound_counts[$class][$compound] = $counts;
				else $total_class_compound_counts[$class][$compound] += $counts;
			}
		}
	}
	return $total_class_compound_counts;
}

function top_total_class_compound_counts($total_class_compound_counts, $rank){
	$top_total_class_compund_counts = [];
	foreach($total_class_compound_counts as $class => $compound_counts){
		arsort($compound_counts);
		$i = 1;
		foreach($compound_counts as $compound => $counts){
			if($i > $rank) continue;
			else $top_total_class_compund_counts[$class][$compound] = $counts;
			$i++;
		}
	}
	return $top_total_class_compund_counts;
} 

function make_top_id_class_compound_relab($id_class_compound_relab, $top_class_compound_relab){
	$top_id_class_compound_relab = [];
	foreach($id_class_compound_relab as $id => $class_compound_relab){
		foreach($top_class_compound_relab as $class => $compound_counts){
			foreach($compound_counts as $compound => $counts){
				if(!isset($class_compound_relab[$class][$compound])) continue;
				$relab = $class_compound_relab[$class][$compound];
				$top_id_class_compound_relab[$id][$class][$compound] = $relab;
			}
		}
	}
	return $top_id_class_compound_relab;
}
function total_class_counts($id_class_compound_counts){
	$total_class_counts = [];
	foreach($id_class_compound_counts as $class_compound_counts){
		foreach($class_compound_counts as $class => $compound_counts){
			foreach($compound_counts as $counts){
				if(!isset($total_class_counts[$class])) $total_class_counts[$class] = $counts;
				else $total_class_counts[$class] += $counts;
			}	
		}
	}
	return $total_class_counts;
}

function total_compound_counts($id_class_compound_counts){
	$total_compound_counts = [];
	foreach($id_class_compound_counts as $class_compound_counts){
		foreach($class_compound_counts as $compound_counts){
			foreach($compound_counts as $compound => $counts){
				if(!isset($total_compound_counts[$compound])) $total_compound_counts[$compound] = $counts;
				else $total_compound_counts[$compound] += $counts;
			}
		}
	}
	arsort($total_compound_counts);
	return $total_compound_counts;
}


function make_id_bound_compound_relab_by_class_counts($total_class_counts, $id_class_compound_counts){
	$id_class_compound_relab = [];
	foreach($id_class_compound_counts as $id => $class_compound_counts){
		if($id == "3_01R") continue;
		foreach($class_compound_counts as $class => $compound_counts){
			$total = $total_class_counts[$class];
			foreach($compound_counts as $compound => $counts){
				$relab = $counts/$total;
				$id_class_compound_relab[$id][$class][$compound] = $relab;
			}
		}
	}
	return $id_class_compound_relab;	
}

function make_id_bound_compound_relab($total_id_counts, $id_bound_compound_counts){
	$id_bound_compound_relab = [];
	foreach($id_bound_compound_counts as $id => $bound_compound_counts){
		$total = $total_id_counts[$id];
		foreach($bound_compound_counts as $bound => $compound_counts ){
			foreach($compound_counts as $compound => $counts){
				$relab = $counts/$total;
				$id_bound_compound_relab[$id][$bound][$compound] = $relab;
			}
		}
	}
	return $id_bound_compound_relab;
}

function total_bound_counts($id_bound_counts){
	$total_bound_counts = [];
	foreach($id_bound_counts as $id => $bound_counts){
		if($id == "3_01R") continue;
		foreach($bound_counts as $bound => $counts){
			if(!isset($total_bound_counts[$bound])) $total_bound_counts[$bound] = $counts;
			else $total_bound_counts[$bound] += $counts;
		}
	}
	return $total_bound_counts;
}
function make_id_bound_compound_relab_by_bound_counts($total_bound_counts, $id_bound_compound_counts){
	$id_bound_compound_relab = [];
	foreach($id_bound_compound_counts as $id => $bound_compound_counts){
		foreach($bound_compound_counts as $bound => $compound_counts){
			if(!isset($total_bound_counts[$bound])) continue;
			$total = $total_bound_counts[$bound];
			foreach($compound_counts as $compound => $counts){
				$relab = $counts/$total;
				//$relab = exp($relab);
				$id_bound_compound_relab[$id][$bound][$compound] = $relab;
			}
		}
	}
	return $id_bound_compound_relab;
}

function order_arr_by_compound($id_bound_compound_relab){
	$sorted_arr = [];
	foreach($id_bound_compound_relab as $id => $bound_compound_relab){
		ksort($bound_compound_relab);
		$sorted_arr[$id] = $bound_compound_relab;
	}
	return $sorted_arr;
}
function get_station_type_by_name($id){
	$station_name = "";
	$explode_name = explode("_", $id);
	$station_name = $explode_name[1];
	return $station_name;
}
function make_st_bound_compound_relab($id_station_name, $id_bound_compound_relab){
	$st_bound_compound_relab = [];
	foreach($id_bound_compound_relab as $id => $bound_compound_relab){
		if(!isset($id_station_name[$id])) continue;
		$st = $id_station_name[$id][0];
		$st_bound_compound_relab[$st] = $bound_compound_relab;
	}
	return $st_bound_compound_relab;
}

//vamos a quitar los que son muy poco abundantes en el slope respecto a la trench.

function make_typest_compound_sum($st_class_compound_relab){
	$typest_compound_sum = [];
	foreach($st_class_compound_relab as $st => $class_compound_relab){
		$type_st = get_station_type_by_name($st);
		foreach($class_compound_relab as $compound_relab){
			foreach($compound_relab as $compound => $relab){
				if(!isset($typest_compound_sum[$type_st][$compound])) $typest_compound_sum[$type_st][$compound] = $relab;
				else $typest_compound_sum[$type_st][$compound] += $relab;
			}
		}
	}
	return $typest_compound_sum;
}

function filter_st_class_compound_relab($st_class_compound_relab, $typest_compound_total, $filter, $type_st){
	$filtered_arr = [];
	foreach($st_class_compound_relab as $st => $class_compound_relab){
		foreach($class_compound_relab as $class => $compound_relab){
			foreach($compound_relab as $compound => $relab){
				if(isset($typest_compound_total[$type_st][$compound])) continue;
				else $filtered_arr[$st][$class][$compound]  = $relab;
			}
		}
	}
	return $filtered_arr;
}