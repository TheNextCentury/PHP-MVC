<?php

namespace framework\ui;

class CustomRadio extends Container {

	/**
	 *
	 * @var Label
	 */
	private $label;

	/**
	 *
	 * @var Radio
	 */
	private $radio;

	public function __construct(string $name, $value = null, $labelText = null) {
		parent::__construct("div");
		
		$this->radio = new Radio($name);
		if ($value != null) {
			$this->radio->setValue($value);
		}
		
		$this->label = (new Label())->addClass("custom-control-label");
		if ($labelText != null) {
			$this->label->setText($labelText);
		}
		
		$this->setName($name)
			->setId($name)
			->addChild($this->radio)
			->addChild($this->label)
			->addClass("custom-control custom-radio");
	}
	
	public function setDisabled(bool $disabled) : CustomRadio {
		$this->radio->setDisabled($disabled);
		return $this;
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see HtmlElement::setName()
	 */
	public function setName(string $name): HtmlElement {
		$this->radio->setName($name);
		return $this;
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see HtmlElement::setId()
	 */
	public function setId(string $id): HtmlElement {
		$this->radio->setId($id);
		$this->label->setFor($id);
		return $this;
	}

	/**
	 *
	 * @param bool $checked
	 * @return CustomRadio
	 */
	public function setChecked(bool $checked): CustomRadio {
		$this->radio->setChecked($checked);
		return $this;
	}
}

