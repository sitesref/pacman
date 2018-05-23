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
	
	public function checkInterval($frequency, $askedYear, $askedMonth) {
		// si frequency = YEAR, time value = YYYY			
		// si frequency = QUARTER, time value = YYYYQX
		// si frequency = MONTH, time value = YYYYMXX
		// askedYear = YYYY (ex: 2018)
		// askedMonth = M (ex: 3)
		 
		switch($frequency) {
			// intervalle ok si interval.end.year = askedYear - 1
			// ex intervalle ok:  begin = 1950 end = 2017
			case "YEAR":
				return 1;
				break;
			

			case "QUARTER":
				return 0;
				switch($askedMonth) {
					/* intervalle ok 
						si interval.end.year = askedYear - 1
						ET interval.end.quarter = 4
					 ex intervalle ok:  begin = 1950Q1 end = 2017Q4
					*/
					case 1:
					case 2:
					case 3:
						break;
						
					/* intervalle ok 
						si interval.end.year = askedYear
						ET interval.end.quarter = 1
					 ex intervalle ok:  begin = 1950Q1 end = 2018Q1
					*/
					case 4:
					case 5:
					case 6:
						break;
					
					/* intervalle ok 
						si interval.end.year = askedYear
						ET interval.end.quarter = 2
					 ex intervalle ok:  begin = 1950Q1 end = 2018Q2
					*/
					case 7:
					case 8:
					case 9:
						break;
					
					/* intervalle ok 
						si interval.end.year = askedYear
						ET interval.end.quarter = 3
					 ex intervalle ok:  begin = 1950Q1 end = 2018Q3
					*/					
					case 10:
					case 11:
					case 12:
						break;
				}
				break;
				
			/* intervalle ok 
				si interval.end.year = askedYear
				ET interval.end.month = askedMonth
			 ex intervalle ok:  begin = 1950M01 end = 2018M03
			*/
			case "MONTH":
				return 1;
				break;
			
			
		} // end switch frequency
	} // end function
	
	private function basicYearIsValid($year) {
		if(strlen($year) != 4)
			return 0;
		if(!is_int($year))
			return 0;
		if($year < 1900 || $year > 2100)
			return 0;
		
		return 1;
	}
	
	private function basicQuarterIsValid($quarter) {
		if(!in_array($quarter, array("1","2","3","4")))
			return 0;
		
		return 1;
	}
	
	private function basicMonthIsValid($month) {
		if(!in_array($month, array("01","02","03","04", "05", "06", "07", "08", "09", "10", "11", "12")))
			return 0;
		
		return 1;
	}	
	
	// time value = YYYYQX
	private function quarterIsValid($quarter) {
		if(strlen($quarter) != 6)
			return 0;
		if(substr($quarter,4,1) != "Q")
			return 0;
		
		$y = substr($quarter,0,4);
		$q = substr($quarter,5,1);
		
		if($this->basicYearIsValid($y)==0)
			return 0;
		
		if($this->basicQuarterIsValid($q)==0)
			return 0;
		
		return 1;
	}
	
	// time value = YYYYMXX
	private function monthIsValid($month) {
		if(strlen($month) != 7)
			return 0;
		if(substr($month,4,1) != "M")
			return 0;
		
		$y = substr($month,0,4);
		$m = substr($month,5,2);
		
		if($this->basicYearIsValid($y)==0)
			return 0;
		
		if($this->basicMonthIsValid($m)==0)
			return 0;
		
		return 1;
	}
	
	
}