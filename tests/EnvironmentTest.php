<?php

namespace paularf\bubbleplot;

class EnvironmentTest extends \PHPUnit\Framework\TestCase {

	public function testGetTypeFromNutrients() {
		$result = Environment::get_type_from_nutrients(["nitrite" => 0, "oxygen" => 10]); //:: scope operator, llama a esta función dentro de la clase environment que está static
		$this->assertEquals("suboxic", $result);
	}

	public function testGetTypeFromNutrients2() {
		$result = Environment::get_type_from_nutrients(["nitrite" => 20, "oxygen" => 0]);
		$this->assertEquals("anoxic", $result);	
	}
}