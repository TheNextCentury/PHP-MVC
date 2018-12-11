<?php
namespace framework\bdd;

/**
 * Représentation d'une clé étrangère
 * 
 * TODO: manage multiple discriminant fields
 */
class SqlForeignField extends SqlField {
	
	const DIR_MODEL_TO_ANOTHER = 0;
	const DIR_ANOTHER_TO_MODEL = 1;

	public $foreignClass;
	
	/**
	 * Foreign key referenced fields
	 *
	 * @var SqlField[]
	 */
	public $referenceFields;
	
    /**
     *
     * @var int
     */
    public $dir;
    
    function __construct(string $foreignClass, string $name, string $sqlName, int $dataType, int $dir = self::DIR_MODEL_TO_ANOTHER) {
        parent::__construct($name, $sqlName, $dataType);
        $this->foreignClass = $foreignClass;
        $this->referenceFields = $foreignClass::discriminantFields();
        $this->dir = $dir;
    }
    
    public function setDir(int $dir) : SqlForeignField {
    	$this->dir = $dir;
    	return $this;
    }
}

