<?php

function load_oxy_sites($filename = '../data/Tax_grouped/Mg_ambientales.txt') {
  $oxy_sites = [];
  $file = fopen($filename, 'r');
  while($line = trim(fgets($file))){
    $cols = explode("\t", $line);
    $site_name = $cols[0];
    $oxygen = $cols[1];
    //$nitrite = $cols[5]; 
    if (!isset($oxygen)) continue;
    else $oxy_sites[$site_name] = $oxygen;
  }
  return $oxy_sites;
  fclose($file);
}

//función para encontrar oxy_sup, core y oxy_inf según concentración de oxígeno y site.
function define_oxygen_layer($oxy_sites, $sites_counts){
  $oxy_def = [];
  foreach ($sites_counts as $sites => $counts){
      $elem = [ 'value' => null, 'color' => 'black' ];
      if (isset($oxy_sites[$sites])){
        $o = $oxy_sites[$sites];
        if ($o <= 5) $elem['color'] = "red"; //anoxico
        else if ($o >= 150) $elem['color'] = "green"; //oxico
        else $elem['color'] = "blue"; //oxiclina
        
        $elem['value'] = $o; 
      }
      $oxy_def[$sites] = $elem;
  }
  return $oxy_def;
}

function get_site_names_by_oxy_def($oxy_colors, $color){
  $site_oxy_list = [];  
  foreach ($oxy_colors as $site => $oxy_def){
    if ($oxy_def['color'] == "$color") $site_oxy_list[] = $site;
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



