<?php 
namespace framework\bdd;

/**
 * Représentation d'une requête SQL de type SELECT avec clause LIMIT
 * 
 * @author Vince
 *
 */
class SqlPageSelect extends SqlSelect {
	
	/**
	 * Clause limit
	 * 
	 * @var string
	 */
	public $limit = "";

	/**
     * @param SqlAliasedTable $table
     * @param int $pageSize
     * @param int $offset
     */
	function __construct(string $modelClass, int $pageSize, int $offset){
		parent::__construct($modelClass);
        $this->limit = $offset . ", " . $pageSize;
    }
    
    /**
     * Contruit la chaîne SQL
     * 
     * @return string
     */
    public function toString(string $dbName){
    	return parent::toString($dbName) . " LIMIT " . $this->limit;
    }
}
?>