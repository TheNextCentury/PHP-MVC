<?php

namespace framework\ui;

class InputRange extends Input {

	public function __construct(string $name) {
		parent::__construct(self::TYPE_RANGE, $name, "custom-range");
	}

	/**
	 *
	 * @param string $step
	 * @return InputRange
	 */
	public function setMin(int $min) {
		$this->putArgument("min", $min);
		return $this;
	}

	/**
	 *
	 * @param string $step
	 * @return InputRange
	 */
	public function setMax(int $max) {
		$this->arguments["max"] = $max;
		return $this;
	}
}

