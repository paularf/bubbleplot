<?php
require_once(__DIR__ . '/04.taxa_funcs.php');
require_once(__DIR__ . '/06.total_counts.php');
require_once(__DIR__ . '/05.environmental.php');
require_once(__DIR__ . '/Chart.php');
define('DELTA_X', 15); //constante!!
define('DELTA_Y', 10);

$ec_colors = ["4.1.1.39" => 'green', "2.3.3.8" => '#f48f42', "6.2.1.18" => '#f48f42', "2.3.1.169" => '#f4415c', "1.2.1.75" => '#414cf4', "6.2.1.36" => '#8342f4', "6.2.1.40" => '#8342f4'];

//utilizamos el 04.taxa_funcs pararecorrer todos los archivos en busca de los ecs y los tax_names, sus abundancias relativas por sitios

$sites_counts = [];
$sites_rankings = [];
$sites_rel_abs = [];
$sites_rel_ab_custom = [];


foreach (glob("Mg_*.test.test.final.taxa") as $file){
  $site = basename($file, ".test.test.final.taxa");
  $taxa_counts = get_taxa_counts_from_file_by_ec_taxa_name($file, $ecs, 4);
  $sites_counts[$site] = $taxa_counts;
  $sites_rankings[$site] = get_rankings_from_ec_tax_name_tax_count_array($taxa_counts, 5);
  $sites_rel_ab[$site] = get_rel_abs_from_ec_tax_name_tax_count_array($taxa_counts);
  if ( isset($mg_count_arr[$site]))
    $sites_rel_ab_custom[$site] = get_rel_abs_custom_from_ec_tax_name_tax_count_array($taxa_counts, $mg_count_arr[$site]);
}

$taxa_names = get_taxa_names($sites_rankings);
$sites_colors = get_color_from_ec_colors_and_site_ec_array ($ec_colors, $sites_counts);
//var_dump($sites_rel_ab_custom);
var_dump(get_site_ec_tax_names_rel_ab_custom_array("Mg_Arabian_Sea_OMZ_core_PA5_IT", "4.1.1.39", $taxa_names));

function get_rel_ab_from_site_ec_tax_name($site, $ec, $tax_name){
  global $sites_rel_ab;
  if (isset($sites_rel_ab[$site][$ec][$tax_name])) 
    return $sites_rel_ab[$site][$ec][$tax_name];
  else return 0;
}

function get_ranking_from_site_ex_tax_name($site, $ec, $tax_name){
  global $sites_rankings; //global trae la variable a la función, si no, habría que definirla aquí de nuevo
  if (isset($sites_rankings[$site][$ec][$tax_name]))
    return $sites_rankings[$site][$ec][$tax_name];
  else return 0;
}

function get_rel_ab_custom_from_site_ec_tax_name($site, $ec, $tax_name){
  global $sites_rel_ab_custom;
  if (isset($sites_rel_ab_custom[$site][$ec][$tax_name]))
    return $sites_rel_ab_custom[$site][$ec][$tax_name];
  else return 0;
}

function get_site_ec_tax_names_rel_ab_custom_array($site, $ec, $tax_names){
  $col_array = [];
  foreach ($tax_names as $tax_name){
    $rel_ab = get_rel_ab_custom_from_site_ec_tax_name($site, $ec, $tax_name);
    $col_array[$site][$ec][$tax_name] = $rel_ab;
  } 
  return $col_array;
}


function get_count_from_site_ec_tax_name($site, $ec, $tax_name){
  global $sites_counts;
  if (isset($sites_counts[$site][$ec][$tax_name]))
    return $sites_counts[$site][$ec][$tax_name];
  else return 0;
}
function get_color_from_site_ec($site, $ec){
  global $sites_colors;
    if (isset($sites_colors[$site][$ec]))
    return $sites_colors[$site][$ec];
  else return 'black';
}

//dibuja los tax_names hacia abajo en la misma x.


