<?php
require_once('../data/php/00.total_counts.php');
require_once('../src/01.taxa_funcs.php');
require_once('../src/environmental_PR.php');
require_once('../src/04.Chart.php');
require_once('../src/leyend_PR.php');

$ecs = ["4.1.1.39", "2.3.1.169", "4.2.1.120", "6.2.1.40", "6.2.1.36", "1.3.1.84", "4.1.3.46", "1.2.1.75", "5.4.1.3", "4.2.1.153", "6.2.1.18"];

$mg_files = glob("../data/tax_grouped/Mg_*.test.test.final_3.taxa");
$end_name = ".test.test.final_3.taxa";

//**metagenomes

//abundancia relativa y rearreglo de las filas y las columnas para probar dibujo
$mg_site_ec_tax_relab = make_site_ec_tax_relab_arr($mg_files, $end_name, $ecs, $mg_count_arr);
//var_dump(get_min($mg_site_ec_tax_relab));
//var_dump(get_max($mg_site_ec_tax_relab));

$mg_ec_site_tax_data = flip_big_group_row_col_names($mg_site_ec_tax_relab);
//oxigeno
$mg_ec_site_tax_data = reorder_arr_by_ecs($mg_ec_site_tax_data, $ecs);
$mg_oxy_sites = load_oxy_sites();
$mg_oxy_def_by_sites = define_oxygen_layer($mg_oxy_sites, $mg_site_ec_tax_relab);
//print_r($mg_oxy_def_by_sites);
$test_z = order_sites_by_def_and_depth($mg_oxy_def_by_sites);


$color_list = color_by_oxy_def($mg_oxy_def_by_sites);

$leyend_scale = [ 1.5e-7, 1.5e-5, 1.5e-4, 2.0e-4];
$scientific_notation = [];
foreach ($leyend_scale as $value){
  $scient = formatScientific($value);
  $scientific_notation[] = $scient;
}

$mg_bubbleplot = new Chart;
$mg_bubbleplot->data = $mg_ec_site_tax_data;
$mg_bubbleplot->site_name_filters = ['Mg_', '_DNA_454', '_DNA_IluMs', '_DNA_IluMS', '_454', 'IT', 'PA5', 'PA2'];
$mg_bubbleplot->delta_x = 15;
$mg_bubbleplot->delta_y = 12;
$mg_bubbleplot->bubble_scale = 150000;
$mg_bubbleplot->row_names = $test_z;//$mg_list_by_oxygen;//$test_s;//$test_r;//
$mg_bubbleplot->big_group = $ecs;
$mg_bubbleplot->get_color = function($big_group, $row_name, $col_name) {
  global $color_list;
  return $color_list[$row_name];
  //global $ec_colors;
  //return $ec_colors[$big_group];
};

//***Metatranscriptomas

$mt_files = glob("../data/tax_grouped/Mt_*.test.test.final_3.taxa");
$mt_site_ec_tax_relab = make_site_ec_tax_relab_arr($mt_files, $end_name, $ecs, $mt_count_arr);

$mt_ec_site_tax_data = flip_big_group_row_col_names($mt_site_ec_tax_relab);
//oxigeno
$mt_oxy_sites = load_oxy_sites('../data/Tax_grouped/Mt_ambientales.txt');
$mt_oxy_def_by_sites = define_oxygen_layer($mt_oxy_sites, $mt_site_ec_tax_relab);

$mt_oxy_depth_list = order_sites_by_def_and_depth($mt_oxy_def_by_sites);

$mt_color_list = color_by_oxy_def($mt_oxy_def_by_sites);

$mt_bubbleplot = new Chart;
$mt_bubbleplot->data = $mt_ec_site_tax_data;
$mt_bubbleplot->site_name_filters = ['Mt_', '_cDNA_454', '_cDNA_IluMs', '_cDNA_IluMS', '_454', 'IT', 'PA5', 'PA2'];
$mt_bubbleplot->delta_x = 15;
$mt_bubbleplot->delta_y = 12;
$mt_bubbleplot->bubble_scale = 150000;
$mt_bubbleplot->row_names = $mt_oxy_depth_list;//$mt_list_by_oxygen;
$mt_bubbleplot->big_group = $ecs;
$mt_bubbleplot->get_color = function($big_group, $row_name, $col_name) {
  global $mt_color_list;
  return $mt_color_list[$row_name];
};


echo '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="10000" width="15000">';
$mg_bubbleplot->draw(200, 200);
draw_leyend (700, 640, $leyend_scale, $scientific_notation);

$mt_bubbleplot->draw(200, 1000);
draw_leyend (300, 1200, $leyend_scale, $scientific_notation);



echo '</svg>';
