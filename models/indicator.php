<?php

class Indicator
{
	private $code;
	private $frequency;
	
	private $creationViewInstruction;
	private $creationViewTimeInterval;
	private $extractFromViewInstruction;
	private $extractFromViewTimeInterval;
	
	function __construct($code,$frequency) {
		$this->code = $code;
		$this->frequency = $frequency;
	}
	
	//----------------------------------
	// GETTERS
	//----------------------------------
	
	public function getCode() {
		return $this->code;
	}
	
	public function getFrequency() {
		return $this->frequency;
	}
	
	public function getCreationViewInstruction() {
		return $this->creationViewInstruction;
	}
	
	public function getCreationViewTimeInterval() {
		return $this->creationViewTimeInterval;
	}
	
	public function getExtractFromViewInstruction() {
		return $this->extractFromViewInstruction;
	}
	
	public function getExtractFromViewTimeInterval() {
		return $this->extractFromViewTimeInterval;
	}
	
	public function __toString() {
		return $this->getCode();
	}
	
	//----------------------------------
	// SETTERS
	//----------------------------------
	
	public function setCode($code) {
		$this->code = $code;
		return $this;
	}
	
	public function setFrequency($frequency) {
		$this->frequency = $frequency;
		return $this;
	}
	
	public function setCreationViewInstruction($creationViewInstruction) {
		$this->creationViewInstruction = $creationViewInstruction;
		return $this;
	}
	
	public function setCreationViewTimeInterval($creationViewTimeInterval) {
		$this->creationViewTimeInterval = $creationViewTimeInterval;
		return $this;
	}
	
	public function setExtractFromViewInstruction($extractFromViewInstruction) {
		$this->extractFromViewInstruction = $extractFromViewInstruction;
		return $this;
	}
	
	public function setExtractFromViewTimeInterval($extractFromViewTimeInterval) {
		$this->extractFromViewTimeInterval = $extractFromViewTimeInterval;
		return $this;
	}
}



?>