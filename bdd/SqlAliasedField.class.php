<?php 
namespace framework\bdd;

/**
 * Représentation d'un champ SQL utilisé dans une requête SELECT avec un alias
 * 
 * @author Vince
 *
 */
class SqlAliasedField {
       
    /**
     * Alias du champ SQL
     * 
     * @var string
     */
    public $alias = null;
    
    /**
     * Champ SQL
     *
     * @var SqlField
     */
    public $field;
    
    function __construct(SqlField $field, string $alias){
    	$this->field = $field;
    	$this->alias = $alias;
    }
}
?>