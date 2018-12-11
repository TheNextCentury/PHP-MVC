<?php

namespace framework\ui\bootstrap4;

use framework\ui\Input as DefaultInput;

class FileInput extends DefaultInput {

	public function __construct(string $name) {
		parent::__construct("file", $name);
		$this->addClass("form-control-file");
		
	}
}

