<?php 
namespace framework\bdd;

/**
 * Représentation d'une table SQL utilisée dans une requête SELECT avec un alias
 */
class SqlAliasedTable {

	/**
	 * Implode of path
	 *
	 * @var string
	 */
	private $flatPath;
	
    /**
     * Alias
     * 
     * @var string
     */
    private $alias;
    
    /**
     * Name in database
     * 
     * @var string
     */
    private $sqlName;
    
    public function __construct(string $alias, array $path, string $sqlName) {
        $this->alias = $alias;
        $this->flatPath = implode("_", $path);
        $this->sqlName = $sqlName;
    }
    
    public function getSqlName() : string {
    	return $this->sqlName;
    }
    
    public function getAlias() : string {
    	return $this->alias;
    }
    
    public function getKey() : string {
    	return $this->flatPath;
    }
}
?>