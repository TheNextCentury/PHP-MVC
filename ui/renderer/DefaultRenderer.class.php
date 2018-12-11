<?php

namespace framework\ui\renderer;

use framework\ui\HtmlElement;

class DefaultRenderer implements IRenderer {
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see IRenderer::render()
	 */
	public function render(HtmlElement $element) {
		$element->render();
	}
}

