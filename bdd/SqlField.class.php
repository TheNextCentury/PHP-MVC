<?php
namespace framework\bdd;

/**
 * Représentation d'un champ dans une bdd, permet également de l'associer à l'attribut d'un model
 * 
 * @author Vince
 *
 */
class SqlField {
    
    /*
     * Types de données pris en charge par l'ORM 
     */
    const TEXT = 0;
    const INT = 1;
    const DATE = 2;
    const DATE_TIME = 3;
    const BOOLEAN = 4;
    const FLOAT = 5;
    
    /**
     * Nom de l'attribut du model
     *
     * @var string
     */
    public $name;
    
    /**
     * Nom du champ dans la bdd
     *
     * @var string
     */
    public $sqlName;
    
    /**
     * Type de la donnée
     * 
     * @var int
     */
    public $dataType;
    
    function __construct(string $name, string $sqlName, int $dataType) {
    	$this->name = $name;
        $this->sqlName = $sqlName;
        $this->dataType = $dataType;
    }
}

