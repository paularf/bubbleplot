<?php

include_once("../src/02.funcs.php");

$id_station_name = make_sample_names();
$id_bound_count = make_id_bound_counts($file = "../data/EF_IPL_Processing_fianl_corrected.csv");
$total_id_counts = total_id_counts($id_bound_count);

$id_bound_relab = id_bound_relab($id_bound_count, $total_id_counts);


$sorted_id_bound_count = sort_id_bound_counts($id_bound_relab);

$st_depth_bound_count = make_station_deep_bound_count_arr($sorted_id_bound_count, $id_station_name);
//print_r($id_station_name);
//write_out_table($table_name = "relab_per_bound.csv", $st_depth_bound_count);