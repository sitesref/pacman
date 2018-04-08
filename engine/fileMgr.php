<?php


class FileManager {
	
	private $filePath;
	private $fileHandle;
	
	function __construct() {
		
	}
	
	function setFilePath($filePath) {
		$this->filePath = $filePath;
	}
	
	function getFilePath() {
		return $this->filePath;
	}
	
	function getFilePathForDisplay() {
		return str_replace(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, $this->filePath);
	}
	
	function open() {		
		$this->fileHandle = fopen($this->filePath, 'w');		
	}
	
	function add($content) {
		fwrite($this->fileHandle, $content);
	}
	
	function close() {
		fclose($this->fileHandle);
	}
	
	function getFileSize() {
		return $this->FileSizeConvert(filesize($this->filePath));
	}
	
	function FileSizeConvert($bytes)
	{
		$bytes = floatval($bytes);
		$arBytes = array(
			0 => array(
				"UNIT" => "To",
				"VALUE" => pow(1024, 4)
			),
			1 => array(
				"UNIT" => "Go",
				"VALUE" => pow(1024, 3)
			),
			2 => array(
				"UNIT" => "Mo",
				"VALUE" => pow(1024, 2)
			),
			3 => array(
				"UNIT" => "Ko",
				"VALUE" => 1024
			),
			4 => array(
				"UNIT" => "octets",
				"VALUE" => 1
			),
		);
		
		$result = -1;

		foreach($arBytes as $arItem)
		{
			if($bytes >= $arItem["VALUE"])
			{
				$result = $bytes / $arItem["VALUE"];
				$result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
				break;
			}
		}
		return $result;
	}
	//---------------------------------------------------------------
	

	
	
	
	
}