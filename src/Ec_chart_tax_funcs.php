<?php

function make_ec_count_arr($file, $ecs, $limit = 2) {
  $ec_counts = [];
  foreach($ecs as $ec){
    $f = fopen($file, "r");
    while ($line = trim(fgets($f))){
      $columns = explode("\t", $line);
      $ec_number = $columns[0];
      $taxa_count = $columns[1];
      if ($taxa_count <= $limit) continue;
      if ($ec == $ec_number)
        $ec_counts[$ec] = $taxa_count;
    }
    fclose($f);
  }
  return $ec_counts;
}
function make_site_ec_coun_arr($files, $end_site_name, $ecs, $limit = 2){
  $site_arr = [];
  foreach($files as $file){
    $basename = basename($file, $end_site_name);
    $site_arr[$basename] = make_ec_count_arr($file, $ecs, $limit);
  }
  return $site_arr;
}
//saca abundancias relativas del array que contiene tax_name y $counts(para ser usado en array de un nivel)
function get_relab_from_ec_count_arr($ec_count, $total){
  $ec_relab = [];
  foreach($ec_count as $ec => $count){
    $rel_ab = $count/$total;
    $ec_relab[$ec] = $rel_ab;
  }
  return $ec_relab;
}

function get_relab_from_site_count_arr($site_ec_counts, $total_arr) {
  $site_ec_relab = [];
  foreach ( $site_ec_counts as $site => $ec_count) {
    $site_ec_relab[$site] = get_relab_from_ec_count_arr($ec_count, $total_arr[$site]);
  }
  return $site_ec_relab;
}
