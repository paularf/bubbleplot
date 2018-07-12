<?php

namespace paularf\bubbleplot;

function read_environmental_data($filename = '../data/Tax_grouped/Mg_ambientales.txt') {
  $data = [];

  $file = fopen($filename, 'r');
  
  while($line = trim(fgets($file))){
    $cols = explode("\t", $line);

    $site_name = $cols[0];
    $oxygen = $cols[1] ?? null; //if isset el primero, else, tirate el segundo
    $nitrite = $cols[5] ?? null;
    
    $data[$site_name] = [
      'site_name' => $site_name,
      'nutrients' => [
      	'nitrite' => $nitrite,
        'oxygen' => $oxygen
      ]  
    ];
  }
  fclose($file);

  return $data;
}

function get_type_from_nutrients($nutrients) {

    if( $nutrients['nitrite'] >= 0.45 && $nutrients['oxygen'] <= 2) return "anoxic";
    else if($nutrients['oxygen'] >= 150) return "oxic";
    else return "suboxic";
      
}

function define_oxygen_layer($oxy_sites, $sites_counts){
  $oxy_def = [];
  foreach ($sites_counts as $sites => $ec_tax_arr){
      $elem = get_type_from_nutrients($oxy_sites[$sites]['nutrients']);
      $oxy_def[$sites] = $elem;
  }
  return $oxy_def;
}