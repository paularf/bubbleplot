<?php
require_once(__DIR__ . '/04.taxa_funcs.php');
require_once(__DIR__ . '/06.total_counts.php');
require_once(__DIR__ . '/05.environmental.php');
//require_once(__DIR__ . '/Chart.php');
require_once(__DIR__ . '/Chart_PR.php'); //by_columns
//require_once(__DIR__ . '/Chart_by_rows.php'); //by_rows
//require_once(__DIR__ . '/Chart_mt_by_rows.php'); //by_rows
require_once(__DIR__ . '/07.leyend.php');

//$ec_colors = ["4.1.1.39" => 'green', "2.3.3.8" => '#f48f42', "6.2.1.18" => '#f48f42', "2.3.1.169" => '#f4415c', "1.2.1.75" => '#414cf4', "6.2.1.36" => '#8342f4', "6.2.1.40" => '#8342f4'];
$sites_counts = [];
$sites_rel_ab_custom = [];

foreach (glob("Mt_*.test.test.final_3.taxa") as $file){
  $site = basename($file, ".test.test.final_3.taxa");
  $taxa_counts = get_taxa_counts_from_file_by_ec_taxa_name($file, $ecs, 5);
  $sites_counts[$site] = $taxa_counts;
  if ( isset($mt_count_arr[$site]))
    $sites_rel_ab_custom[$site] = get_rel_abs_custom_from_ec_tax_name_tax_count_array($taxa_counts, $mt_count_arr[$site]);
}


function get_color_from_site_ec($site, $ec){
  global $sites_colors;
    if (isset($sites_colors[$site][$ec]))
    return $sites_colors[$site][$ec];
  else return 'black';
}

function flip_big_group_row_col_names($array){
  $r = [];
  foreach($array as $site => $ec_tax_name_arr){
    foreach ($ec_tax_name_arr as $ec => $tax_value_arr){
      foreach ($tax_value_arr as $tax_name => $value){
        $r[$ec][$site][$tax_name] = $value;
        //$r[$ec][$tax_name][$site] = $value;
        //$r[$site][$tax_name][$ec] = $value;
      }
    }
  }
  return $r;
}

$taxa_names = get_taxa_names($sites_rel_ab_custom);
$oxy_sites = load_oxy_sites();
$oxy_colors = define_oxygen_layer($oxy_sites, $sites_counts);
$site_oxy_list = get_site_names_by_oxy_def($oxy_colors, 'red');
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
$chart->site_name_filters = ['Mt_', '_cDNA_454', '_cDNA_IluMs', '_cDNA_IluMS', '_454', 'IT'];
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

draw_leyend (284, 430, $leyend_scale, $scientific_notation); //mt
//draw_leyend (284, 700, $leyend_scale, $scientific_notation); //mg
//draw_variables_by_site(1200, 200, $oxy_sites);

//draw_scale_leyend_by_color(700, 650, $scale);

/*$chart->filter = function($big_group, $row_name, $col_name, $value) {
  global $oxy_colors;
  if ( $oxy_colors[$row_name]['color'] == 'red' ) return true;
  else return false;
};
$chart->draw(260, 150);

$chart->filter = function($big_group, $row_name, $col_name, $value) {
  global $oxy_colors;
  if ( $oxy_colors[$row_name]['color'] == 'blue' ) return true;
  else return false;
};
$chart->draw(260, 600);

$chart->filter = function($big_group, $row_name, $col_name, $value) { //está solo en función del site, los otros eestán demás, importa el orden? la coma podría ser un ó
  global $oxy_colors;
  if ( $oxy_colors[$row_name]['color'] == 'green' ) return true;
  else return false;
};
$chart->draw(260, 1100);*/

echo '</svg>';