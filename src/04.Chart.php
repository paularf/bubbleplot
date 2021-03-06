<?php
//una clase puede tener constantes y variables (propiedades), así como sus propias funciones (métodos) 
class Chart {

public $delta_x;
public $delta_y;
public $data;
public $row_names;
public $column_names; // estos son los ecs
public $big_group;
public $bubble_scale;
public $get_color;
public $filter;
public $site_name_filters = [];
 //estos no son necesarios en php pero es mejor para mantener el orden

function __construct() {
  $this->get_color = function($big_group, $row_name, $col_name) { return 'red'; }; //funcion anonima modificable
  $this->filter = function($big_group, $row_name, $col_name, $value) {
    return true;
  };
}

function get_total_by_column($big_group, $col_name){
  $total = 0;
  foreach ($this->row_names as $row_name){
    $total += $this->get_value($big_group, $row_name, $col_name);
  }
  return $total;
}

function get_column_names() {
  $result = [];
  //$result[] = "Cyanobacterias";
  foreach ( $this->data as $big_group => $rows) {
    foreach ( $rows as $columns ) {
      foreach ( $columns as $col_name => $value) {
        $total = $this->get_total_by_column($big_group, $col_name);
        $arr[$big_group][$col_name] = $total;  
      } 
    }
      arsort($arr[$big_group]);
      foreach($arr as $big_group_2){
        foreach($big_group_2 as $col_name_2 => $total){
          if ( in_array($col_name_2, $result) ) continue;
          else if($col_name_2 == "Other") continue;
          else if($col_name_2 == "Chloroflexi bacteria") continue;  
        $result[] = $col_name_2;
        }    
      }
      $arr = []; 
  }
  $result[] = "Chloroflexi bacteria";
  $result[] = "Other";
  return $result;
}

function get_column_names_2() {
  $result = [];
  foreach ( $this->data as $rows ) {
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
  foreach ( $this->data as $rows ) {
    foreach ( $rows as $row_name => $rows ) {
        if ( in_array($row_name, $result) ) continue;
        $result[] = $row_name;
    }
  }
  return $result;
}

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

function draw_row_names($x, $y){
  foreach ($this->row_names as $row_name){
    $y += $this->delta_y;
    $row_name = $this->clean_site_name($row_name);
    $this->draw_text($x + 50, $y - 1.5, $row_name, 0, 10);

  }
}

function clean_site_name($site) {
  foreach ( $this->site_name_filters as $filter ) {
    $site = str_replace($filter, '', $site);
  }
  $site = str_replace('_', ' ', $site);
  return $site;
}
function draw_col_text($x, $y, $texts){ //no se usa
  $current_x = $x;
  foreach ($texts as $text){
    $this->draw_text($current_x, $y, $text, 90, 8);
    $current_x += $this->delta_x;
  }
}

function get_value($big_group, $row_name, $col_name){
  if (isset($this->data[$big_group][$row_name][$col_name])) {
    $value = $this->data[$big_group][$row_name][$col_name];
    $func = $this->filter;
    if ( $func($big_group, $row_name, $col_name, $value) )
      return $value;
    else
      return 0;
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


function draw_color_bubble($x, $y, $big_group, $row_name, $col_name) {
  $value = $this->get_value($big_group, $row_name, $col_name);
  $func = $this->get_color;
  $color = $func($big_group, $row_name, $col_name); //por mi versión de php
  if ($value > 0){ 
    $this->draw_bubble($x, $y, $value * $this->bubble_scale, $color);
    return true;
  }  
  else return false;  
} 

function draw_bubble_column_by_big_group_col_name($x, $y, $big_group, $col_name) { 
  $current_y = $y;
  $this->draw_text($x, $current_y - 10, $col_name, '60', '8');
  foreach ( $this->row_names as $row_name){
    $bubble = $this->draw_color_bubble($x, $current_y, $big_group, $row_name, $col_name);
    $current_y += $this->delta_y;
  }
  $this->draw_line($x, $y, $x, $current_y - $this->delta_y, 'black', 0.1);
}


function draw_bubble_per_big_group($x, $y, $big_group){
  $current_x = $x;
  $contador = 0;
  foreach ($this->column_names as $col_name){ 
    $column_total = $this->get_total_by_column($big_group, $col_name);
    if ( $column_total == 0 ) continue;
    else {
      $this->draw_bubble_column_by_big_group_col_name($current_x, $y, $big_group, $col_name);
      $current_x += $this->delta_x;
      $contador += 1;
    }
  }
  $this->draw_text($x - 8, $y - 20 , $big_group, 60, '8', 'bold');
  return $contador;
}

function draw_bubble_metaomas($x, $y){
  $current_y = $y;
  $current_x = $x;
  $is_up = true;
  foreach ($this->data as $big_group => $row_col_array){
    $contador = $this->draw_bubble_per_big_group($current_x, $current_y, $big_group);
    $current_x += $this->delta_x * ($contador + 1);
  }
  foreach ($this->row_names as $row_name){
    $this->draw_line($x, $current_y, $current_x - 2*$this->delta_x, $current_y, 'black', 0.1);
    $current_y += $this->delta_y;
  }  
}

function draw($x, $y) {
  if ( empty($this->row_names) ) {
    $this->row_names = $this->get_row_names();
  }
  if ( empty($this->column_names) ) {
    $this->column_names = $this->get_column_names();
  }
  $this->draw_row_names($x - 120, $y - 8, $this->row_names);
  
  //$this->draw_bubble_per_metaoma($x + 300, $y + 800, "2.3.1.169");
  //$this->draw_bubble_column_by_big_group_col_name($x + 300, $y , "2.3.1.169", "Candidatus Scalindua brodae");
    
  $this->draw_bubble_metaomas($x + 100, $y);
}




}

