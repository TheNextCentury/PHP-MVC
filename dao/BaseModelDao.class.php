<?php
namespace framework\dao;

use framework\Config;
use framework\bdd\DbConnection;
use framework\bdd\SqlAliasedField;
use framework\bdd\SqlField;
use framework\bdd\SqlSelect;
use framework\models\Model;
use framework\bdd\AliasedQueryField;

/**
 * Dao de base associé à un model
 */
abstract class BaseModelDao {

    /**
     * Database connection
     * 
     * @var DbConnection
     */
    protected $conn;
    
    /**
     * Class of the model
     * 
     * @var string
     */
    protected $modelClass;

    /**
     * @param DbConnection $conn
     * @param string $modelClass
     */
    function __construct(DbConnection $conn, string $modelClass) {
        $this->conn = $conn;
        $this->modelClass = $modelClass;
    }
    
    /**
     * Insert model data into the corresponding tableof database
     * 
     * @param Model $model model à insérer
     * @param Transaction
     * @throws \Exception
     */
    public function insert(Model $model, bool $activeTransaction = false) {
        $model->insert($this->conn, $activeTransaction);
    }
    
    /**
     * Permet de mettre à jour un model en base de données
     *
     * @param Model $model model à mettre à jour
     * @throws \Exception
     */
    public function update(Model $model, bool $activeTransaction = false) {
    	$model->update($this->conn, $activeTransaction);
    }
    
    /**
     * Permet de supprimer un model en base de données
     *
     * @param Model $model model à supprimer
     * @throws \Exception
     */
    public function delete(Model $model) {
        $model->delete($this->conn);
    }
    
    /**
     * Permet de supprimer un model en base de données
     *
     * @param int id du model à supprimer
     * @throws \Exception
     */
    public function deleteById(int $id) {
    	$this->conn->execute("DELETE FROM " . Config::get(Config::DB_NAME) . "." . $this->modelClass::TABLE_NAME . " WHERE id=" . $id);
    }
    
    /**
     * Permet d'exécuter la requête fournie par le SqlSelect
     *
     * @param SqlSelect $select requête à exécuter
     * @throws \Exception
     * @return array
     */
    public function selectRaw(SqlSelect $select) : array {
    	$result = array();
    	try {
    		$result = $this->conn->execute($select->toString(Config::get(Config::DB_NAME)));
    	} catch (\Exception $e) {
    		throw $e;
    	}
    	
    	return $result;
    }
    
    /**
     * Permet d'exécuter la requête fournie par le SqlSelect et de charger une liste de modèles
     *
     * @param SqlSelect $select requête à exécuter
     * @throws \Exception
     * @return array
     */
    public function selectFirst(SqlSelect $select) {
    	$result = $this->select($select);
    	return (count($result) > 0) ? $result[0] : null;
    }
    
    /**
     * Permet d'exécuter la requête fournie par le SqlSelect et de charger une liste de modèles
     *
     * @param SqlSelect $select requête à exécuter
     * @throws \Exception
     * @return array
     */
    public function select(SqlSelect $select) : array {
        $result = array();
        $queryResult = null;
        try {
        	$query = $select->toString(Config::get(Config::DB_NAME));
        	$queryResult = $this->conn->execute($query);
        } catch (\Exception $e) {
        	throw $e;
        }
        
        foreach ($queryResult as $data) {
            $resultLine = array();
            foreach ($select->getFields() as $field) {
				$this->setStepValue(
	            	0, 
                	$field,
	            	$resultLine, 
					$data[$field->aliasedField->alias]
	            );
            }
            $result[] = new $this->modelClass($resultLine);
        }
        
        return $result;
    }

    /**
     * Permet de construire le tableau associatif permettant de charger les models via la fonction Model.fill(array $data)
     * 
     * @param int $index
     * @param SqlAliasedField $field
     * @param array $data
     * @param null|\DateTime|boolean|string|integer $value
     */
    private function setStepValue(int $index, AliasedQueryField $field, array &$data, $value) {
        $finalStep = ($index == count($field->path));
        if($finalStep) {
        	$data[$field->aliasedField->field->name] = $this->getSqlValueForModel($field->aliasedField->field, $value);
        } else {
            $step = $field->path[$index];
            if(!array_key_exists($step, $data)) {
                $data[$step] = array();
            }
            $this::setStepValue($index + 1, $field, $data[$step], $value);
        }
    }

    /**
     * Permet de convertir une donnée de la bdd en une donnée utilisable par PHP
     * 
     * @param SqlField $sqlField
     * @param string $sqlValue
     * @throws \Exception
     * @return null|\DateTime|boolean|string|integer
     */
    private function getSqlValueForModel(SqlField $sqlField, string $sqlValue=null) {
        if($sqlValue == null) {
            return null;
        }
        
        switch ($sqlField->dataType) {
            case SqlField::DATE : return new \DateTime($sqlValue);
            case SqlField::DATE_TIME : return new \DateTime($sqlValue);
            case SqlField::BOOLEAN : return $sqlValue == 0 ? false : true;
            case SqlField::FLOAT : return floatval($sqlValue);
            case SqlField::INT : return intval($sqlValue);
            case SqlField::TEXT : return $sqlValue;
            default: throw new \Exception("Type inconnu: " . $sqlField->dataType);
        }
    }    
}
?>