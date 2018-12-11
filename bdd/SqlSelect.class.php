<?php 
namespace framework\bdd;

use framework\exception\IllegalArgumentException;

/**
 * Représentation d'une requête SQL de type SELECT
 * 
 * @author Vince
 *
 */
class SqlSelect {
	
	/**
	 * 
	 * @var string
	 */
	private $modelClass;
    
    /**
     * Champs à sélectionner
     * 
     * @var array[string][AliasedQueryField]
     */
	private $fields = array();
    
    /**
     * Table avec alias
     * 
     * @var SqlAliasedTable
     */
    private $table;
    
    /**
     * Tableau de SqlJoin représentant les jointures
     * 
     * @var array[string][SqlJoin]
     */
    private $links = array();
    
    /**
     * Condition du SELECT
     * 
     * @var ConditionExpression
     */
    public $conditions;
    
    /**
     * Ordre de trie des données
     *
     * @var SqlOrderedField[]
     */
    private $orderedFields = array();
    
    /**
     * Indique si des OR doivent être placés pour les prochaine conditions ajoutées
     * 
     * @var bool
     */
    private $jonction = false;
    
    /**
     * @param string $modelClass
     */
    function __construct(string $modelClass) {
    	$this->modelClass = $modelClass;
    	$this->table = new SqlAliasedTable("t0", array(), $modelClass::TABLE_NAME);
    	$this->conditions = new ConditionExpression();
    }
    
    public function getFields() {
    	return $this->fields;
    }
    
    public function getTable(){
    	return $this->table;
    }
    
    /**
     * Permet d'effectuer une sélection et de charger le résultat dans un model
     *
     * @param SqlField[] $fields
     *
     * @return SqlSelect
     */
    public function addField(SqlField $field) : SqlSelect {
    	$this->addFields(array($field));
    	return $this;
    }
    
    /**
     * Permet d'effectuer une sélection et de charger le résultat dans un model
     *
     * @param SqlField[] $fields
     *
     * @return SqlSelect
     */
    public function addFields(array $fields) : SqlSelect {
    	foreach ($fields as $field) {
    		$alias = $field->name;
    		$this->fields[$alias] = new AliasedQueryField($this->table, $field, $alias, array());
    	}
    	return $this;
    }
    
    /**
     * Permet d'effectuer une sélection sur un model lié et de charger le résultat dans un ce dernier
     *
     * @param string $foreignModelClass
     * @param string[] $path
     * @param ConditionExpression $conditions
     *
     * @return SqlSelect
     */
    public function fetch($path) : SqlSelect {
    	if(count($path) == 0) {
    		throw new IllegalArgumentException("path", "empty path not allowed !");
    	}
    	if(is_string($path)) {
    		$path = array($path);
    	}
    	
    	$field = $this->modelClass::findField($path);
    	if(!($field instanceof SqlForeignField)) {
    		throw new IllegalArgumentException("path", "no foreign field for this path !");
    	}
    	$foreignClass = $field->foreignClass;
    	$aliasedTable = new SqlAliasedTable($this->generateTableAlias(), $path, $foreignClass::TABLE_NAME);
    	
    	$primaryField = null;
    	$referenceField = null;
    	
    	$aliasPrefix = implode("_", $path);
    	
    	$foreignFields = $foreignClass::findFields();
    	foreach ($foreignFields as $foreignField) {
    		$alias = ($aliasPrefix != "" ? $aliasPrefix . "_" . $foreignField->name : $foreignField->name);
    		
    		$queryField = new AliasedQueryField($aliasedTable, $foreignField, $alias, $path);
    		$this->fields[$alias] = $queryField;
    	}
    	
    	// TODO: manage multiple discriminant fields
    	$primaryField = new QueryField($this->findTable(array_slice($path, 0, -1)), $field);
    	$referenceField = new QueryField($aliasedTable, $field->referenceFields[0]);
    	
    	$conditions = new ConditionExpression();
    	$conditions->operation($primaryField, "=", $referenceField);
    	
    	$this->addLink($aliasedTable, $conditions);
    	return $this;
    }
    
    /**
     * Permet d'effectuer une sélection en utilisant une fonction d'aggrégation
     *
     * @param SqlAliasedTable $table
     * @param SqlField $field
     * @param string $function
     *
     * @return SqlSelect
     */
    public function aggregateField(SqlField $field, string $function, array $path = array()) : SqlSelect {
    	$alias = implode("_", $path);
    	$alias .= ($alias == "" ? "" : "_") . $field->name;  
    	$this->fields[$alias] = new AggregateQueryField($function, $this->table, $field, $alias, $path);
    	return $this;
    }
    
    public function addLink(SqlAliasedTable $table, ConditionExpression $conditions, string $linkType = "LEFT OUTER JOIN") : SqlSelect {
    	$this->links[$table->getKey()] = new SqlJoin($linkType, $table, $conditions);
    	return $this;
    }
    
    public function addOrderedField($path, string $orderDirection) {
    	$field = $this->findField($path);
    	$this->orderedFields[] = new SqlOrderedField($field, $orderDirection);
    	return $this;
    }
    
    public function findTable(array $path) : SqlAliasedTable {
    	if(count($path) == 0) {
    		return $this->table;
    	}
    	
    	$alias = implode("_", $path);
    	if(array_key_exists($alias, $this->links))
    		return $this->links[$alias]->table;
    	
    	throw new IllegalArgumentException("path", "no table for this path: " . $alias);
    }
    
    public function findField($path) : QueryField {
    	if(is_string($path)) {
    		$path = array($path);
    	}
    	$field = null;
    	
    	$alias = implode("_", $path);
    	if (!array_key_exists($alias, $this->fields)) {
    		$table = $this->findTable(array_slice($path, 0, -1));
    		return new QueryField($table, $this->modelClass::findField($path));
    	} else {
    		return $this->fields[$alias];
    	}
    }
    
    /**
     * Contruit la chaîne SQL
     * 
     * @return string
     */
    public function toString(string $dbName) {
        $sql = "SELECT ";
        foreach ($this->fields as $alias => $field) {
        	$sql .= $field->toString() . ",";
        }
        $sql = substr($sql, 0, strlen($sql) - 1);
        
        $sql .= " FROM " . $dbName . "." . $this->table->getSqlName() . " " . $this->table->getAlias() . " ";
        foreach ($this->links as $tableKey => $link) {
        	$sql .= $link->toString($dbName) . " ";
        }

        $conditionStr = $this->conditions->toString();
        $sql .= ($conditionStr == "") ? "" : "WHERE " . $conditionStr;
        
        $orderClause = "";
        foreach ($this->orderedFields as $orderedField) {
        	$orderClause .= $orderedField->toString();
        }
        $sql .= ($orderClause == "") ? "" : "ORDER BY " . $orderClause . " ";
        
        
        return $sql;
    }
    
    private function generateTableAlias() {
    	return "t" . strval(count($this->links) + 1);
    }
}
?>