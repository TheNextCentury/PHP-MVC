<?php

namespace framework\models;

use framework\Config;
use framework\bdd\DbConnection;
use framework\bdd\SqlField;
use framework\bdd\SqlForeignField;
use framework\utils\LogUtils;
use framework\utils\SqlUtils;
use framework\exception\IllegalAccessException;
use framework\exception\IllegalArgumentException;

/**
 * Representation of datable row,
 *
 * /!\ la table en bdd doit obligatoirement avoir un clé primaire nommée "id"
 */
class Model {
    
	/**
	 * fields mapping cache 
	 * 
	 * @var SqlField[]
	 */
    protected static $fields;

    function __construct(array $data = array()) {
        $this->fill($data);
    }
    
    /**
     * Return discriminant fields of model
     * 
     * @return SqlField[]  
     */
    public static function discriminantFields() : array {
    	throw new IllegalAccessException("Unimplemented function: " . static::class . "->distinctFields()");
    }
    
    /**
     * Mapping between database fields and model attributes
     */
    protected static function fields() {
    	throw new IllegalAccessException("Unimplemented function: " . static::class . "->fields()");
    }
    
    /**
     * Use to set the value of discriminant attributes after insert
     * 
     * @param $conn DbConnection
     */
    protected function setDiscriminantValues(DbConnection $conn) {
    	throw new IllegalAccessException("Unimplemented function: " . static::class . "->setDiscriminantValues()");
    }
       
    /**
     * Find specific fields of model
     *
     * @param $filter function(SqlField):bool filter function 
     *
     * @return SqlField
     */
    public static function findFields($filter = null) {
    	if($filter == null) {
    		return static::getFields();
    	}
    	return array_filter(static::getFields(), $filter);
    }
    
    /**
     * Find specific field of model or foreign model
     *
     * @param array $path
     * 
     * @return SqlField
     */
    public static function findField($path) {
    	if(is_string($path)) {
    		$path = array($path);
    	}
    	$field = null;
    	$model = static::class;
    	
    	$stepCount = count($path);
    	for ($i = 0; $i < $stepCount; $i++) {
    		if($model == null) {
    			throw new IllegalArgumentException("path", "problem with this step");
    		}
    		$step = $path[$i];
    		$modelFields = $model::findFields();
    		
    		foreach ($modelFields as $modelField) {
    			if($modelField->name == $step) {
    				$field = $modelField;
    				break;
    			}
    		}
    		
    		if($field instanceof SqlForeignField) {
    			$model = $field->foreignClass;
    		} else {
    			$model = null; 
    		}
    	}
    	
    	return $field;
    }
    
