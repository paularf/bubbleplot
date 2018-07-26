<?php
include("../src/Ec_chart_tax_funcs.php");
include("../data/php/00.total_counts.php");
include("../src/Ec_chart.php");
include("../src/environmental_PR.php");

$ecs = ["4.1.1.39", "2.3.1.169", "4.2.1.120", "6.2.1.40", "6.2.1.36", "1.3.1.84", "4.1.3.46", "1.2.1.75", "5.4.1.3", "4.2.1.153", "6.2.1.18"];

$mg_files = glob("../data/ec_counts/Mg_*txt");

$mg_site_ec_count_arr = make_site_ec_coun_arr($mg_files, "_ec_counts.txt", $ecs, $limit = 2);

$mg_site_ec_rel_ab = get_relab_from_site_count_arr($mg_site_ec_count_arr, $mg_count_arr);


$test_bubble_plot = New Ec_chart;
$test_bubble_plot->delta_x = 25;
$test_bubble_plot->delta_y = 12;
$test_bubble_plot->data = $mg_site_ec_rel_ab;

$test_bubble_plot->bubble_scale = 150000;

echo '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="10000" width="15000">';


$test_bubble_plot->draw(200, 200);


echo '</svg>';