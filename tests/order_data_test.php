<?php
require_once("../src/01.taxa_funcs.php");

$ecs = ["ec2", "ec1", "ec3"];

$data_test = [
	"ec1" => 1,
	"ec3" => 4,
	"ec2" => 3
];

$test = reorder_arr_by_ecs ($data_test, $ecs);

print_r($test);
