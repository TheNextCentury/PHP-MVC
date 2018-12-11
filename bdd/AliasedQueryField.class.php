<?php 
namespace framework\bdd;


/**
 * champs sql associé à une table
 *
 * @author Vince
 *
 */
class AliasedQueryField extends QueryField {
    
    /**
     * Champ sql
     * 
     * @var SqlAliasedField
     */
    public $aliasedField;
    
    /**
     * 
     * @var array
     */
    public $path;
    
    function __construct(SqlAliasedTable $table, SqlField $field, string $alias, array $path){
    	parent::__construct($table, $field);
    	$this->aliasedField = new SqlAliasedField($field, $alias);
    	$this->path = $path;
    }
    
    public function toString() {
    	return $this->getAccessString() . " " . $this->aliasedField->alias;
    }
}
?>