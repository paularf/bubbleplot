<?php

include_once("../src/02.funcs.php");
include_once("../src/03.funcs_compound_bubbles.php"); 
require_once('../src/04.Chart.php');
require_once('../src/leyend_PR.php');

$id_station_name = make_sample_names();
$file = "../data/EF_IPL_Processing_fianl_corrected.csv";
$id_bound_count = make_id_bound_counts($file);
$total_bound_counts = total_bound_counts($id_bound_count);

//$id_bound_compound_counts = make_id_bound_compound_counts($file,  $filter = [7,8,9,10, 11, 12]); 
$id_class_compound_counts = make_id_class_compound_counts($file, ["AR", "NA", "Gly-Cer", "PME/PDME"]);

$total_class_counts = total_class_counts($id_class_compound_counts);

$id_class_compound_relab = make_id_bound_compound_relab_by_class_counts($total_class_counts, $id_class_compound_counts);

$total_class_compound_counts = make_total_class_compound_counts($id_class_compound_counts);
$top_total_class_compound_counts = top_total_class_compound_counts($total_class_compound_counts, $rank = 100000000000000000000000);
//** relab
$top_id_class_compound_relab = make_top_id_class_compound_relab($id_class_compound_relab, $top_total_class_compound_counts);
//print_r($top_id_class_compound_counts);

//*** sin relab
$top_id_class_compound_relab = make_top_id_class_compound_relab($id_class_compound_counts, $top_total_class_compound_counts);
$st_class_compound_relab = make_st_bound_compound_relab($id_station_name, $top_id_class_compound_relab);

///**** los que son poco abundantes en el slope respecto a la fosa

$typest_compound_total = make_typest_compound_sum($st_class_compound_relab);

$st_class_compound_relab = filter_st_class_compound_relab($st_class_compound_relab, $typest_compound_total, 0, "slope");

//print_r($typest_compound_total);


$class_st_compound_relab = flip_id_bound_compound($st_class_compound_relab);
//print_r($st_bound_compound_relab);
$min = get_min($class_st_compound_relab);
//print_r($min);

$leyend_scale = [3e-2, 1.5e-1, 3.5e-1, 7e-1];
$scientific_notation = [];
foreach ($leyend_scale as $value){
  $scient = formatScientific($value);
  $scientific_notation[] = $scient;
}

$colors = ["Continental_slope_st12_529m" => "green", "Continental_slope_st08_539m" => "green", "Continental_slope_st22_545m" => "green", "Continental_slope_st07_920m" => "green", "Continental_slope_st05_957m" => "green", "Continental_slope_st11_1113m" => "green", "Continental_slope_st04_1200m" => "green", "Inner_trench_st08_01cm_7734m" => "blue", "Inner_trench_st03_01cm_7890m" => "blue", "Inner_trench_st04_01cm_8063m" => "blue", "Inner_trench_st08_12cm_7734m" => "blue", "Inner_trench_st03_12cm_7890m" => "blue", "Inner_trench_st04_12cm_8063m" => "blue", "Inner_trench_st08_23cm_7734m" => "blue", "Inner_trench_st03_23cm_7890m" => "blue", "Inner_trench_st04_23cm_8063m" => "blue"];
print_r($class_st_compound_relab);

$st_depth_bound_relab_bubbleplot = new Chart;
$st_depth_bound_relab_bubbleplot->data = $class_st_compound_relab;
$st_depth_bound_relab_bubbleplot->delta_x = 15;
$st_depth_bound_relab_bubbleplot->delta_y = 12;
$st_depth_bound_relab_bubbleplot->row_names = ["Continental_slope_st12_529m" , "Continental_slope_st08_539m", "Continental_slope_st22_545m", "Continental_slope_st07_920m", "Continental_slope_st05_957m", "Continental_slope_st11_1113m", "Continental_slope_st04_1200m", "Inner_trench_st08_01cm_7734m", "Inner_trench_st08_12cm_7734m", "Inner_trench_st08_23cm_7734m", "Inner_trench_st03_01cm_7890m", "Inner_trench_st03_12cm_7890m", "Inner_trench_st03_23cm_7890m", "Inner_trench_st04_01cm_8063m", "Inner_trench_st04_12cm_8063m", "Inner_trench_st04_23cm_8063m"];
$st_depth_bound_relab_bubbleplot->bubble_scale = 0.1;
$st_depth_bound_relab_bubbleplot->get_color = function($big_group, $row_name, $col_name) {
  global $colors;
  return $colors[$row_name];
};



echo '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="500" width="10000">';
$st_depth_bound_relab_bubbleplot->draw(150, 100);
draw_leyend (300, 300, $leyend_scale, $scientific_notation, "bound");

echo '</svg>';
