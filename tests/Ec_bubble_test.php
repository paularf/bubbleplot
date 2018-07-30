<?php
include("../src/Ec_chart_tax_funcs.php");
include("total_counts_test.php");
include("../src/Ec_chart.php");

$ecs = ["4.1.1.39", "2.3.1.169", "2.3.3.8"];
$files = glob("*txt");
$site_ec_count_arr = make_site_ec_coun_arr($files, "_ec_count.txt", $ecs, $limit = 2);
$site_ec_rel_ab = get_relab_from_site_count_arr($site_ec_count_arr, $mg_arr);



print_r($site_ec_rel_ab);

$test_bubble_plot = New Ec_chart;
$test_bubble_plot->delta_x = 15;
$test_bubble_plot->delta_y = 12;
$test_bubble_plot->data = $site_ec_rel_ab;

$test_bubble_plot->bubble_scale = 800;

echo '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="1000" width="1500">';


$test_bubble_plot->draw(180, 210);


echo '</svg>';