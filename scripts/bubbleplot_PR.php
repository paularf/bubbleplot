<?php
require_once('../data/php/00.total_counts.php');
require_once('../src/01.taxa_funcs.php');
require_once('../src/environmental_PR.php');
require_once('../src/04.Chart.php');
require_once('../src/leyend_PR.php');

//$ecs = ["4.1.1.39", "2.7.1.19", "2.3.1.169", "2.1.1.258", "4.2.1.120", "6.2.1.40", "6.2.1.36", "1.2.1.75","1.3.1.84", "5.4.1.3", "4.2.1.153", "6.2.1.18", "2.3.3.8"];
$ecs = ["4.1.1.39", "2.3.1.169", "4.2.1.120", "1.2.1.75","1.3.1.84", "5.4.1.3", "4.2.1.153", "6.2.1.18"];

$cogs = ["COG1850", /*"COG0572".*/ "COG1152", "COG1614", "COG2368"];

$ec_colors = ["4.1.1.39" => '#00a400', "2.7.1.19" => '#00a400', "2.3.1.169" => '#ff4000', "2.1.1.258" => '#ff4000', "4.2.1.120" => 'blue', "6.2.1.36" => 'purple', "1.2.1.75" =>'purple',"6.2.1.40" => 'purple', "1.3.1.84" => 'purple', "5.4.1.3" => '#ff00bf', "4.2.1.153" => '#ff00bf', "2.3.3.8" => '#ff0000', "6.2.1.18" => '#ff0000', "2.3.3.8" => '#ff0000'];
$cog_colors = ["COG1850" => '#00a400', "COG0572" => '#00a400', "COG1152" => '#ff4000', "COG1614" => '#ff4000', "COG0136" =>'purple', "COG2368" => 'blue', "COG0365"=> 'purple', "COG1541" => 'purple', "COG1064" => 'purple', "COG1804" => '#ff00bf', "COG3777"=> '#ff00bf'];
//**cogs
//$mg_files = glob("../data/cog_not_grouped/Mg*.not.grouped.taxa.txt");
//$end_name = ".not.grouped.taxa.txt";

//**ungrouped
$mg_files = glob("../data/not_grouped/Mg_*.not.grouped.taxa.txt");
$end_name = ".test.not.grouped.taxa.txt";

//**grouped
//$mg_files = glob("../data/grouped/Mg_*.taxa");
//$end_name = ".test.test.final_1.taxa";
//**metagenomes

//abundancia relativa y rearreglo de las filas y las columnas para probar dibujo
$ec_size_arr = add_average_sizes_per_ec();
// print_r($ec_size_arr);

///**** normalización sin gen_size
//$mg_site_ec_tax_relab = make_site_ec_tax_relab_arr($mg_files, $end_name, /*$cogs*/$ecs, $mg_count_arr, /*$ec_size_arr,*/ 0.000006);
//print_r($mg_site_ec_tax_relab);

//***** Normalización con gen size
$mg_site_ec_tax_relab_a = make_site_ec_rel_ab_with_ec_size($mg_files, $ecs, $mg_count_arr, $ec_size_arr, $end_name, $limit = 0.00000000);
$z = add_total_tax_ab_per_site_and_ec($mg_site_ec_tax_relab_a);
//print_r($z);
$y = get_most_abundant_tax_names_per_ec ($z, $ranking = 8);
$mg_site_ec_tax_relab = make_most_abundant_site_ec_tax_rel_ab($mg_site_ec_tax_relab_a, $y);
//print_r($y);
//var_dump(get_min($mg_site_ec_tax_relab));
//var_dump(get_max($mg_site_ec_tax_relab));

