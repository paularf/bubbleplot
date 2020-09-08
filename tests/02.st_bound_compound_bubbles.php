<?php 
include_once("../src/02.funcs.php");
include_once("../src/03.funcs_compound_bubbles.php"); 
require_once('../src/04.Chart.php');
require_once('../src/leyend_PR.php');

$id_station_name = make_sample_names();
$file = "01.initial_table_test.csv";
$id_bound_count = make_id_bound_counts($file);
$total_id_counts = total_id_counts($id_bound_count);

$id_bound_compound_counts = make_id_bound_compound_counts($file,  $filter = [1, 2]); 

$id_bound_compound_relab = make_id_bound_compound_relab($total_id_counts, $id_bound_compound_counts);

$id_bound_compound_relab = order_arr_by_compound($id_bound_compound_relab);

$st_bound_compound_relab = make_st_bound_compound_relab($id_station_name, $id_bound_compound_relab);
$bound_st_compound_relab = flip_id_bound_compound($st_bound_compound_relab);
print_r($st_bound_compound_relab);

$colors = ["Continental_slope_st12_529m" => "green", "Continental_slope_st08_539m" => "green", "Continental_slope_st22_545m" => "green", "Continental_slope_st07_920m" => "green", "Continental_slope_st05_957m" => "green", "Continental_slope_st11_1113m" => "green", "Continental_slope_st04_1200m" => "green", "Inner_trench_st08_01cm_7734m" => "blue", "Inner_trench_st03_01cm_7890m" => "blue", "Inner_trench_st04_01cm_8063m" => "blue", "Inner_trench_st08_12cm_7734m" => "blue", "Inner_trench_st03_12cm_7890m" => "blue", "Inner_trench_st04_12cm_8063m" => "blue", "Inner_trench_st08_23cm_7734m" => "blue", "Inner_trench_st03_23cm_7890m" => "blue", "Inner_trench_st04_23cm_8063m" => "blue"];

$st_depth_bound_relab_bubbleplot = new Chart;
$st_depth_bound_relab_bubbleplot->data = $bound_st_compound_relab;
$st_depth_bound_relab_bubbleplot->delta_x = 15;
$st_depth_bound_relab_bubbleplot->delta_y = 12;
$st_depth_bound_relab_bubbleplot->row_names = ["Continental_slope_st12_529m" , "Continental_slope_st08_539m", "Continental_slope_st22_545m", "Continental_slope_st07_920m", "Continental_slope_st05_957m", "Continental_slope_st11_1113m", "Continental_slope_st04_1200m", "Inner_trench_st08_01cm_7734m", "Inner_trench_st08_12cm_7734m", "Inner_trench_st08_23cm_7734m", "Inner_trench_st03_01cm_7890m", "Inner_trench_st03_12cm_7890m", "Inner_trench_st03_23cm_7890m", "Inner_trench_st04_01cm_8063m", "Inner_trench_st04_12cm_8063m", "Inner_trench_st04_23cm_8063m"];
$st_depth_bound_relab_bubbleplot->bubble_scale = 1;
$st_depth_bound_relab_bubbleplot->get_color = function($big_group, $row_name, $col_name) {
  global $colors;
  return $colors[$row_name];
};



echo '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="10000" width="80000">';
$st_depth_bound_relab_bubbleplot->draw(200, 200);


echo '</svg>';