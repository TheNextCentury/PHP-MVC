<?php
namespace framework\utils;

/**
 * Class utilitaire pour la création des interfaces utilisateur
 * 
 * @author Vince
 *
 */

// TODO : Refactor maybe as factory
class UiUtils {
	
    /**
	 * Raccourcis vers 
	 * createInputInList($index, $fieldName, $inputType, $label, $labelClass, $containerClass, $required, $inputArgs, $disabled, visible, $fieldValue)
     *
     * @param int $index               Index de l'élément dans la liste
     * @param string $fieldName        Nom de l'élément
     * @param string $fieldValue       Valeur à insérer dans l'input
     * @param string $inputType        Type de l'input HTML à créer
	 * @param string $label            Label de l'élément
	 * @param string $labelClass       Classe CSS du label
	 * @param string $containerClass   Classe CSS de la div conteneur
	 * @param bool $required           Indique si l'élément est requis
	 * @param array $inputArgs         Tableau associatif contenant les arguments de l'élément HTML
	 * @param bool $disabled           Indique si l'élément doit être désactivé
	 * @param bool $visible            Indique si l'élément doit être visible
     */
    public static function createInputWithValueInList(int $index, string $fieldName, string $fieldValue=null, string $inputType, string $label, string $labelClass, string $containerClass, bool $required=false, array $inputArgs=array(), bool $disabled=false, bool $visible=true) {
        UiUtils::createInputInList($index, $fieldName, $inputType, $label, $labelClass, $containerClass, $required, $inputArgs, $disabled, $visible, $fieldValue);
    }
    
    /**
     * Permet de créer un <input /> qui serait contenu dans une liste
     * 
     * @param int $index               Index de l'élément dans la liste
     * @param string $fieldName        Nom de l'élément
     * @param string $inputType        Type de l'input HTML à créer
	 * @param string $label            Label de l'élément
	 * @param string $labelClass       Classe CSS du label
	 * @param string $containerClass   Classe CSS de la div conteneur
	 * @param bool $required           Indique si l'élément est requis
	 * @param array $inputArgs         Tableau associatif contenant les arguments de l'élément HTML
	 * @param bool $disabled           Indique si l'élément doit être désactivé
	 * @param bool $visible            Indique si l'élément doit être visible
     * @param string $fieldValue       Valeur à insérer dans l'input
     */
    public static function createInputInList(int $index, string $fieldName, string $inputType, string $label, string $labelClass, string $containerClass, bool $required=false, array $inputArgs=array(), bool $disabled=false, bool $visible=true, string $fieldValue=null) {
        UiUtils::createInput($fieldName . "[" . $index . "]", $inputType, $label, $labelClass, $containerClass, $required, $inputArgs, $disabled, $visible, $fieldValue);
    }
    
    /**
     * Permet de créer un <select></select> qui serait contenu dans une liste 
     * 
     * @param int $index
     * @param string $fieldName
     * @param string $label
     * @param string $labelClass
     * @param string $containerClass
     * @param array $mapValues
     * @param bool $required
     * @param array $selectArgs
     * @param bool $disabled
     * @param bool $visible
     * @param string $selectedValue
     */
    public static function createSelectInList(int $index, string $fieldName, string $label, string $labelClass, string $containerClass, array $mapValues=array(), bool $required=false, array $selectArgs=array(), bool $disabled=false, bool $visible=true, string $selectedValue=null) {
        UiUtils::createInput($fieldName . "[" . $index . "]", $inputType, $label, $labelClass, $containerClass, $required, $inputArgs, $disabled, $visible, $fieldValue);
    }
    
    
    /**
     * Permet de créer un groupe de radios boutons 
     * 
     * @param string $fieldName        Nom de l'élément
	 * @param string $label            Label de l'élément
     * @param array $labelsValues      Labels des radios boutons
	 * @param string $labelClass       Classe CSS du label
     * @param string $inputDivClass    Classe CSS de la div principale
	 * @param string $containerClass   Classe CSS de la div conteneur
	 * @param bool $required           Indique si l'élément est requis
	 * @param array $inputArgs         Tableau associatif contenant les arguments de l'élément HTML
	 * @param bool $disabled           Indique si l'élément doit être désactivé
	 * @param bool $visible            Indique si l'élément doit être visible
     */
	public static function createRadioGroup(string $fieldName, string $label, array $labelsValues=array(), string $labelClass, string $inputDivClass, string $containerClass, bool $required=false, array $inputArgs=array(), bool $disabled=false, bool $visible=true) {
	    $fieldValues = null;
	    $hasError = false;
	    $hasValue = false;
	    
	    // TODO: Remove GET access
	    if(isset($_GET[$fieldName])) {
	        $fieldValues = unserialize($_GET[$fieldName]);
	        $hasError = isset($fieldValues["error"]);
	        $hasValue = isset($fieldValues["value"]);
	    }
	    
	    // TODO: Refactor with createElement()
	    ?>
        <div id="container_<?php echo $fieldName; ?>" class="row form-group" <?php if(!$visible) { echo 'style="display:none;"'; } ?> >
        	<div class="<?php echo $labelClass; ?>" >
        		<label class="font-weight-bold control-label"><?php echo $label; ?> :</label>
        	</div>
        	<div class="<?php echo $inputDivClass; ?>">
           		<?php 
           		    $strInputArgs = "";
               		foreach ($inputArgs as $key => $value) {
               		  $strInputArgs .= $key . "=\"" . $value . "\" ";
               		}
           		?>
            		
           		<?php 
           		  foreach ($labelsValues as $value => $labelInput) {
           		      ?>
           		      <div class="custom-control custom-radio <?php echo $containerClass; ?>" <?php echo $strInputArgs; ?> >
           		      	<input id="<?php echo $fieldName . $value; ?>" <?php if($hasValue && $fieldValues["value"] == $value) { echo "checked"; } ?> type="radio" name="<?php echo $fieldName; ?>" value="<?php echo $value ?>" <?php if($disabled) { echo "disabled"; } ?> class="custom-control-input <?php if($hasError) { echo "is-invalid"; } ?>" >
           		      	<label class="custom-control-label" for="<?php echo $fieldName . $value; ?>">
           		      		 <?php echo $labelInput; ?>
           		      	</label>
           		      </div>
   		      	<?php 
           		  }
           		
           		  if($hasError) {
           		    ?>
   					<span class="invalid-feedback"><?php echo $fieldValues["error"]; ?></span>    					    
   					<?php 
           		}
           		?>
			</div>
		</div>
		<?php 
	}
	
