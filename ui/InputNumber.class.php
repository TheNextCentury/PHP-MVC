<?php

namespace framework\ui;

class InputNumber extends Input {
	public function __construct(string $name) {
		parent::__construct("number", $name);
	}
	
	/**
	 * 
	 * @param string $step
	 * @return InputNumber
	 */
	public function setStep(string $step) {
		$this->putArgument("step", $step);
		return $this;
	}
}