    /**
     * Insert model data into corresponding table of database
     * 
     * @param DbConnection $conn
     * @param bool $activeTransaction
     * 
     * @throws \Exception
     */
    public function insert(DbConnection $conn, bool $activeTransaction = false) {
    	$fields = $this->getFields();
    	
    	$modelFields = array();
    	$extraFields = array();
    	
    	foreach ($fields as $field) {
    		if($field instanceof SqlForeignField && $field->dir == SqlForeignField::DIR_MODEL_TO_ANOTHER || !($field instanceof SqlForeignField)) {
    			$modelFields[] = $field;
    		} else {
    			$extraFields[] = $field;
    		}
    	}
    	
    	$sqlFields = "";
    	$sqlValues = "";
    	foreach ($modelFields as $sqlField) {
    		$sqlFields .= $sqlField->sqlName . ",";
    		$sqlValues .= $this->convertModelToSqlValue($sqlField) . ",";
    	}
    	$sqlFields = substr($sqlFields, 0, -1);
    	$sqlValues = substr($sqlValues, 0, -1);
    	
    	if(!$activeTransaction){
    		$conn->startTransaction();
    	}
    	
    	try {
	    	$conn->execute(
				"INSERT INTO " . Config::get(Config::DB_NAME) . "." . static::TABLE_NAME . " (" . $sqlFields . ") "
	    			. "VALUES(" . $sqlValues . ");"
	    	);
	    	
	    	$this->setDiscriminantValues($conn);
	    	
	    	foreach ($extraFields as $extraField) {
	    		$fieldName = $extraField->name;
	    		$foreignModel = $this->$fieldName;
	    		
	    		// TODO: manage multiple reference fields
	    		$foreignFieldName = $extraField->referenceFields[0]->name;
	    		$foreignModel->$foreignFieldName = $this->id;
	    		$foreignModel->insert($conn, true);
	    	}
    	
    	} catch (\Exception $e) {
    		if(!$activeTransaction) {
    			$conn->rollback();
    		}
    		LogUtils::error($e);
    		throw $e;
    	}
    	
    	if(!$activeTransaction){
    		$conn->commit();
    	}
    }
    
    
    /**
     * Permet de mettre à jour un model en base de données
     *
     * @param DbConnection
     * @throws \Exception
     */
    public function update(DbConnection $conn, bool $activeTransaction = false) {
    	$discriminantFields = $this->discriminantFields();
    	
    	$fields = $this->getFields();
    	$modelFields = array();
    	$extraFields = array();
    	foreach ($fields as $field) {
    		if ($field instanceof SqlForeignField && $field->dir == SqlForeignField::DIR_MODEL_TO_ANOTHER || !($field instanceof SqlForeignField)) {
    			$modelFields[] = $field;
    		} else {
    			$extraFields[] = $field;
    		}
    	}
    	
    	$rq = "UPDATE " . Config::get(Config::DB_NAME) . "." . static::TABLE_NAME . " SET ";
    	
    	$values = array();
    	foreach ($modelFields as $modelField) {
    		if($modelField instanceof SqlForeignField) {
    			$fieldName = $modelField->name;
    			foreach ($modelField->referenceFields as $referenceField) {
    				$values[] = $modelField->sqlName . " = " . $this->convertModelToSqlValue($referenceField, $this->$fieldName);
    			}
    		} else {
    			$values[] = $modelField->sqlName . " = " . $this->convertModelToSqlValue($modelField, $this);
    		}
    	}

    	$rq .= implode(", ", $values) . " WHERE ";
    	
    	$conditions = array();
    	foreach ($discriminantFields as $discriminantField) {
    		$fieldName = $discriminantField->name;
    		if($this->$fieldName == null) {
    			throw new IllegalAccessException("discriminant value not set !");
    		}
    		$conditions[] = $discriminantField->sqlName . " = " . $this->convertModelToSqlValue($discriminantField, $this);
    	}
    	
    	$rq .= implode(" AND ", $conditions);
    	
    	if(!$activeTransaction){
    		$conn->startTransaction();
    	}
    	
    	try {
	    	$conn->execute($rq);
    	
	    	foreach ($extraFields as $foreignField) {
	    		$fieldName = $foreignField->name;
	    		$foreignModel = $this->$fieldName;
	    		
	    		// TODO: manage multiple primary key
	    		foreach ($foreignField->referenceFields as $referenceField) {
	    			$foreignFieldName = $referenceField->name;
	    			$foreignModel->$foreignFieldName = $this->id;
	    		}
	    		
	    		$foreignModel->update($conn, true);
	    	}
    	} catch (\Exception $e) {
    		if(!$activeTransaction) {
    			$conn->rollback();
    		}
    		LogUtils::error($e);
    		throw $e;
    	}
    	
    	if(!$activeTransaction){
    		$conn->commit();
    	}
    }
    