	/**
	 * Raccourcis vers 
	 * createInput($fieldName, $inputType, $label, $labelClass, $containerClass, $required, $inputArgs, $disabled, $visible, $fieldValue)
	 * 
	 * @param string $fieldName        Nom de l'élément
	 * @param string $fieldValue       Valeur à insérer dans l'input
	 * @param string $inputType        Type de l'input HTML à créer
	 * @param string $label            Label de l'élément
	 * @param string $labelClass       Classe CSS du label
	 * @param string $containerClass   Classe CSS de la div conteneur
	 * @param bool $required           Indique si l'élément est requis
	 * @param array $inputArgs         Tableau associatif contenant les arguments de l'élément HTML
	 * @param bool $disabled           Indique si l'élément doit être désactivé
	 * @param bool $visible            Indique si l'élément doit être visible
	 */
	public static function createInputWithValue(string $fieldName, string $fieldValue=null, string $inputType, string $label, string $labelClass, string $containerClass, bool $required=false, array $inputArgs=array(), bool $disabled=false, bool $visible=true) {
	    UiUtils::createInput($fieldName, $inputType, $label, $labelClass, $containerClass, $required, $inputArgs, $disabled, $visible, $fieldValue);
	}
	
	/**
	 * Permet de créer un <input />
	 *
	 * @param string $fieldName        Nom de l'élément
	 * @param string $inputType        Type de l'input HTML à créer
	 * @param string $label            Label de l'élément
	 * @param string $labelClass       Classe CSS du label
	 * @param string $containerClass   Classe CSS de la div conteneur
	 * @param bool $required           Indique si l'élément est requis
	 * @param array $inputArgs         Tableau associatif contenant les arguments de l'élément HTML
	 * @param bool $disabled           Indique si l'élément doit être désactivé
	 * @param bool $visible            Indique si l'élément doit être visible
	 * @param string $fieldValue       Valeur à insérer dans l'input
	 */
	public static function createInput(string $fieldName, string $inputType, string $label, string $labelClass, string $containerClass, bool $required=false, array $inputArgs=array(), bool $disabled=false, bool $visible=true, string $fieldValue=null) {
	    $element = '<input class="' . ($inputType == 'file' ? 'form-control-file' : 'form-control') . '" ' 
	    	. ' height="40px" '
	 		. 'value="' . $fieldValue . '" type="' . $inputType . '" id="' . $fieldName . '" name="' . $fieldName . '" '
    	    . UiUtils::createElementArgs($inputArgs) . ' ' 
	        . ($disabled ? 'disabled ' : '') . '/>';
	    
	    
        UiUtils::createElement($fieldName, $element, $label, $labelClass, $containerClass, $visible);
	    
	}
	
