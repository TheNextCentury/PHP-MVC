<?php

namespace framework\ui;

class Select extends Control {
	
	private $selectedValue;
	private $options;
	
	public function __construct(string $name, array $options = array()) {
		parent::__construct($name);
		
		$this->options = $options;
	}
	
	public function render() {
		echo "<select " . $this->buildArguments() . " >" . $this->buildOptions() . "</select>";
	}
	
	private function buildOptions() : string {
		$htmlOptions = "";
		foreach ($this->options as $value => $label) {
			$htmlOptions .= "<option value=\"$value\" ";
			if($value == $selectedValue) {
				$htmlOptions .= "selected ";
			}
			$htmlOptions .= ">$label</option>";
		}
		return $htmlOptions;
	}
}
?>