<?php
namespace framework\bdd;

/**
 * Représentation d'un champ dans une requête SQL
 *
 * @author Vince
 *
 */
class QueryField {
	
	/**
	 * 
	 * @var SqlAliasedTable
	 */
	public $table;
	
	/**
	 * 
	 * @var SqlField
	 */
	public $field;
	
	public function __construct(SqlAliasedTable $table, SqlField $field){
		$this->table = $table;
		$this->field = $field;
	}
	
	public function getAccessString() : string {
		return $this->table->getAlias() . "." . $this->field->sqlName;
	}
}
?>