//texto con un ángulo de rotación
function draw_text($x, $y, $text, $rotation_angle = 0, $size = 8){
      echo <<<EOF
      <text
           font-family="Verdana"
           transform="translate($x, $y)rotate(-$rotation_angle)"
           font-size="$size"
           fill="black">
           $text
      </text>
EOF;
}  
Function draw_tax_names($x, $y, $taxa_names){
  foreach ($taxa_names as $tax_name){
    $y += DELTA_Y; 
    draw_text($x, $y, $tax_name, 0, $size = 10)
  }
}
function draw_row_text($x, $y, $texts){
  $current_x = $x;
  foreach ($texts as $text){
    draw_text($current_x, $y, $text, '90', '8');
    $current_x += DELTA_X;
  }
}


function draw_line($x1, $y1, $x2, $y2, $stroke, $width = 4) {
  printf('<line x1="%f" y1="%f" x2="%f" y2="%f" style="stroke:%s;stroke-width:%f" />', $x1, $y1, $x2, $y2, $stroke, $width);
  echo "\n";
}

function draw_bubble($x, $y, $value, $color = 'red') {
  printf('<ellipse cx="%f" cy="%f" rx="%f" ry="%f" fill-opacity="0.2" style="fill:%s;stroke:%s;stroke-width:1" />', $x, $y, $value, $value, $color, $color);
} // Los % significan las asignaciones del final en orden. Esto es parecido a concatenar un echo pero más ordenado

//Dibuja la elipse pero con el valor transformado!
function draw_bubble_transform($x, $y, $value, $color = 'red') {
  if ( $value == 0 ) return;
  //draw_text($x, $y, "$ranking", 0);
  $value = $value * 100000;
  draw_bubble($x, $y, $value, $color);
}

function draw_ec_colors_bubble($x, $y, $site, $ec, $tax_name){
  $rel_ab_custom = get_rel_ab_custom_from_site_ec_tax_name($site, $ec, $tax_name);
  $site_ec_color = get_color_from_site_ec($site, $ec);
  draw_bubble_transform($x, $y, $rel_ab_custom, $site_ec_color); 
} 
function draw_oxy_bubble($x, $y, $site, $ec, $tax_name) {
  $rel_ab_custom = get_rel_ab_custom_from_site_ec_tax_name($site, $ec, $tax_name);
  global $oxy_colors;
  if ($rel_ab_custom > 0)
  draw_bubble_transform($x, $y, $rel_ab_custom, $oxy_colors[$site]['color']);
}
//dibuja las elipses por línea, ideantificando, por sitio, ec y tax_name
function draw_bubble_by_site_ec_tax_name($x, $y, $site, $ec, $tax_name) {
  //draw_text($x, $y, "$ranking / $rel_ab / $rel_ab_custom / $count", 0);
  draw_oxy_bubble($x, $y , $site, $ec, $tax_name);
}


function draw_bubble_column_by_site_ecs_tax_name($x, $y, $site, $ec, $tax_names) {
  $current_y = $y;
  draw_text($x, $current_y - 10, $ec, '90', '8');
  foreach ( $tax_names as $tax_name){
    draw_bubble_by_site_ec_tax_name($x, $current_y, $site, $ec, $tax_name);
    $current_y += DELTA_Y;
  }
}


function draw_bubble_per_metaoma_zig_zag_names ($x, $y, $site, $ecs, $tax_names, $is_up){
  $current_x = $x;
  if ($is_up == true)
  draw_text($x -10, $y - 80, $site, '0', '6');
  if ($is_up == false)
  draw_text($x -10, $y - 60, $site, '0', '6');
  $contador = 0;
  foreach ($ecs as $ec){
  $current_ec_array = get_site_ec_tax_names_rel_ab_custom_array($site, $ec, $tax_names);
    foreach ($current_ec_array as $ecs_tax_names_rel_ab){
      foreach ($ecs_tax_names_rel_ab as $tax_names_rel_ab){
        if (array_sum($tax_names_rel_ab) == 0) continue;
        if (array_sum($tax_names_rel_ab) > 0){  
          draw_bubble_column_by_site_ecs_tax_name($current_x, $y, $site, $ec, $tax_names);
          $current_x += DELTA_X;
          $contador += 1;
        }  
      }
    }
  }
  return $contador;
}


