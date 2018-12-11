<?php
namespace framework\bdd;

class AggregateQueryField extends AliasedQueryField {
   
   
    /**
     * Nom de la fonction
     * 
     * @var string
     */
    public $function;
    
    /**
     * 
     * @param string $function 
     */
    function __construct(string $function, SqlAliasedTable $table, SqlField $field, string $alias, array $path){
    	parent::__construct($table, $field, $alias, $path);
        $this->function = $function;
    }
    
    public function getAccessString() : string {
    	return $this->aliasedField->alias;
    }
    
    public function toString() {
    	return $this->function . "(" . parent::getAccessString() . ") " . $this->aliasedField->alias;
    }
}
