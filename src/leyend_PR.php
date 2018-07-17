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

function draw_line($x1, $y1, $x2, $y2, $stroke, $width = 4) {
  printf('<line x1="%f" y1="%f" x2="%f" y2="%f" style="stroke:%s;stroke-width:%f" />', $x1, $y1, $x2, $y2, $stroke, $width);
  echo "\n";
}


function draw_bubble($x, $y, $value, $color = 'red') {
  printf('<ellipse cx="%f" cy="%f" rx="%f" ry="%f" fill-opacity="0.2" style="fill:%s;stroke:%s;stroke-width:1" />', $x, $y, $value, $value, $color, $color);
}


function draw_scale_leyend($x, $y, $scale){
  $current_x = $x;
  foreach ($scale as $value){
    draw_bubble($current_x, $y +30, 150000*$value, '#4e4f51');
    $current_x += 50;
  }
  draw_line($x, $y+30, $current_x, $y+30, 'black', 0.2);

  draw_bubble($current_x + 20, $y - 20, 6, 'green'); 
  draw_text($current_x + 30, $y - 15, 'Oxic', 0, 12);

  draw_bubble($current_x + 20, $y, 6, 'blue'); 
  draw_text($current_x + 30, $y + 5, 'Up oxycline', 0, 12);

  draw_bubble($current_x + 20, $y + 20, 6, 'purple'); 
  draw_text($current_x + 30, $y + 25, 'Low oxygen interfase', 0, 12);

  draw_bubble($current_x + 20, $y + 40, 6, 'red'); 
  draw_text($current_x + 30, $y + 45, 'Anoxic', 0, 12);

  draw_bubble($current_x + 20, $y + 60, 6, '#3602f4'); 
  draw_text($current_x + 30, $y + 65, 'Down oxycline', 0, 12);
}

function get_max($data){
  $max = 0.0000001;
  foreach ($data as $sites){
    foreach($sites as $taxas){
      foreach($taxas as $counts){
        $max = max($counts, $max);      
      }
    }
  }
  return $max; 
}

function get_min($data){
$min = 100000000;
  foreach ($data as $sites){
    foreach($sites as $taxas){
      foreach($taxas as $counts){
        $min = min($counts, $min);      
      }
    }
  }
  return $min; 
}


function formatScientific($someFloat){
  return sprintf("%.2e", $someFloat);
}
function draw_leyend ($x, $y, $leyend_scale, $scientific_notation){
  draw_scale_leyend($x, $y, $leyend_scale);
  draw_scient_values ($x - 15, $y + 30, $scientific_notation);
}

function draw_scient_values ($x, $y, $values){
  draw_text($x, $y + 28, 'Relative abundance per metagenome', 0, 8, 'bold');
  foreach ($values as $value){
    draw_text($x, $y + 18, $value, 0, 8);
    $x += 50;
  }
}
