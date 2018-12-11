<?php

namespace framework\ui;

use framework\ui\renderer\IRenderer;

class CustomRadioGroup implements IRenderer {
	
	/**
	 * 
	 * @var array[string, CustomRadio]
	 */
	private $radios = array();
	
	public function __construct(string $name, array $groupValues = array()) {
		parent::__construct($name);
		
		foreach ($groupValues as $value => $label) {
			$this->radios[$value] = (new CustomRadio($name . "_" . $value, $value, $label))
				->setName($name);
		}
	}
	
	public function setSelectedValue(string $value) : CustomRadioGroup {
		$this->radios[$value]->setChecked(true);
	}
	
	public function setDisabled(bool $disable): Control {
		foreach ($this->radios as $value => $radio) {
			$radio->setDisable($disable);
		}
	}
	
	public function setVisible(bool $disable): Control {
		foreach ($this->radios as $value => $radio) {
			$radio->setVisible($disable);
		}
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see IRenderer::render()
	 */
	public function render(HtmlElement $element) {
		foreach ($this->radios as $radio) {
			$radio->render();
?>
			<div class="<?php echo implode(" ", $this->classes); ?>" >
				<input id="<?php echo $this->name . "_" . $value; ?>" 
<?php 
					if($this->value != null && $this->value == $value) {
						echo "checked "; 
					}
?>
					type="<?php echo $this->type; ?>" 
					name="<?php echo $this->name; ?>" 
					value="<?php echo $value ?>" 
<?php 
					if($this->disable) { 
						echo "disabled"; 
					} 
?> 
					class="custom-control-input" 
				/>
           		<label class="custom-control-label" for="<?php echo $this->name . "_" . $value; ?>">
					<?php echo $label; ?>
				</label>
			</div>
<?php 
		}
	}
}
?>