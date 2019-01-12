<?php

function make_ec_tax_count_arr($file, $ecs) {
  $taxa_counts = [];
  foreach($ecs as $ec){
    $f = fopen($file, "r");
    while ($line = trim(fgets($f))){
      $columns = explode("\t", $line);
      $ec_number = $columns[0];
      $taxa_name = $columns[1];
      $taxa_count= $columns[2];
      if ($taxa_count == 1) continue;
      if ($ec == $ec_number)
        $taxa_counts[$ec][$taxa_name] = $taxa_count;
    }
    fclose($f);
  }
  return $taxa_counts;
}

function add_average_sizes_per_ec ($gene_lengths_by_ec = "../../piecharts/data/gene_lengths_by_ec.txt"){
  $f = fopen($gene_lengths_by_ec, "r");
  $ec_ave_arr = [];
  while($line = trim(fgets($f))){
    $cols = explode("\t", $line);
    $ec = $cols[0];
    $average_per_ko = $cols[2];
    if(!isset($ec_ave_arr[$ec])) $ec_ave_arr[$ec] = $average_per_ko;
    else $ec_ave_arr[$ec] += $average_per_ko;
  }
  fclose($f);
  return $ec_ave_arr;
}

//****** normalización con tamaño del gen

function get_rel_ab_with_gene_size_and_total_counts($ec_tax_count_arr, $total, $ecs_size_arr, $limit = 0.00000000000001){
  $ec_tax_rel_ab_arr = [];
  foreach($ec_tax_count_arr as $ec => $taxa_counts_arr){
    $ec_size = $ecs_size_arr[$ec]*3;
    foreach($taxa_counts_arr as $taxa => $counts){
      $rel_ab = $counts/($total*$ec_size);
      if($rel_ab < $limit) continue;
      else $ec_tax_rel_ab_arr[$ec][$taxa] = $rel_ab; 
    }
  }
  return $ec_tax_rel_ab_arr;
}

function make_site_ec_rel_ab_with_ec_size($files, $ecs, $total_arr, $ecs_size_arr, $end_name, $limit = 0.00000000000001){
  $ec_tax_count_arr = [];
  $site_ec_tax_relab_arr = []; 
  foreach($files as $file){
    $site = basename($file, $end_name);
    $ec_tax_count_arr = make_ec_tax_count_arr($file, $ecs);
    if(!isset($total_arr[$site])) continue;
    else $site_ec_tax_relab_arr[$site] = get_rel_ab_with_gene_size_and_total_counts($ec_tax_count_arr, $total_arr[$site], $ecs_size_arr, $limit);
  }
  return $site_ec_tax_relab_arr;
}

function add_total_tax_ab_per_site_and_ec($site_ec_tax_relab_arr){
  $total_tax_ab_per_ec = [];
  foreach($site_ec_tax_relab_arr as $site => $ec_tax_rel_ab){
    foreach($ec_tax_rel_ab as $ec => $tax_rel_ab){
      foreach($tax_rel_ab as $tax => $rel_ab){
        if (!isset($total_tax_ab_per_ec[$ec][$tax])) $total_tax_ab_per_ec[$ec][$tax] = $rel_ab;
        else $total_tax_ab_per_ec[$ec][$tax] += $rel_ab;
      }
    }
  }
  foreach($total_tax_ab_per_ec as $ec_2 => $tax_relab){
    arsort($tax_relab);
    $total_tax_ab_per_ec[$ec_2] = $tax_relab;

  }
  return $total_tax_ab_per_ec;
}

function get_most_abundant_tax_names_per_ec ($total_tax_ab_per_ec, $ranking = 4){
  $most_abundant_tax_names_per_ec = [];
  
  foreach($total_tax_ab_per_ec as $ec => $tax_total){
    $i = 1;
    foreach($tax_total as $tax => $total){
      if ($i > $ranking) continue;
      else $most_abundant_tax_names_per_ec[$ec][$tax] = $total;
      $i += 1;
    }
  }
  return $most_abundant_tax_names_per_ec;
}

function make_most_abundant_site_ec_tax_rel_ab($site_ec_tax_relab_arr, $most_abundant_tax_names_per_ec){
  $most_abundant_site_ec_tax_rel_ab = [];
  foreach($site_ec_tax_relab_arr as $site => $ec_tax_relab){
    foreach($ec_tax_relab as $ec => $tax_relab){
      foreach($tax_relab as $tax => $relab){
        if (isset($most_abundant_tax_names_per_ec[$ec][$tax])) $most_abundant_site_ec_tax_rel_ab[$site][$ec][$tax] = $relab;
      }
    }
  }
  return $most_abundant_site_ec_tax_rel_ab;
}

///**** calculo del porcentaje de reads por cada ec como el 100%, ignorando singletons, para calcular saber el porcentaje de los más abundantes respecto al total count por ec.

function add_total_count_per_ec ($files, $end_name, $ecs){
  $site_ec_tax_count_arr = [];
  $total_count_ec = [];
  foreach($files as $file){
    $name = basename($file, $end_name);
    $site_ec_tax_count_arr[$name] = make_ec_tax_count_arr($file, $ecs);
  }
  foreach ($site_ec_tax_count_arr as $site => $ec_tax_count_arr){
    foreach($ec_tax_count_arr as $ec => $tax_count_arr){
      foreach($tax_count_arr as $tax => $counts){
        if ($counts == 1) continue;
        if (!isset($total_count_ec[$ec]))
          $total_count_ec[$ec] = $counts;
        else $total_count_ec[$ec] += $counts;
      }
    }
  }
  return $total_count_ec;
}

