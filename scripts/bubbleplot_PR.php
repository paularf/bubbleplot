<?php
require_once('../data/php/00.total_counts.php');
require_once('../src/01.taxa_funcs.php');
require_once('../src/environmental_PR.php');
require_once('../src/04.Chart.php');

$ecs = ["4.1.1.39", "2.3.3.8", "6.2.1.18", "2.3.1.169", "6.2.1.40", "1.3.1.84", "5.4.1.3", "4.2.1.153", "4.1.3.46", "1.2.1.75", "6.2.1.36", "4.2.1.120"];

$files = glob("../data/tax_grouped/Mg_*.test.test.final_3.taxa");
$end_name = ".test.test.final_3.taxa";

//**metagenomes

//abundancia relativa y rearreglo de las filas y las columnas para probar dibujo
$mg_site_ec_tax_relab = make_site_ec_tax_relab_arr($files, $end_name, $ecs, $mg_count_arr);

$mg_ec_site_tax_data = flip_big_group_row_col_names($mg_site_ec_tax_relab);
//oxigeno
$mg_oxy_sites = load_oxy_sites();
$mg_oxy_def_by_sites = define_oxygen_layer($mg_oxy_sites, $mg_site_ec_tax_relab);

//$mg_list_by_oxygen_gradient = order_sites_by_oxygen_gradient($mg_oxy_sites, $mg_site_ec_tax_relab);

//lista de metagenomas de acuerdo a la concentración de oxígeno, convertir esto en función y pasarlo a environmental_PR
$mg_oxic_list = get_site_names_by_oxy_def($mg_oxy_def_by_sites, "oxic");
$mg_suboxic_list = get_site_names_by_oxy_def($mg_oxy_def_by_sites, "suboxic");
$mg_low_oxygen_list = get_site_names_by_oxy_def($mg_oxy_def_by_sites, "low_oxygen");
$mg_anoxic_list = get_site_names_by_oxy_def($mg_oxy_def_by_sites, "anoxic");
$mg_list_by_oxygen = array_merge($mg_oxic_list, $mg_suboxic_list,$mg_low_oxygen_list, $mg_anoxic_list);
$color_list = color_by_oxy_def($mg_oxy_def_by_sites);
$test_r = get_site_names_by_depth($mg_list_by_oxygen);
$test_s = order_sites_by_oxygen_gradient($mg_oxy_sites, $mg_site_ec_tax_relab);

//print_r($test_r);

$mg_bubbleplot = new Chart;
$mg_bubbleplot->data = $mg_ec_site_tax_data;
$mg_bubbleplot->delta_x = 15;
$mg_bubbleplot->delta_y = 12;
$mg_bubbleplot->bubble_scale = 150000;
$mg_bubbleplot->row_names = $test_s;//$test_r;//$mg_list_by_oxygen;
$mg_bubbleplot->big_group = $ecs;
$mg_bubbleplot->get_color = function($big_group, $row_name, $col_name) {
  global $color_list;
  return $color_list[$row_name];
  //global $ec_colors;
  //return $ec_colors[$big_group];
};
echo '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="1000" width="1500">';
$mg_bubbleplot->draw(200, 200);


echo '</svg>';
