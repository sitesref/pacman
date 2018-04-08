<?php

class interf {
	
	const MSG_ERROR = -1;
	const MSG_NONE = 0;
	const MSG_OK = 1;
	const MSG_FINISHED = 2;
	
	
	function __construct() {
	}
	//---------------------------------------------------------------
	function message($msgType,$area,$msg) {
		
		$begin = "";
		$end = "";
		switch($msgType) {
			case self::MSG_OK:
				$begin = "<i class='fa fa-check ok'></i>";
				break;
				
			case self::MSG_FINISHED:
				$begin = "<i class='fa fa-check finished'></i>";
				break;
			
			case self::MSG_ERROR:
				$begin = "<span class='error'><i class='fa fa-exclamation-triangle'></i>";
				$end = "</span>";
				break;			
		}
		
		echo '<script>
			$("#'.$area.'").html($("#'.$area.'").html() + "' . $begin . $msg . $end . '<br/>");
			</script>';
		$this->sendnow();
	}
	//---------------------------------------------------------------
	function addArea1($indicName,$counter,$total) {
		echo '<script>
			var l = document.createElement("hr");
			l.style = "border-color: #F00;";
			$("#msg2").append(l);
			var elm = document.createElement("div");
			elm.id = "' . $indicName . 'span1";
			elm.innerHTML = "' . $indicName . ' (' . $counter . '/' . $total . ') : (' . date("H:i:s") . ') View created/updated. Fetching data from server...";
			$("#msg2").append(elm);
			</script>';
		$this->toBottom();
		$this->sendnow();
	}
	//---------------------------------------------------------------
	function addArea3($indicName) {
		echo '<script>
			var elm = document.createElement("div");
			elm.id = "' . $indicName . 'span3";
			document.getElementById("msg2").appendChild(elm);
			</script>';
		$this->sendnow();
	}
	//---------------------------------------------------------------
	function updateCounterArea($indicName, $value, $filePath) {
		echo '<script>$("#' . $indicName . 'span3").html("' . $indicName . ' : ' . $value . ' lines saved in ' . $filePath . '");</script>';
		$this->sendnow();
	}
	//---------------------------------------------------------------
	function addArea4($indicCode, $filePath, $fileSize) {
		echo '<script>
			var elm = document.createElement("div");
			elm.id = "' . $indicCode . 'span4";
			elm.innerHTML = "' . $indicCode . ' : (' . date("H:i:s") . ') EXPORT FINISHED, File Size=' . $fileSize . ' (<a href=\'/pacman/output/' . $indicCode . '.tsv\'>link</a>)";
			document.getElementById("msg2").appendChild(elm);
			</script>';
			$this->sendnow();
	}
	//---------------------------------------------------------------
	function addPacman() {
		echo "<div style='margin-top:30px;'><img src='img/sogeti.jpg' id='sog' name='sog' width='400px' style='margin-bottom:100px;'/><img src='img/pac.gif' id='pac' name='pac'/><img src='img/ec.png' id='ec' name='ec'/></div>";
		$this->sendnow();
	}	
	//---------------------------------------------------------------
	function hidePacman() {
		echo "<script>$('#sog').hide();$('#pac').hide();$('#ec').hide();</script>";
		$this->sendnow();
	}	
	//---------------------------------------------------------------
	function showPacman() {
		echo "<script>$('#sog').show();$('#pac').show();$('#ec').show();</script>";
		$this->sendnow();
	}
	//---------------------------------------------------------------
	function hideCounter() {
		echo "<script>$('#cnt').hide();</script>";
		$this->sendnow();		
	}	
	//---------------------------------------------------------------
	function showCounter() {
		echo "<script>$('#cnt').show();</script>";	
		$this->sendnow();
	}	
	//---------------------------------------------------------------
	function setCounterValue($current,$total) {
		if($current == $total) {
			echo "<script>$('#cnt').html('" . $current . "/" . $total . "').removeClass('working').addClass('done');</script>";		
		} else {			
		   echo "<script>$('#cnt').html('" . $current . "/" . $total . "').removeClass('done').addClass('working');</script>";	
		}		
		$this->sendnow();
	}
	//---------------------------------------------------------------
	function toBottom() {
		echo "<script>bottom();</script>";
		$this->sendnow();
	}
	//---------------------------------------------------------------
	function line($color) {
		echo '<script>
		var l = document.createElement("hr");
		l.style = "border-color: #' . $color . ';";
		document.getElementById("msg2").appendChild(l);
		</script>';
		$this->sendnow();
	}
	//---------------------------------------------------------------
	function sendnow() {
		// This is for the buffer achieve the minimum size in order to flush data
		echo str_repeat(' ',1024*64);

		// Send output to browser immediately
		flush();

		// Sleep 0.1 sec so we can see the delay
		usleep(10);
	}
	//---------------------------------------------------------------
}