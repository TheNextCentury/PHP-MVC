<?php

namespace framework\utils;

use framework\bdd\SqlField;

class SqlUtils {
	/**
	 * Converti une donnée provenant d'un model en une donnée utilisable en SQL
	 *
	 * @param SqlField $sqlField
	 * @param mixed $value
	 * @throws \Exception
	 * @return string
	 */
	public static function ensureSqlValue(SqlField $sqlField, $value) : string {
	
		if($value === null) {
			return "NULL";
		}
		
		switch ($sqlField->dataType) {
			case SqlField::DATE : return $value->format(DateUtils::SQL_DATE_FORMAT);
			case SqlField::DATE_TIME : return $value->format(DateUtils::SQL_DATE_TIME_FORMAT);
			case SqlField::BOOLEAN : return $value ? "1" : "0";
			case SqlField::FLOAT : return strval($value);
			case SqlField::INT : return strval($value);
			case SqlField::TEXT : return htmlspecialchars(str_replace("%", "\\%", str_replace("_", "\\_", str_replace("'", "\\'", $value))));
			default: throw new \Exception("Type inconnu: " . $sqlField->dataType);
		}
	}
	
	/**
	 * Converti une donnée provenant d'un model en une donnée utilisable en SQL
	 *
	 * @param SqlField $sqlField
	 * @param mixed $value
	 * @throws \Exception
	 * @return string
	 */
	public static function addQuote(SqlField $sqlField, string $ensuredValue) : string {
		
		if($ensuredValue === "NULL") {
			return $ensuredValue;
		}
		
		switch ($sqlField->dataType) {
			case SqlField::DATE : return "'" . $ensuredValue . "'";
			case SqlField::DATE_TIME : return "'" . $ensuredValue . "'";
			case SqlField::TEXT : return "'" . $ensuredValue . "'";
			default: return $ensuredValue;
		}
	}
}