//draw_bubble_per_metaoma(260, 110, 'Mg_Arabian_Sea_OMZ_core_PA5_IT', $ecs, $taxa_names);
function draw_bubble_metaomas_zig_zag_names($x, $y, $sites, $ecs, $tax_names){
  $current_y = $y;
  $current_x = $x;
  $is_up = true;
  foreach ($sites as $site => $ec_array){
    $contador = draw_bubble_per_metaoma_zig_zag_names ($current_x, $y, $site, $ecs, $tax_names, $is_up);
    $is_up = !$is_up;
    $current_x += (DELTA_X)* $contador;
  }
  foreach ($tax_names as $tax_name){
    draw_line($x, $current_y, $current_x, $current_y, 'black', 0.1);
    $current_y += DELTA_Y;
  }  
}


//dibuja elipse con coordenadas x, y y con radio proporticional a un valor normalizado (ranking, rel_ab,etc)
//$oxy_color = "";
//echo '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="1000" width="100000">';
$oxy_sites = load_oxy_sites();
var_dump($oxy_sites);
$oxy_colors = define_oxygen_layer($oxy_sites, $sites_counts);
//draw_bubble_column_by_site_ecs_tax_name(260, 110, "Mg_Arabian_Sea_OMZ_oxycline_PA2_IT", "4.1.1.39", $taxa_names);
//exit(1);
//draw_text(50, 100, get_color_from_site_ec("Mg_Arabian_Sea_OMZ_oxycline_PA2_IT", "4.1.1.39"));
//draw_tax_names(50, 105, $taxa_names);
//draw_bubble_per_metaoma_zig_zag_names(260, 110, "Mg_Arabian_Sea_OMZ_oxycline_PA2_IT", $ecs, $taxa_names, $is_up);
//draw_bubble_metaomas_zig_zag_names(260, 110, $sites_rel_ab_custom, $ecs, $taxa_names);
//echo '</svg>';

function flip($array) {
  $r = [];
  foreach ( $array as $ec =>$taxa_names ) {
    foreach ( $taxa_names as $taxa_name => $value) {
      $r[$taxa_name][$ec] = $value;
    }
  }
  return $r;
}

function big_flip($array) {
  $r = [];
  foreach ( $array as $k => $v ) {
    $r[$k] = flip($v);
  }
  return $r;
}


$chart = new Chart;
$chart->delta_x = 15;
$chart->delta_y = 10;
$chart->ecs = $ecs;
$chart->data = $sites_rel_ab_custom;
$chart->ecs = $chart->get_unique_taxa_names();
$chart->data = big_flip($sites_rel_ab_custom);
$chart->bubble_scale = 100000;
$chart->get_color = function($site, $ec, $tax_name) {
  global $oxy_colors;
  return $oxy_colors[$site]['color'];
};



echo '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="1000" width="100000">';

$chart->filter = function($site, $ec, $tax_name, $value) {
  global $oxy_colors;
  if ( $oxy_colors[$site]['color'] == 'red' ) return true;
  else return false;
};
$chart->draw(260, 110);

$chart->filter = function($site, $ec, $tax_name, $value) {
  global $oxy_colors;
  if ( $oxy_colors[$site]['color'] == 'blue' ) return true;
  else return false;
};
$chart->draw(260, 410);

$chart->filter = function($site, $ec, $tax_name, $value) {
  global $oxy_colors;
  if ( $oxy_colors[$site]['color'] == 'green' ) return true;
  else return false;
};
$chart->draw(260, 710);

echo '</svg>';