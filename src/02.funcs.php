<?php


function make_sample_names($file = "../data/sample_names.txt"){
	$sample_names = [];
	$f = fopen($file, "r");
	while($line = fgets($f)){
		$sample_name = "";
		$line = trim($line);
		$cols = explode("\t", $line);
		$origin = $cols[0];
		$station_cm = explode("_", $cols[1]);
		$depth = $cols[2];
		if($origin == "Inner_trench") {
			$station = $station_cm[0];
			$cm = $station_cm[1];
			$sample_name = "$origin"."_"."st"."$station"."_"."$cm"."cm"."_"."$depth"."m";
		}
		if($origin == "Continental_slope"){
			$station = $station_cm[1];

			$sample_name = "$origin"."_"."st"."$station"."_"."$depth"."m";
		}
		$sample_names[$cols[1]] = [$sample_name, $depth];
		
	}
	fclose($f);
	return $sample_names;
}

function parse_compound_name($name){
	$final_name = "";
	$explode_name = explode(":", $name);
	//$semi_final_name = explode("\"", $explode_name[1]);
	//$final_name = $semi_final_name[0];
	$final_name = $explode_name[1];
	return $final_name;
}
function get_carbon_number_from_compound_name($name){
	$final_name = "";
	$count = 0;
	$first_explode = explode(":", $name);
	$second_explode = explode("-", $first_explode[0]);
	$count = count($second_explode) - 1;
	$final_name = $second_explode[$count];
	return $final_name;
}
function make_id_bound_counts($file, $carbon = false){
	$id_bound_counts = [];
	$f = fopen($file, "r");
	while($line = fgets($f)){
		$line = trim($line);
		$cols = explode(",", $line);
		$id = $cols[0];
		$compound = $cols[1];
		$total = $cols[9];
		if($total == "NA") continue;
		if(strpos($compound, ":") == false) continue;
		if($carbon == false) $bound = parse_compound_name($compound);
		if($carbon == true) $bound = get_carbon_number_from_compound_name_2($compound);
		if(!isset($id_bound_counts[$id][$bound])) $id_bound_counts[$id][$bound] = $total;
		else $id_bound_counts[$id][$bound] += $total;

	}
	fclose($f);
	return($id_bound_counts);
}

function flip_id_bound_compound($id_bound_compound_counts){
	$flip = [];
	foreach($id_bound_compound_counts as $id => $bound_compound_counts){
		foreach($bound_compound_counts as $bound => $compound_counts){
			//if($bound == "5") continue;
			$flip[$bound][$id] = $compound_counts;
		}
	}
	return $flip;
}

function total_id_counts($id_bound_counts){
	$total_id_counts = [];
	foreach($id_bound_counts as $id => $bound_counts){
		foreach($bound_counts as $bound => $counts){
			if(!isset($total_id_counts[$id])) $total_id_counts[$id] = $counts;
			else $total_id_counts[$id] += $counts;
		}
	}
	return $total_id_counts;
}

function id_bound_relab($id_bound_counts, $total_id_counts){
	$id_bound_relab = [];
	foreach($id_bound_counts as $id => $bound_count){
		if(!isset($total_id_counts[$id])) continue;
		else $total = $total_id_counts[$id];
		foreach($bound_count as $bound => $count){
			$relab = $count/$total;
			$id_bound_relab[$id][$bound] = $relab;
		}
	}
	return $id_bound_relab;
}


function sort_id_bound_counts($id_bound_counts){
	$sorted_arr = [];
	foreach($id_bound_counts as $id => $bound_counts){
		ksort($bound_counts);
		$sorted_arr[$id] = $bound_counts;
	}
	return $sorted_arr;
}

function make_station_deep_bound_count_arr($id_bound_counts, $sample_names){
	$st_depth_bound_count = [];
	foreach($id_bound_counts as $id => $bound_counts){
		if(!isset($sample_names[$id])) continue;
		$st = $sample_names[$id][0];
		$depth = $sample_names[$id][1];
		$st_depth_bound_count[$st][$depth] = $bound_counts;
	}
	return $st_depth_bound_count;
}

function make_st_bound_count($st_depth_bound_count){
	$st_bound_count = [];
	foreach($st_depth_bound_count as $st => $depth_bound_count){
		foreach($depth_bound_count as $depth => $bound_count){
			foreach($bound_count as $bound => $count){
				if($bound == "0") $bound = "0.0";
				for ($i=1; $i < 13 ; $i++) { 
					if(isset($bound_count[$i])) continue;
					else $st_bound_count[$st][$i] = 0;
				}
				$st_bound_count[$st][$bound] = $count;
			}
		}
	}
	foreach($st_bound_count as $st => $bound_count_2){
		ksort($bound_count_2);
		$st_bound_count_2[$st] = $bound_count_2;
	}
	return $st_bound_count_2;
}

function write_out_table($table_name = "one_per_bound.csv", $st_depth_bound_count){
	$f = fopen($table_name, "w");
	fprintf($f, "station,depth,bound_0,bound_1,bound_2,bound_3,bound_4,bound_5,bound_6,bound_7,bound_8,bound_9,bound_10,bound_11,bound_12\n");
	foreach($st_depth_bound_count as $st => $depth_bound_count){
		$line = "";
		foreach($depth_bound_count as $depth => $bound_count){
			for ($i=0; $i < 13; $i++) {
				if(!isset($bound_count[$i])) $count = 0;
				else $count = $bound_count[$i];
				$line .= "$count,";	
			}		
				
		}
			fprintf($f, "$st,$depth,$line\n");
		}
	fclose($f);
}