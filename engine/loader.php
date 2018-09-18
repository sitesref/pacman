<?php

include realpath(__DIR__ . "/../exception") . "/FileLoadException.php";
include realpath(__DIR__ . "/../exception") . "/FolderWriteException.php";
include realpath(__DIR__ . "/../exception") . "/NoIndicException.php";
include realpath(__DIR__ . "/../exception") . "/NoSQLException.php";
include realpath(__DIR__ . "/../exception") . "/BadIntervalException.php";
include "xmlLoader.php";

define("CONFIG_XML_FILEPATH",  realpath(__DIR__ . "/../config") . DIRECTORY_SEPARATOR  . "config.xml");

class loader {
	
	private $config;
	private $indicArray;
	//--------------------------------------------------------------------------
	function __construct() {
	}
	//--------------------------------------------------------------------------
	function loadConfig() {
		$xmlLoader = new xmlLoader(CONFIG_XML_FILEPATH);
		$this->config = $xmlLoader->loadData("config");
		return $this->config;
	}
	//--------------------------------------------------------------------------
	function loadIndicators() {
		$xmlLoader = new xmlLoader($this->getConfig("indicatorsPath"));
		$this->indicArray = $xmlLoader->loadData("indic");
		return $this->indicArray;
	}
	//--------------------------------------------------------------------------
	function loadCreationView() {
		$creationViewSQLPath = $this->getConfig("creationViewSQLPath");
		
		$lines = array();
		$sqlInstruction = "";
		$indicatorCode = "";
		$timeIntervalStr = null;
		$handle = @fopen($creationViewSQLPath, "r");
		if ($handle) {
			while (($line = fgets($handle)) !== false) {
				
				$line = str_replace(array("\n","\r", ";"),"",$line);
				
				// line commentaire (nom de l'indicateur)
				if(strpos($line, "--") === 0) {
					$indicatorCode = substr($line,2);
				}
				// line 0 - create or replace view
				else if(strpos($line, "create or replace view") !== false) {
					$lines[0] =  $line;
				}
				// line 1 - select * from
				else if(strpos($line, "SELECT * FROM (") !== false) {
					$lines[1] =  $line;
				}
				// line 2 - select * from
				else if(strpos($line, "select * from (select") !== false) {
					$lines[2] =  $line;
				}
				// line 3 - columns list
				else if(strpos($line, "CL_GROSS") === 0) {
					$lines[3] =  $line;
				}
				// line 4 - to_char
				else if(strpos($line, "to_char") !== false) {
					$lines[4] =  $line;
				}
				// line 5 - )
				else if(strpos($line, ")") === 1) {
					$lines[5] =  $line;
				}
				// line 6 - pivot
				else if(strpos($line, "pivot(") !== false) {
					$lines[6] =  $line;
					$from = strpos($line,"'");
					$to = strrpos($line,"'");
					$timeIntervalStr = substr($line,$from,($to-$from+1));
				}
				// line 7 - order by
				else if(strpos($line, "order by") !== false) {
					$lines[7] =  $line;
					$sqlInstruction = implode("", $lines);
					
					if(isset($this->indicArray[$indicatorCode])) {
					$this->indicArray[$indicatorCode]
						->setCreationViewInstruction($sqlInstruction)
						->setCreationViewTimeInterval(new TimeInterval("CREATE_VIEW",$timeIntervalStr));
					}
				
					$lines = array();
					$timeIntervalStr = "";
				}
			}
			fclose($handle);
		} else {
			throw new FileLoadException("CANNOT OPEN CREATION VIEW SQL FILE !");
		} 
	}
	//--------------------------------------------------------------------------
	function loadExtractReportFromView() {
		$extractFromViewSQLPath = $this->getConfig("extractFromViewSQLPath");
		
		$indicatorCode = "";
		$timeIntervalStr = null;
		$handle = @fopen($extractFromViewSQLPath, "r");
		if ($handle) {
			while (($line = fgets($handle)) !== false) {
				$line = str_replace(array("\n","\r", ";"),"",$line);
				$select = "";
				
				// line commentaire (nom de l'indicateur)
				if(strpos($line, "--") === 0) {
					$indicatorCode = substr($line,6);
				}
				// line 0 - select...
				else if(strpos($line, "select ") === 1) {
					// time interval str
					$from = strpos($line,"\"'");
					$to = strrpos($line,"\"");
					$timeIntervalStr = substr($line,$from,($to-$from+1));
					$sqlInstruction = $line;

					if(isset($this->indicArray[$indicatorCode])) {
					$this->indicArray[$indicatorCode]
						->setExtractFromViewInstruction($sqlInstruction)
						->setExtractFromViewTimeInterval(new TimeInterval("EXTRACT_FROM_VIEW",$timeIntervalStr));
					}
					$timeIntervalStr = null;
					}
				} // end while
				fclose($handle);  
			} else {
				throw new FileLoadException("CANNOT OPEN EXTRACT_REPORT_FROM_VIEW SQL FILE !");
			}// end else
	}
	//--------------------------------------------------------------------------
	public function getConfig($key) {
		return $this->config[$key];
	}
	//--------------------------------------------------------------------------
	public function getIndicArray() {
		return $this->indicArray;
	}
	//--------------------------------------------------------------------------
	public function checkOutputFolder() {
		$outputFolder = $this->getConfig("outputFolder");
		if(!is_dir($outputFolder))
			throw new FolderWriteException("OUTPUT FOLDER DOES NOT EXIST");
		else if(!is_writable($outputFolder))
			throw new FolderWriteException("OUTPUT FOLDER IS READ ONLY");
	}
	//--------------------------------------------------------------------------
	public function checkArray($askedYear,$askedMonth) {
		if(count($this->indicArray) == 0) {
			throw new NoIndicException("NO INDICATOR PROVIDED IN INDIC XML FILE!");
		} else {
			
			foreach($this->indicArray as $indicator) {
				
				if(empty($indicator->getCreationViewInstruction()))
					throw new NoSQLException($indicator->getCode() . ": CREATION VIEW - SQL INSTRUCTION MISSING !");
				if(empty($indicator->getCreationViewTimeInterval()))
					throw new NoSQLException($indicator->getCode() . ": CREATION VIEW - TIME INTERVAL MISSING !");
				if(empty($indicator->getExtractFromViewInstruction()))
					throw new NoSQLException($indicator->getCode() . ": EXTRACT FROM VIEW - SQL INSTRUCTION MISSING !");
				if(empty($indicator->getExtractFromViewTimeInterval()))
					throw new NoSQLException($indicator->getCode() . ": EXTRACT FROM VIEW -  TIME INTERVAL MISSING !");
				
				/*
				if($indicator->getCreationViewTimeInterval()->checkInterval($indicator->getFrequency(), $askedYear, $askedMonth) === 0)
					throw new BadIntervalException($indicator->getCode() . ": INTERVAL ERROR IN CREATION VIEW !");
				
				if($indicator->getExtractFromViewTimeInterval()->checkInterval($indicator->getFrequency(), $askedYear, $askedMonth) === 0)
					throw new BadIntervalException($indicator->getCode() . ": INTERVAL ERROR IN EXTRACT FROM VIEW !");
				*/
			}
		}
	}
	//--------------------------------------------------------------------------
	
	
}