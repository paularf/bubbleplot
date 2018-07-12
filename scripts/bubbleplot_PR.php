<?php
require_once('../data/php/00.total_counts.php');
require_once('../src/01.taxa_funcs.php');
require_once('../src/environmental_PR.php');

$ecs = ["4.1.1.39", "2.3.3.8", "6.2.1.18", "2.3.1.169", "6.2.1.40", "1.3.1.84", "5.4.1.3", "4.2.1.153", "4.1.3.46", "1.2.1.75", "6.2.1.36", "4.2.1.120"];

$files = glob("../data/tax_grouped/Mg_*.test.test.final_3.taxa");
$end_name = ".test.test.final_3.taxa";

//metagenomes
$mg_site_ec_tax_relab = make_site_ec_tax_relab_arr($files, $end_name, $ecs, $mg_count_arr);
$mg_oxy_sites = load_oxy_sites();
$mg_oxy_def_by_sites = define_oxygen_layer($mg_oxy_sites, $mg_site_ec_tax_relab);
$mg_oxic_list = get_site_names_by_oxy_def($mg_oxy_def_by_sites, "anoxic");

print_r($mg_site_ec_tax_relab);
