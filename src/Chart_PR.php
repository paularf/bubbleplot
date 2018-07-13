<?php

function draw_text($x, $y, $text, $rotation_angle = 0, $size = 8, $weigth = 'normal'){
      echo <<<EOF
      <text
           font-family="Verdana"
           transform="translate($x, $y)rotate(-$rotation_angle)"
           font-size="$size"
           font-weight="$weigth"
           fill="black">
           $text
      </text>
EOF;
}

function draw_row_names($x, $y, $row_names){
  foreach ($row_names as $row_name){ 
    $y += 20;
    //$row_name = clean_site_name($row_name);
    draw_text($x, $y, $row_name, 0, 10);
  }
}

function draw_column_text($x, $y, $texts){
  $current_x = $x;
  foreach ($texts as $text){
    draw_text($current_x, $y, $text, 90, 8);
    $current_x += 10;
  }
}

function get_value($data, $level_3, $level_2, $level_1){
  if (isset($data[$level_3][$level_2][$level_1])) {
    $value = $data[$level_3][$level_2][$level_1];
    return $value;
  }
  else return 0;
} 

function draw_line($x1, $y1, $x2, $y2, $stroke, $width = 4) {
  printf('<line x1="%f" y1="%f" x2="%f" y2="%f" style="stroke:%s;stroke-width:%f" />', $x1, $y1, $x2, $y2, $stroke, $width);
  echo "\n";
}

function draw_bubble($x, $y, $value, $color = 'red') {
  printf('<ellipse cx="%f" cy="%f" rx="%f" ry="%f" fill-opacity="0.2" style="fill:%s;stroke:%s;stroke-width:1" />', $x, $y, $value, $value, $color, $color);
}

function draw_color_bubble_from_three_level_arr($x, $y, $data, $big_group, $row_name, $col_name, $color, $bubble_scale) {
  $value = get_value($data, $big_group, $row_name, $col_name);
  if ($value > 0){ 
    draw_bubble($x, $y, $value * $bubble_scale, $color);
    return true;
  }  
  else return false;  
} 

function draw_tax_bubbles_per_unique_ec_and_metaome($x, $y, $delta_x, $data, $site, $ec, $tax_arr, $color, $bubble_scale){
	$current_x = $x;
	$tax_count = 0;
	foreach($tax_arr as $tax => $value){
		draw_text($current_x, $y - 20, $tax, 60, 8, $weigth = 'normal');
		draw_color_bubble_from_three_level_arr($current_x, $y, $data, $site, $ec, $tax, $color, $bubble_scale);
	$current_x += $delta_x;
	$tax_count += 1;
	}
	return $tax_count;
}

function draw_metaome ($x, $y, $delta_x, $delta_y, $data, $site, $ec_arr, $color, $bubble_scale){
	draw_text($x - 200, $y, $site, 0, 8, $weigth = 'normal');
	$current_x = $x;
	foreach($ec_arr as $ec => $tax_arr){
		draw_text($current_x, $y - 40, $ec, 60, 8, $weigth = 'normal');
		$tax_count = draw_tax_bubbles_per_unique_ec_and_metaome($current_x, $y, $delta_x, $data, $site, $ec, $tax_arr, $color, $bubble_scale);
		$current_x += $delta_x * ($tax_count + 1);
	}
}
function draw_metaomes($x, $y, $delta_x, $delta_y, $data, $color, $bubble_scale){
	$current_y = $y;
	foreach ($data as $site => $ec_arr){
		draw_metaome ($x, $current_y, $delta_x, $delta_y, $data, $site, $ec_arr, $color, $bubble_scale);
		$current_y += $delta_y;
	}
}





function draw_tax_column_by_metaomes_as_row($x, $y, $delta_y, $data, $sites, $ec, $tax, $color, $bubble_scale){ 
	$current_y = $y; //no cambia $y!!
	draw_text($x, $y - 20, $tax, 60, 8, $weigth = 'normal');
	foreach($sites as $site){
		draw_color_bubble_from_three_level_arr($x, $current_y, $data, $site, $ec, $tax, $color, $bubble_scale);
		$current_y += $delta_y;
	}
	draw_line($x, $y, $x, $current_y, 'black', 0.4);
}


function draw_bubbles_per_ec($x, $y, $delta_x, $delta_y, $data, $sites, $ec, $tax_arr, $color, $bubble_scale){
	$current_x = $x;
	foreach($tax_arr as $tax => $value){ 
		draw_tax_column_by_metaomes_as_row($current_x, $y, $delta_y, $data, $sites, $ec, $tax, $color, $bubble_scale);
		$current_x += $delta_x;
	}
	draw_line($x, $y, $current_x, $y, 'black', 0.4);
}


/*
function clean_site_name($site) {
  foreach ( site_name_filters as $filter ) {
    $site = str_replace($filter, '', $site);
  }
  $site = str_replace('_', ' ', $site);
  return $site;
}


function draw_bubble_per_metaoma_zig_zag_names ($x, $y, $big_group, $is_up){
  $current_x = $x;
  $contador = 0;
  foreach (column_names as $col_name){ 
    $column_total = get_total_by_column($big_group, $col_name);
    if ( $column_total == 0 ) continue;
    else {
      draw_bubble_column_by_big_group_col_name($current_x, $y, $big_group, $col_name);
      $current_x += delta_x;
      $contador += 1;
    }
  }
  if ( $contador > 0 ) {
    if ($is_up == true)
      draw_text($x - 8, $y - 20 , $big_group, 60, '8', 'bold');
    if ($is_up == false)
      draw_text($x - 8, $y - 20, $big_group, 60, '8', 'bold');
  }
  return $contador;
}

function draw_bubble_metaomas_zig_zag_names($x, $y){
  $current_y = $y;
  $current_x = $x;
  $is_up = true;
  foreach (data as $big_group => $row_col_array){
    $contador = draw_bubble_per_metaoma_zig_zag_names ($current_x, $current_y, $big_group, $is_up);

    if ( $contador > 0 ) {
      $current_x += delta_x * ($contador + 1);
      $is_up = !$is_up;
    }
  }
  foreach (row_names as $row_name){
    draw_line($x, $current_y, $current_x - 2*delta_x, $current_y, 'black', 0.1);
    $current_y += delta_y;
  }  
}

function get_column_names() {
  $result = [];
  foreach ( data as $rows ) {
    foreach ( $rows as $columns ) {
      foreach ( $columns as $col_name => $value ) {
        if ( in_array($col_name, $result) ) continue;
        $result[] = $col_name;
      }
    }
  }
  return $result;
}

function get_row_names() {
  $result = [];
  foreach ( data as $rows ) {
    foreach ( $rows as $row_name => $rows ) {
        if ( in_array($row_name, $result) ) continue;
        $result[] = $row_name;
    }
  }
  return $result;
}

function draw($x, $y) {
  if ( empty(row_names) ) {
    row_names = get_row_names();
  }
  if ( empty(column_names) ) {
    column_names = get_column_names();
  }
  draw_row_names($x - 140, $y - 8, row_names);
  
  //draw_bubble_per_metaoma_zig_zag_names ($x + 300, $y, "2.3.1.169", $is_up);
  //draw_bubble_column_by_big_group_col_name($x + 300, $y , "2.3.1.169", "Candidatus Scalindua brodae");
    
  draw_bubble_metaomas_zig_zag_names($x + 100, $y);
}

}*/