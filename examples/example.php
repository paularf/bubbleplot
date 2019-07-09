<?php

include_once('../vendor/autoload.php');

$data = new \paularf\bubbleplot\Data;
$data->data = [
    'r1' => ['c1' => 5, 'c2' => 10 ],
    'r2' => ['c1' => 20, 'c2' => 25 ],
    'r3' => ['c1' => 2, 'c2' => 10 ]
];

$chart = new \paularf\bubbleplot\Chart;
$chart->data = $data;
$chart->bubble_scale = 1;

echo '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="10000" width="15000">';
$chart->draw(300,300);
echo '</svg>';
