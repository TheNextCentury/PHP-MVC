<?php

namespace framework\ui\renderer;

use framework\ui\HtmlElement;

interface IRenderer {
	
	/**
	 * 
	 * @param HtmlElement $element
	 */
	public abstract function render(HtmlElement $element);
}