$mg_ec_site_tax_data = flip_big_group_row_col_names($mg_site_ec_tax_relab);
//oxigeno
$mg_ec_site_tax_data = reorder_arr_by_ecs($mg_ec_site_tax_data, /*$cogs*/$ecs);
//print_r($mg_ec_site_tax_data);
$mg_oxy_sites = load_oxy_sites("../data/not_grouped/Mg_ambientales.txt");
$mg_oxy_def_by_sites = define_oxygen_layer($mg_oxy_sites, $mg_site_ec_tax_relab);
//print_r($mg_oxy_def_by_sites);
$test_b = get_site_names_by_oxy_def($mg_oxy_def_by_sites, "anoxic");
//print_r($test_b);
$test_c = order_sites_def_by_oceans($test_b);
//print_r($test_c);
$test_z = order_sites_by_def_and_depth($mg_oxy_def_by_sites);


$color_list = color_by_oxy_def($mg_oxy_def_by_sites);
$ec_color_list = get_color_from_ec_colors_and_site_ec_array ($ec_colors, $mg_site_ec_tax_relab);
//print_r($ec_color_list);

$leyend_scale = [ 3.0e-9, 3.0e-8, 1.5e-7];
$scientific_notation = [];
foreach ($leyend_scale as $value){
  $scient = formatScientific($value);
  $scientific_notation[] = $scient;
}

$list_by_oxygen_and_geography = ["Mg_ETSP_Galathea_60m_DNA_454", "Mg_ETSP_MOOMZ3_80m_DNA_454", "Mg_ETSP_MOOMZ3_110m_DNA_454", "Mg_ETSP_MOOMZ3_150m_DNA_454", "Mg_ETSP_MOOMZ1_200m_DNA_454", "Mg_Arabian_Sea_OMZ_core_PA5_IT", "Mg_ETNP_OMZoMBiE_2013_St6_100m_DNA_IluMS", "Mg_ETNP_OMZoMBiE_2013_St10_125m_DNA_IluMs", "Mg_ETNP_OMZoMBiE_2013_St6_125m_DNA_IluMS", "Mg_ETNP_OMZoMBiE_2013_St10_300m_DNA_IluMS", "Mg_ETNP_OMZoMBiE_2013_St6_300m_DNA_IluMS"];

$mg_bubbleplot = new Chart;
$mg_bubbleplot->data = $mg_ec_site_tax_data;
$mg_bubbleplot->site_name_filters = ['Mg_', '_DNA_454', '_DNA_IluMs', '_DNA_IluMS', '_454', 'IT', 'PA5', 'PA2'];
$mg_bubbleplot->delta_x = 15;
$mg_bubbleplot->delta_y = 12;
$mg_bubbleplot->bubble_scale = 150000000;
$mg_bubbleplot->row_names = /*$list_by_oxygen_and_geography;*/$test_z;//$mg_list_by_oxygen;//$test_s;//$test_r;//
//$mg_bubbleplot->big_group = $ecs;
$mg_bubbleplot->get_color = function($big_group, $row_name, $col_name) {
  //global $color_list;
  //return $color_list[$row_name];
  global $ec_colors;
  return $ec_colors[$big_group];
	//global $cog_colors;
	//return $cog_colors[$big_group];
};

//***Metatranscriptomas
//**Not grouped
//$mt_files = glob("../data/Tax_not_gruped/Mt_*.test.not.grouped.taxa.txt");
$mt_files = glob("../data/grouped/Mt_*.taxa");

$mt_site_ec_tax_relab = make_site_ec_tax_relab_arr($mt_files, $end_name, $ecs, $mt_count_arr, 0.000008);

$mt_ec_site_tax_data = flip_big_group_row_col_names($mt_site_ec_tax_relab);
//oxigeno
$mt_oxy_sites = load_oxy_sites('../data/not_grouped/Mt_ambientales.txt');
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


echo '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="10000" width="80000">';
$mg_bubbleplot->draw(200, 200);
draw_ec_colors_leyend (500, 800, $leyend_scale, $scientific_notation, $metaome = "metagenome");
//draw_leyend (700, 800, $leyend_scale, $scientific_notation);

//$mt_bubbleplot->draw(100, 1100);
//draw_leyend (300, 1500, $leyend_scale, $scientific_notation, $metaome = "metatranscriptome");



echo '</svg>';