function add_total_coun_per_ec_tax($files, $end_name, $ecs){
  $site_ec_tax_count_arr = [];
  $total_count_ec_tax = [];
  foreach($files as $file){
    $name = basename($file, $end_name);
    $site_ec_tax_count_arr[$name] = make_ec_tax_count_arr($file, $ecs);
  }
  foreach ($site_ec_tax_count_arr as $site => $ec_tax_count_arr){
    foreach($ec_tax_count_arr as $ec => $tax_count_arr){
      foreach($tax_count_arr as $tax => $counts){
        if ($counts == 1) continue;
        if (!isset($total_count_ec_tax[$ec][$tax]))
          $total_count_ec_tax[$ec][$tax] = $counts;
        else $total_count_ec_tax[$ec][$tax] += $counts;
      }
    }
  }
  foreach($total_count_ec_tax as $ec => $tax_count_arr){
    arsort($tax_count_arr);
    $total_count_ec_tax_2[$ec] = $tax_count_arr;

  }
  return $total_count_ec_tax_2;
}

function percentaje_of_most_abundant_tax_per_ec($total_ec_counts, $total_ec_tax_counts, $ranking = 4){
  $percentaje_arr = [];
  foreach($total_ec_tax_counts as $ec => $tax_count_arr){
    $i = 1;
    $total = $total_ec_counts[$ec];
    foreach($tax_count_arr as $tax => $counts){
      if($i > $ranking) continue;
      $percentaje_arr[$ec][$tax] = $counts*100/$total;
      $i += 1; 
    }
  }
  return $percentaje_arr;
}

function total_percentaje_per_ec($percentaje_arr){
  $total_percentaje_per_ec = [];
  foreach($percentaje_arr as $ec => $tax_percentaje_arr){
    foreach($tax_percentaje_arr as $tax => $percentaje){
      if(!isset($total_percentaje_per_ec[$ec])) $total_percentaje_per_ec[$ec] = $percentaje;
      else $total_percentaje_per_ec[$ec] += $percentaje;
    }
  }
  return $total_percentaje_per_ec;
}













//***** sin agregar el tamaño del gen
function get_rel_abundance_custom_from_tax_count($taxa_count, $total, $limit = 0.0000000001){
  $custom_taxa_rel = [];
  foreach($taxa_count as $tax_name => $count){
    $rel_ab = $count/$total;
    if ($rel_ab < $limit) continue;
    else $custom_taxa_rel[$tax_name] = $rel_ab;
  }
  return $custom_taxa_rel;
}

//saca abundancias relativas aplicando función anterior al segundo nivel del array creado por la primera funcion
function get_relab_from_ec_tax_count_arr($ec_taxa_counts, $total, $limit = 0.0000000001) {
  $rel_abs_custom = [];
  foreach ( $ec_taxa_counts as $ecs => $counts ) {
    if ($counts == 1) continue;
    $rel_abs_custom[$ecs] = get_rel_abundance_custom_from_tax_count($counts, $total, $limit);
  }
  return $rel_abs_custom;
}

function make_site_ec_tax_relab_arr($files, $end_name, $ecs, $total_count_arr, $limit = 0.0000000001){
  $sites_counts = [];
  $sites_rel_ab_custom = [];
  foreach ($files as $file){
    $site = basename($file, $end_name);
    $taxa_counts = make_ec_tax_count_arr($file, $ecs);
    if ( isset($total_count_arr[$site])){
      $total = $total_count_arr[$site];
      $sites_rel_ab_custom[$site] = get_relab_from_ec_tax_count_arr($taxa_counts, $total, $limit);
    }  
  }
  return $sites_rel_ab_custom;
}

//******

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
function flip_big_group_row_col_names($array){
  $r = [];
  $temp_tax_name = "";
  foreach($array as $site => $ec_tax_name_arr){
    foreach ($ec_tax_name_arr as $ec => $tax_value_arr){
      $temp_tax_name = $tax_name;
      foreach ($tax_value_arr as $tax_name => $value){
        
        //if($ec == "2.3.1.169" && $tax_name == "GSO") continue;
        $r[$ec][$site][$tax_name] = $value;

      }
    }
  }
  return $r;
}

function reorder_arr_by_ecs ($arr_ecs, $ecs){
  $ordered_arr = [];
  //$arr_ecs = get_ordered_col_by_total($arr_ecs);
  foreach($ecs as $ec){
    if(isset($arr_ecs[$ec])) $ordered_arr[$ec] = $arr_ecs[$ec];
  }
  return $ordered_arr;
}

function reorder_arr_by_ecs_2 ($arr_ecs, $ecs){
  $ordered_arr = [];
  foreach($ecs as $ec){
    if(isset($arr_ecs[$ec])) $ordered_arr[$ec] = $arr_ecs[$ec];
  }
  return $ordered_arr;
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
        if ($ec == $ec_color) $site_color[$ec] = $color;
      }
    }
  }
  return $site_color;
}



    
  

