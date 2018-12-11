<?php
namespace framework\models;

use framework\bdd\SqlField;
use framework\bdd\DbConnection;

/**
 * Model with id attribute as primary key (int|bigint| and maybe big unsigned int)
 */
class ClassicModel extends Model {
    
    const FIELD_ID = "id";
    
    /**
     * id field
     * 
     * @var int
     */
    public $id;
    
    function __construct(array $data = array()) {
        parent::__construct($data);
    }
    
    public static function discriminantFields() : array {
    	return array(static::findField(self::FIELD_ID));
    }
    
    /**
     * Use to set the value of discriminant attributes after insert
     *
     * @param $conn DbConnection
     */
    protected function setDiscriminantValues(DbConnection $conn){
    	$this->id = $conn->getLastId();
    }
    
    /**
     * Mapping between database fields and model attributes
     */
    protected static function fields() {
    	return array(new SqlField(self::FIELD_ID, "id", SqlField::INT));
    }
    
}
?>