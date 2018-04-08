<?php

class xmlLoader {
	
	private $filePath;
	private $xml;
	
	function __construct($filePath) {
		$this->filePath = $filePath;
	}
	
	// récupération des données depuis le fichier XML
	public function loadData($target) {
			
		$indicData = @file_get_contents($this->filePath);
		if($indicData === false) {
			throw new FileLoadException("XML File not found");
		} 
		else 
		{
			$indicArray = array();
			$this->xml=@simplexml_load_string($indicData);
			if($this->xml === false) {
				throw new FileLoadException("XML File contents invalid");
			}
			
			switch($target) {
				case "indic":
					foreach($this->xml->indicator as $indic) {
						if((string)$indic["enabled"] === "TRUE") {
						$newIndic = new Indicator((string)$indic["tableName"],(string)$indic["reportType"]);
						$indicArray[(string)$indic["tableName"]] = $newIndic;
						}
					} // end foreach
					return $indicArray;
					break;
					
				case "config":
					return array(
						  "connectionString" => (string)$this->xml->connectionString,
						  "username" => (string)$this->xml->username,
						  "password" => (string)$this->xml->password,
						  "indicatorsPath" => (string)$this->xml->indicatorsPath,
						  "creationViewSQLPath" => (string)$this->xml->creationViewSQLPath,
						  "extractFromViewSQLPath" => (string)$this->xml->extractFromViewSQLPath,
						  "alterSessionSQLInstruction" => (string)$this->xml->alterSessionSQLInstruction,
						  "outputFolder" => (string)$this->xml->outputFolder,
						  "askedYear" => (string)$this->xml->askedYear,
						  "askedMonth" => (string)$this->xml->askedMonth
					);
					break;
			} // end switch

		} // end else
	} // end function
	
	public function setFilePath($filePath) {
		$this->filePath = $filePath;
	}
}