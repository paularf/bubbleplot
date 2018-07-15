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

function depth_by_site($site){
    $word = explode("_", $site);
    $count = count($word);
    $pos_depth = $count - 3;
    $depth = $word[$pos_depth];
    if ($depth = "oxycline") return $depth = 200;
    else {
      $depth = str_replace('m', '', $depth);
      return $depth;}
}

function define_oxygen_layer($oxy_sites, $sites_arr){
  $oxy_def = [];
  $elem = "";
  foreach ($sites_arr as $sites => $ec_tax_arr){
      if (isset($oxy_sites[$sites])){ 
        $nitrite = $oxy_sites[$sites][0];
        $oxygen = $oxy_sites[$sites][1];
        if($nitrite >= 0.45 && $oxygen <= 2.3) $elem = "anoxic";
        else if(!isset($nitrite) && $oxygen <= 2) $elem = "anoxic";
        else if ($oxygen < 5) $elem = "low_oxygen";
        else if($oxygen >= 90) $elem = "oxic";
        else $elem = "suboxic";
      } else $elem = "non_def";
      $oxy_def[$sites] = $elem;
  }
  return $oxy_def;
}

function order_sites_by_oxygen_gradient($oxy_sites, $original_data){
  $ordered_list = [];
  $array_order = [];
  foreach($original_data as $site => $data_arr){
    //print_r($oxy_sites);
    if(isset($oxy_sites[$site])){
      $oxygen = $oxy_sites[$site][1];
      $array_order[$site] = $oxygen;
  }
}
  arsort($array_order);
  foreach ($array_order as $site => $oxygen){
    $ordered_list[] = $site;
  } 
  return $ordered_list;
}


function get_site_names_by_depth($site_oxy_list){
  $site_depth_list = [];
  $test = [];
  foreach ($site_oxy_list as $site){
    $word = explode("_", $site);
    $count = count($word);
    $pos_depth = $count - 3;
    $depth = $word[$pos_depth];
    $depth = str_replace('m', '', $depth);
    $site_depth_list[$site] = $depth; 
    }
    asort($site_depth_list);
  foreach($site_depth_list as $site => $depth){
    $list[] = $site;
  }
  return $list;
}

function get_site_names_by_oxy_def($site_oxy_def, $def){
  $site_oxy_list = [];
  foreach ($site_oxy_def as $site => $oxy_def){
    if ($oxy_def == "$def") $site_oxy_list[] = $site;
  }
  return $site_oxy_list;      
}



function color_by_oxy_def($site_oxy_def){
  $site_color = [];
  $color = '';
  foreach($site_oxy_def as $site => $def){
    if ($def == 'oxic') $color = 'green';
    else if ($def == 'suboxic') $color = 'blue';
    else if ($def == 'low_oxygen') $color = 'purple';
    else if ($def == 'anoxic') $color = 'red';
    else if ($def == 'non_def') $color = 'black';
    $site_color[$site] = $color;
  } return $site_color;
}

