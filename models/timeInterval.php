<?php

class TimeInterval {
	
	private $source;
	private $begin;
	private $end;
	private $amount;
	
	function __construct($source,$timespanString) {
		$this->source = $source;
		switch($this->source) {
			case "CREATE_VIEW":
				$arr = explode(",",$timespanString);
				$this->begin = str_replace("'","",$arr[count($arr)-1]);
				$this->end = str_replace("'","",$arr[0]);
				$this->amount = count($arr);
				break;
				
			case "EXTRACT_FROM_VIEW":
				$arr = explode(",",$timespanString);
				$beginParts = explode("as",$arr[count($arr)-1]);
				$this->begin = str_replace(array("\"", " "),"",$beginParts[1]);
				$endParts = explode("as",$arr[0]);
				$this->end = str_replace(array("\"", " "),"",$endParts[1]);
				$this->amount = count($arr);
				break;
		}
	}
	
	public function getSource() {
		return $this->source;
	}
	
	public function getBegin() {
		return $this->begin;
	}
	
	public function getEnd() {
		return $this->end;
	}
	
	public function getAmount() {
		return $this->amount;
	}	
}