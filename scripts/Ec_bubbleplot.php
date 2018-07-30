<?php
include("../src/Ec_chart_tax_funcs.php");
include("../data/php/00.total_counts.php");
include("../src/Chart.php");
include("../src/environmental_PR.php");

$ecs = ["4.1.1.39", "2.3.1.169", "4.2.1.120", "6.2.1.40", "6.2.1.36", "1.3.1.84", "4.1.3.46", "1.2.1.75", "5.4.1.3", "4.2.1.153", "6.2.1.18"];
//$methane_ecs = ["2.6.1.45", "1.1.1.81", "2.2.1.2", "2.2.1.3", "1.14.13.25","1.14.18.3", "1.7.2.1", "2.1.1.245", "2.1.1.249", "2.1.1.247", "2.1.1.251", "2.1.1.86", "2.1.1.90", "2.1.1.246", "2.1.1.248", "2.1.1.252", "2.1.1.253"];//, "2.1.1.250"];

$mg_files = glob("../data/ec_counts/Mg_*txt");

$mg_site_ec_count_arr = make_site_ec_coun_arr($mg_files, "_ec_counts.txt", $ecs, 5);

$mg_site_ec_rel_ab = get_relab_from_site_count_arr($mg_site_ec_count_arr, $mg_count_arr);
$mg_oxy_sites = load_oxy_sites();
$mg_oxy_def_by_sites = define_oxygen_layer($mg_oxy_sites, $mg_site_ec_count_arr);
$color_list = color_by_oxy_def($mg_oxy_def_by_sites);

$mg_oxic_list = get_site_names_by_oxy_def($mg_oxy_def_by_sites, "oxic");
$mg_up_oxycline_list = get_site_names_by_oxy_def($mg_oxy_def_by_sites, "up_oxycline");
$mg_low_oxygen_list = get_site_names_by_oxy_def($mg_oxy_def_by_sites, "low_oxygen");
$mg_anoxic_list = get_site_names_by_oxy_def($mg_oxy_def_by_sites, "anoxic");
$low_down_oxycline_list = get_site_names_by_oxy_def($mg_oxy_def_by_sites, "low_down_oxycline");
$mg_down_oxycline_list = get_site_names_by_oxy_def($mg_oxy_def_by_sites, "down_oxycline");
$mg_list_by_oxygen = array_merge($mg_oxic_list, $mg_up_oxycline_list,$mg_low_oxygen_list, $mg_anoxic_list, $low_down_oxycline_list, $mg_down_oxycline_list);
$test_s = order_sites_by_oxygen_gradient($mg_oxy_sites, $mg_site_ec_rel_ab);

$mg_bubble_plot = New Ec_chart;
$mg_bubble_plot->delta_x = 45;
$mg_bubble_plot->delta_y = 12;
$mg_bubble_plot->data = $mg_site_ec_rel_ab;
$mg_bubble_plot->row_names = $mg_list_by_oxygen;
$mg_bubble_plot->bubble_scale = 150000;
$mg_bubble_plot->get_color = function($row_name, $col_name) {
  global $color_list;
  return $color_list[$row_name];
};

$mg_bubble_plot_2 = New Ec_chart;
$mg_bubble_plot_2->delta_x = 45;
$mg_bubble_plot_2->delta_y = 12;
$mg_bubble_plot_2->data = $mg_site_ec_rel_ab;
$mg_bubble_plot_2->row_names = $test_s;
$mg_bubble_plot_2->bubble_scale = 150000;
$mg_bubble_plot_2->get_color = function($row_name, $col_name) {
  global $color_list;
  return $color_list[$row_name];
};

echo '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="10000" width="15000">';


$mg_bubble_plot->draw(200, 200);
$mg_bubble_plot_2->draw(950, 200);


echo '</svg>';