	/**
	 * Permet de créer un <select></select>
	 *
	 * @param string $fieldName        Nom de l'élément
	 * @param string $label            Label de l'élément
	 * @param string $labelClass       Classe CSS du label
	 * @param string $containerClass   Classe CSS de la div conteneur
	 * @param array $mapValues         Tableau associatif contenant les valeurs du select
	 * @param bool $required           Indique si l'élément est requis
	 * @param array $selectArgs        Tableau associatif contenant les arguments de l'élément HTML
	 * @param bool $disabled           Indique si l'élément doit être désactivé
	 * @param bool $visible            Indique si l'élément doit être visible
	 * @param string $selectedValue    Valeur à sélectionner parmis la liste des valeurs
	 */
	public static function createSelect(string $fieldName, string $label, string $labelClass, string $containerClass, array $mapValues=array(), bool $required=false, array $selectArgs=array(), bool $disabled=false, bool $visible=true, string $selectedValue=null) {
	    
	    $element = '<select class="form-control" height="40px" '
		    . 'id="' . $fieldName . '" name="' . $fieldName . '" '
	    . UiUtils::createElementArgs($selectArgs) . ' '
	    . ($disabled ? 'disabled ' : '') . '>';
	    
	    foreach ($mapValues as $entryValue) {
	        $selected = $selectedValue == $entryValue["value"];
	        $element .= '<option value="' . $entryValue["value"] . '" ' . ($selected ? 'selected ' : '') . '>' . $entryValue["key"] . '</option>';
	    }
	    
	    $element .= '</select>';
	    
	    
	    UiUtils::createElement($fieldName, $element, $label, $labelClass, $containerClass, $visible);
	    
	}
	
	/**
	 * Permet de créer un <select></select>
	 *
	 * @param string $fieldName        Nom de l'élément
	 * @param string $label            Label de l'élément
	 * @param string $labelClass       Classe CSS du label
	 * @param string $containerClass   Classe CSS de la div conteneur
	 * @param array $mapValues         Tableau associatif contenant les valeurs du select
	 * @param bool $required           Indique si l'élément est requis
	 * @param array $selectArgs        Tableau associatif contenant les arguments de l'élément HTML
	 * @param bool $disabled           Indique si l'élément doit être désactivé
	 * @param bool $visible            Indique si l'élément doit être visible
	 * @param string $selectedValue    Valeur à sélectionner parmis la liste des valeurs
	 */
	public static function createSelectMultiple(string $fieldName, string $label, string $labelClass, string $containerClass, array $mapValues=array(), bool $required=false, array $selectArgs=array(), bool $disabled=false, bool $visible=true, string $selectedValue=null) {
		
		$element = '<select multiple class="form-control" height="40px" '
        				. 'id="' . $fieldName . '" name="' . $fieldName . '" '
        			. UiUtils::createElementArgs($selectArgs) . ' '
           		    		. ($disabled ? 'disabled ' : '') . '>';
           		    		
           		    		foreach ($mapValues as $entryValue) {
           		    			$selected = $selectedValue == $entryValue["value"];
           		    			$element .= '<option value="' . $entryValue["value"] . '" ' . ($selected ? 'selected ' : '') . '>' . $entryValue["key"] . '</option>';
           		    		}
           		    		
           		    		$element .= '</select>';
           		    		
           		    		
           		    		UiUtils::createElement($fieldName, $element, $label, $labelClass, $containerClass, $visible);
           		    		
	}
	
	/**
	 * Permet de créer les arguments d'un noeud HTML depuis un tableau associatif
	 * 
	 * @param array $elementArgs
	 * 
	 * @return string
	 */
	public static function createElementArgs(array $elementArgs=array()) : string {
	    $strElementArgs = "";
	    foreach ($elementArgs as $key => $value) {
	        $strElementArgs .= $key . "=\"" . $value . "\" ";
	    }
	    
	    return $strElementArgs;
	}	    
	
	/**
	 * Permet de créer un élément de l'ui
	 * 
	 * @param string $fieldName        Nom de l'élément
	 * @param string $element          Element HTML
	 * @param string $label            Label de l'élément
	 * @param string $labelClass       Classe CSS du label
	 * @param string $containerClass   Classe CSS de la div conteneur
	 * @param bool $visible            Indique si l'élément doit être visible
	 */
	public static function createElement(string $fieldName, string $element, string $label, string $labelClass, string $containerClass, bool $visible) {
	    ?>
        <div id="container_<?php echo $fieldName; ?>" class="row form-group" <?php if(!$visible) { echo 'style="display:none;"'; } ?>>
        	<div class="<?php echo $labelClass; ?>">
        		<label class="font-weight-bold control-label" for="<?php echo $fieldName; ?>"><?php echo $label; ?> :</label>
        	</div>
        	<div class="<?php echo $containerClass; ?>">
        		<?php 
        		echo $element;
        		?>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Permet de créer une UListe depuis une liste de données
	 * 
	 * @param string[] $list
	 */
	public static function createUList(array $list){
	    ?>
	    <ul>
    	    <?php 
    	    foreach ($list as $item) {
    	        ?>
    	        <li><?php echo $item; ?></li>
    	        <?php 
    	    }
    	    ?>
	    </ul>
	    <?php 
	}
	
	public static function createAlert(string $type, string $message) {
		?>
			<div class="alert alert-<?php echo $type; ?> alert-dismissible fade show" role="alert">
			  <?php echo $message; ?>
			  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
			    <span aria-hidden="true">&times;</span>
			  </button>
			</div>
		<?php 
	}
}
?>