    /**
     * Permet de supprimer un model en base de données
     *
     * @throws \Exception
     */
    public function delete(DbConnection $conn, bool $activeTransaction = false) {
    	$extraFields = array_filter(self::getFields(), function(SqlField $field) {
    		return ($field instanceof SqlForeignField && $field->dir == SqlForeignField::DIR_ANOTHER_TO_MODEL);
    	});
    	if(!$activeTransaction){
    		$conn->startTransaction();
    	}
    	
    	try {
	    	foreach ($extraFields as $foreignField) {
	    		$fieldName = $foreignField->name;
	    		$foreignModel = $this->$fieldName;
	    		$foreignModel->delete($conn, true);
	    	}
	    	$conn->execute("DELETE FROM " . Config::get(Config::DB_NAME) . "." . static::TABLE_NAME . " WHERE id=" . $this->id);
    	} catch (\Exception $e) {
    		if(!$activeTransaction) {
    			$conn->rollback();
    		}
    		LogUtils::error($e);
    		throw $e;
    	}
    	
    	if(!$activeTransaction){
    		$conn->commit();
    	}
    }
    
    /**
     * Return all model fields
     *
     * @return SqlField[]
     */
    private static function getFields() {
    	if(static::$fields == null) {
    		static::$fields = static::fields();
    	}
    	return static::$fields;
    }
    
    /**
     * Fonction utilitaire permettant de vérifier si la clé est présente dans le tableau de donnée provenant de la bdd
     *
     * @param string $key
     * @param array $data
     * @return null|\DateTime|boolean|string|integer
     */
    private function bindFieldFromQuery(SqlField $field, array $data) {
    	$fieldName = $field->name;
    	$this->$fieldName = null;
    	
    	if(array_key_exists($fieldName, $data)) {
    		$this->$fieldName = $data[$fieldName];
    	}
    }
    
    /**
     * Fonction utilitaire permettant de vérifier si la clé est présente dans le tableau de donnée provenant de la bdd
     *
     * @param string $key
     * @param array $data
     * @return null|\DateTime|boolean|string|integer
     */
    private function bindForeignFieldFromQuery(SqlForeignField $field, array $data) {
    	$fieldName = $field->name;
    	$this->$fieldName = null;
    	
    	if(array_key_exists($fieldName, $data)) {
    		$className = $field->foreignClass;
    		$this->$fieldName = new $className($data[$fieldName]);
    	}
    }
    
    /**
     * Mapping between database query results and binding model values
     *
     * @param array $data
     * 					results from database query pre-transform:
     * 						- simple value: array[$modelAttr, $sqlValue]
     * 						- linked model: array[$modelAttr, array[$linkedModelAttr, $linkedModelSqlValue]]
     *
     */
    private function fill(array $data) {
    	$fields = $this->getFields();
    	foreach ($fields as $field) {
    		if($field instanceof SqlForeignField) {
    			$this->bindForeignFieldFromQuery($field, $data);
    		} else {
    			$this->bindFieldFromQuery($field, $data);
    		}
    	}
    }
    
    /**
     * Converti une donnée provenant d'un model en une donnée utilisable en SQL
     *
     * @param SqlField $field
     * @param Model $model
     * @throws \Exception
     * @return string
     */
    private function convertModelToSqlValue(SqlField $field) : string {
    	$fieldName = $field->name;
    	
    	if($field instanceof SqlForeignField) {
    		// TODO: manage multiple reference fields
    		$foreignFieldName = $field->referenceFields[0]->name;
    		$modelValue = $this->$fieldName->$foreignFieldName;
    	} else {
    		$modelValue = $this->$fieldName;
    	}
    		
    	return SqlUtils::addQuote($field, SqlUtils::ensureSqlValue($field, $modelValue));
    }
    
    /**
     * Converti une donnée provenant d'un model en une donnée utilisable en SQL
     *
     * @param SqlForeignField $field
     * @param Model $model
     * @throws \Exception
     * @return string[]
     */
    private function convertForeignModelToSqlValues(SqlForeignField $field, Model $model) : array {
    	$fieldName = $field->name;
    	
    	$result = array();
    	foreach ($field->referenceFields as $referentField) {
    		$result[$referentField->name] = $this->convertModelToSqlValue($referentField, $model->$fieldName);
    	}
    	return $result;
    }
}
?>