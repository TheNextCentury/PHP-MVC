<?php 
namespace framework\bdd;

/**
 * Représentation d'une jointure SQL
 * 
 * @author Vince
 *
 */
class SqlJoin {
    
    /**
     * Type de la jointure
     * 
     * @var string
     */
    public $joinType;
    
    /**
     * Table avec alias
     * 
     * @var SqlAliasedTable
     */
    public $table;
    
    /**
     * Condition de jointure
     * 
     * @var ConditionExpression
     */
    public $conditions;
    
    function __construct(string $joinType, SqlAliasedTable $table, ConditionExpression $conditions){
        $this->joinType = $joinType;
        $this->table = $table;
        $this->conditions = $conditions;
    }

    /**
     * Construit la chaine SQL
     * 
     * @return string
     */
    public function toString(string $dbName) : string {
        return $this->joinType . " " . $dbName . "." . $this->table->getSqlName() . " " . $this->table->getAlias() . " ON " . $this->conditions->toString();
    }
}
?>