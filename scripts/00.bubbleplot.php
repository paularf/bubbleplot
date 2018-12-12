<?php
require_once('../data/php/00.total_counts.php');
require_once('../src/01.taxa_funcs.php');
require_once('../src/02.environmental.php');
require_once('../src/03.leyend.php');
require_once('../src/04.Chart.php');

$ecs = ["4.1.1.39", "2.3.3.8", "6.2.1.18", "2.3.1.169", "6.2.1.40", "1.3.1.84", "5.4.1.3", "4.2.1.153", "4.1.3.46", "1.2.1.75", "6.2.1.36", "4.2.1.120"];

$sites_counts = [];
$sites_rel_ab_custom = [];
foreach (glob("../data/tax_grouped/Mg_*.test.test.final_3.taxa") as $file){
  $site = basename($file, ".test.test.final_3.taxa");
  $taxa_counts = make_ec_tax_count_arr($file, $ecs, 5);
  $sites_counts[$site] = $taxa_counts;
  if ( isset($mg_count_arr[$site]))
    $sites_rel_ab_custom[$site] = get_relab_from_ec_tax_count_arr($taxa_counts, $mg_count_arr[$site]);
}


$taxa_names = get_taxa_names($sites_rel_ab_custom);
$oxy_sites = load_oxy_sites();
//print_r($oxy_sites);
$oxy_sites_r = load_oxy_sites_r();
//print_r($oxy_sites_r);
$oxy_colors = define_oxygen_layer($oxy_sites, $sites_counts);
//print_r($oxy_colors);
$oxy_colors_r = define_oxygen_layer($oxy_sites_r, $sites_counts);
//print_r($oxy_colors_r);
$site_oxy_list = get_site_names_by_oxy_def_r($oxy_colors_r, 'red');
//$site_oxic_list = get_site_names_by_oxy_def($oxy_colors, 'green');
//$site_oxic_list = ['Mg_Oxic_HOT186_25m_2_DNA_454', 'Mg_Oxic_HOT186_25m_DNA_454','Mg_ETSP_MOOMZ1_15m_DNA_454', 'Mg_ETSP_MOOMZ2_35m_DNA_454', 'Mg_ETNP_OMZoMBiE_2013_St6_30m_DNA_IluMS', 'Mg_ETNP_OMZoMBiE_2013_St10_30m_DNA_IluMS'];
$site_oxic_list = get_site_names_by_oxy_def($oxy_colors, 'green');
$site_oxycline_list = get_site_names_by_oxy_def($oxy_colors, 'blue');
$site_anoxic_list = get_site_names_by_oxy_def($oxy_colors, 'red');
$non_defined_site_list= get_site_names_by_oxy_def($oxy_colors, 'black');
$order_by_oxygen_level_list = array_merge($site_oxic_list, $site_oxycline_list, $site_anoxic_list);

$chart = new Chart;
$chart->delta_x = 15;
$chart->delta_y = 12;

//$chart->column_names = $order_by_oxygen_level_list;//$taxa_names; //['4.1.1.39']
$chart->row_names = $order_by_oxygen_level_list;//["4.1.1.39", "2.3.3.8", "6.2.1.18", "2.3.1.169", "1.2.1.75", "6.2.1.36", "6.2.1.40"];//['Cyanobacterias'];
//$chart->site_name_filters = ['Mt_', '_cDNA_454', '_cDNA_IluMs', '_cDNA_IluMS', '_454', 'IT'];
//$chart->site_name_filters = ['Mg_', '_DNA_454', '_DNA_IluMs', '_DNA_IluMS', '_454', 'IT', 'PA5', 'PA2'];
//$chart->data = $sites_rel_ab_custom;
$chart->data = flip_big_group_row_col_names($sites_rel_ab_custom);

$chart->bubble_scale = 150000; //100000 for mg
$chart->get_color = function($big_group, $row_name, $col_name) {
  global $oxy_colors;
  return $oxy_colors[$row_name]['color'];
  //global $ec_colors;
  //return $ec_colors[$big_group];
};

//leyend 
$max = get_max($sites_rel_ab_custom); 
$min = get_min($sites_rel_ab_custom);
$dif = $max - $min;
//echo $max, "\t", $min;
//$leyend_scale = range($min, $max, $dif/3); //no borrar, de aquí salen todos los valores de la leyenda!
$leyend_scale = [ 1.5e-7, 1.5e-5, 1.5e-4, 2.5e-4]; //mg

$scientific_notation = [];
foreach ($leyend_scale as $value){
  $scient = formatScientific($value);
  $scientific_notation[] = $scient;
}


echo '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="1000" width="1500">';
$chart->draw(180, 210);

//draw_leyend (284, 430, $leyend_scale, $scientific_notation); //mt
draw_leyend (284, 700, $leyend_scale, $scientific_notation); //mg


echo '</svg>';