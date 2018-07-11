<?php

function make_ec_tax_count_arr($file, $ecs, $limit = 2) {
  $f = fopen($file, "r");
  $taxa_counts = [];
  while ($line = trim(fgets($f))){
    $columns = explode("\t", $line);
    $ec_number = $columns[0];
    $taxa_name = $columns[1];
    $taxa_count= $columns[2];
    if ($taxa_count <= $limit) continue;
    if (in_array($ec_number, $ecs))
      $taxa_counts[$ec_number][$taxa_name] = $taxa_count;
  }
  fclose($f);
  return $taxa_counts;
}

//saca abundancias relativas del array que contiene tax_name y $counts(para ser usado en array de un nivel)
function get_rel_abundance_custom_from_tax_count($taxa_count, $total){
  $custom_taxa_rel = [];
  foreach($taxa_count as $tax_name => $count){
    $rel_ab = $count/$total;
    $custom_taxa_rel[$tax_name] = $rel_ab;
  }
  return $custom_taxa_rel;
}

//saca abundancias relativas aplicando función anterior al segundo nivel del array creado por la primera funcion
function get_relab_from_ec_tax_count_arr($ec_taxa_counts, $total) {
  $rel_abs_custom = [];
  foreach ( $ec_taxa_counts as $ecs => $counts ) {
    $rel_abs_custom[$ecs] = get_rel_abundance_custom_from_tax_count($counts, $total);
  }
  return $rel_abs_custom;
}

//saca rankings desde un array o subarray que tiene los taxa names y los taxa_counts 
function get_rankings_from_tax_count($taxa_count, $max = 1000) {
 $ranking = [];
 arsort($taxa_count);
 $i = 1;
 foreach ( $taxa_count as $taxa_name => $count ) {
    $ranking[$taxa_name] = $i;
    
    if ($i >= $max )
      break;
    $i++;
      
  }
  return $ranking;
}

//utiliza la función de arriba para sacar los rankins de los taxa_counts
function get_rankings_from_ec_tax_name_tax_count_array($taxa_counts, $max = 1000) {
  $rankings = [];
  foreach ( $taxa_counts as $ecs => $counts ) {
    $rankings[$ecs] = get_rankings_from_tax_count($counts, $max);
  }
  return $rankings;
}

//abundancias relativas sumando los totales del array (en este caso es tomando en cuenta los genes de carbon_fix pero no es válido para comparar entre metaomasç)
function get_rel_abundance_from_tax_count($taxa_count){
  $total = array_sum($taxa_count);
  $taxa_rel = [];
  foreach($taxa_count as $tax_name => $count){
    $rel_ab = $count/$total;
    $taxa_rel[$tax_name] = $rel_ab;
  }
  return $taxa_rel;
}

//usa la función de arriba para sacar rel_ab de taxa_counts!
function get_rel_abs_from_ec_tax_name_tax_count_array($ec_taxa_counts) {
  $rel_abs = [];
  foreach ( $ec_taxa_counts as $ecs => $counts ) {
    $rel_abs[$ecs] = get_rel_abundance_from_tax_count($counts);
  }
  return $rel_abs;
}

function get_taxa_names($sites) {
  $result = [];
  foreach ( $sites as $ecs ) {
    foreach ( $ecs as $taxas ) {
      foreach ( $taxas as $taxa_name => $count ) {
        if ( in_array($taxa_name, $result) ) continue;
        $result[] = $taxa_name;
      }
    }
  }
  return $result;
}

function get_color_from_ec_colors_and_site_ec_array ($ec_colors, $sites_counts){
$site_color = [];
  foreach($sites_counts as $site => $ec_tax_name_array){
    foreach ($ec_tax_name_array as $ec => $tax_name_count_array){
      foreach ($ec_colors as $ec_color => $color){
        if ($ec == $ec_color) $site_color[$site][$ec] = $color;
      }
    }
  }
  return $site_color;
}



    
  
