<?php
require_once('../data/php/00.total_counts.php');
require_once('../src/01.taxa_funcs.php');
require_once('../src/environmental_PR.php');
require_once('../src/Chart_PR.php');

$ecs = ["4.1.1.39", "2.3.3.8", "6.2.1.18", "2.3.1.169", "6.2.1.40", "1.3.1.84", "5.4.1.3", "4.2.1.153", "4.1.3.46", "1.2.1.75", "6.2.1.36", "4.2.1.120"];

$files = glob("../data/tax_grouped/Mg_*.test.test.final_3.taxa");
$end_name = ".test.test.final_3.taxa";

//metagenomes
$mg_site_ec_tax_relab = make_site_ec_tax_relab_arr($files, $end_name, $ecs, $mg_count_arr);
$mg_oxy_sites = load_oxy_sites();
$mg_oxy_def_by_sites = define_oxygen_layer($mg_oxy_sites, $mg_site_ec_tax_relab);

//lista de metagenomas de acuerdo a la concentración de oxígeno, convertir esto en función y pasarlo a environmental_PR
$mg_oxic_list = get_site_names_by_oxy_def($mg_oxy_def_by_sites, "oxic");
$mg_suboxic_list = get_site_names_by_oxy_def($mg_oxy_def_by_sites, "suboxic");
$mg_anoxic_list = get_site_names_by_oxy_def($mg_oxy_def_by_sites, "anoxic");
$mg_list_by_oxygen = array_merge($mg_oxic_list, $mg_suboxic_list, $mg_anoxic_list);

echo '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="1000" width="1500">';

draw_column_text(200, 200, $mg_list_by_oxygen);
draw_line(200, 300, 400, 300, 'red', $width = 4);

echo '</svg>';