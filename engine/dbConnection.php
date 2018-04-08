<?php

include realpath(__DIR__ . "/../exception") . "/connectionException.php";
define("UPDATE_EVERY", 100); // "lines saved" status msg will be updated every UPDATE_EVERY lines written in TSV file


class dbConnection {

	private $connection = null;
	private $interfMgr = null;
	private $fileMgr = null;

	//---------------------------------------------------------------
	function __construct($interf,$file) {
		$this->interfMgr = $interf;
		$this->fileMgr = $file;
	}
	//---------------------------------------------------------------
	function initConnection($connString, $user, $pw) {
		$this->connection = @oci_connect( $user , $pw, $connString);
		if($this->connection === false) {
			$this->connection = null;
			throw new connectionException("CANNOT CONNECT TO SERVER");
		}
	}
	//---------------------------------------------------------------
	function query_simple($sql) {
		$pars = oci_parse($this->connection, $sql);
		oci_set_prefetch($pars,300);
		oci_execute($pars);

		return $pars;
	}
	//---------------------------------------------------------------
	function query_export($indicCode, $sql) {
		
		$arrCpt = 0;
		
		$this->fileMgr->open();
		
		$pars = $this->query_simple($sql);

		while(($row = oci_fetch_array($pars,OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
			
			$maxCol = count($row);
			
			if($arrCpt == 0) {
				// print header	
				$currentCol = 0;
				foreach($row as $h => $x) {
					$currentCol++;
					if($currentCol == $maxCol) {  // dernière colonne
						$this->fileMgr->add($h . "\r\n");
					} else { // autre colonne
						$this->fileMgr->add($h . "\t");
					}
				} // end foreach
			}
			
			$arrCpt++;
			
			$currentCol = 0;
			// print data
			foreach($row as $colName => $value) {
				$currentCol++;
				if($currentCol == $maxCol) {  // dernière colonne
					$this->fileMgr->add($value . "\r\n");
				} else { // autre colonne
					$this->fileMgr->add($value . "\t");
				}
			}	// end foreach colonnes

			if($arrCpt % UPDATE_EVERY === 0)
				$this->interfMgr->updateCounterArea($indicCode, $arrCpt, $this->fileMgr->getFilePathForDisplay());
		} // end while lines
		
		$this->interfMgr->updateCounterArea($indicCode, $arrCpt, $this->fileMgr->getFilePathForDisplay());
		
		$this->fileMgr->close();
		
		$this->interfMgr->addArea4($indicCode, $this->fileMgr->getFilePathForDisplay(), $this->fileMgr->getFileSize());
		
	}
	//---------------------------------------------------------------

}




?>