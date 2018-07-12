<?php

function load_oxy_sites($filename = '../data/Tax_grouped/Mg_ambientales.txt') {
  $oxy_sites = [];
  $file = fopen($filename, 'r');
  while($line = trim(fgets($file))){
    $cols = explode("\t", $line);
    $site_name = $cols[0];
    $oxygen = $cols[1];
    if(isset($cols[5])) $nitrite = $cols[5];
    else $nitrite = null; 
    if (!isset($oxygen)) $oxygen = null;
    $oxy_sites[$site_name] = [$nitrite, $oxygen];
  }
  return $oxy_sites;
  fclose($file);
}


function define_oxygen_layer($oxy_sites, $sites_counts){
  $oxy_def = [];
  $elem = "";
  foreach ($sites_counts as $sites => $ec_tax_arr){
      if (isset($oxy_sites[$sites])){ 
        $nitrite = $oxy_sites[$sites][0];
        $oxygen = $oxy_sites[$sites][1];
        if($nitrite >= 0.45 && $oxygen <= 2) $elem = "anoxic";
        else if($oxygen >= 150) $elem = "oxic";
        else $elem = "suboxic";
      } else $elem = "non_def";
      $oxy_def[$sites] = $elem;
  }
  return $oxy_def;
}

function get_site_names_by_oxy_def($site_oxy_def, $def){
  $site_oxy_list = [];  
  foreach ($site_oxy_def as $site => $oxy_def){
    if ($oxy_def == "$def") $site_oxy_list[] = $site;
  }
  return $site_oxy_list;      
}

function get_site_names_by_oxy_def_r($sites_oxy_def, $def){
  $site_oxy_list = [];  
  foreach ($sites_oxy_def as $site => $oxy_def){
    if ($oxy_def == "$def") $site_oxy_list[] = $site;
  }
  return $site_oxy_list;      
}


function draw_rect($x, $y, $width, $height, $color) {
  printf("<rect x='$x' y='$y' width='$width' height='$height' style='fill:$color' />");
  echo "\n";
}


function draw_variable($x, $y, $variables){
 $color = 0; 
  foreach ($variables as $variable){
   $color_ini = "hsl($color, 100%%, 50%%)";
   draw_rect($x, $y, $variable, 4, $color_ini);
   $y += 5;
   $color += 360/count($variables) ;
 } 
}

function draw_variables_by_site($x, $y, $site_variables){
  foreach ($site_variables as $variables){
    draw_variable($x, $y, $variables);
    $y += 10;
  }
}
