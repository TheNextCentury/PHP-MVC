<?php

namespace framework\exception;

class SqlException extends \Exception {
	public function __construct($sqlRequest, $previous) {
		parent::__construct("Problème lors de l'execution de la rêquete: " . $sqlRequest, null, $previous);
	}
}

