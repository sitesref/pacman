<html>
<header>
<title>PACMAN@SOGETI</title>
<script src='js/jquery-3.3.1.min.js'></script>
<script src='js/pac.js'></script>
<link rel="stylesheet" href="css/fontawesome/fontawesome-all.css" type="text/css" media="all" />
<link rel="stylesheet" href="css/pac.css" type="text/css" media="all" />
</header>
<body>
<div class="counter" id="cnt" name="cnt"></div>

================================================================<br/>
Pacman - N. Nachtergaele@SOGETI<br/>
v 0.1 - v.29/03/2018<br/>
Current date and time: <?php echo date("d/m/Y H:i:s"); ?><br/>
================================================================<br/>

<div id="msg1" name="msg1"></div>
<?php

include "engine/fileMgr.php";
include "engine/loader.php";
include "engine/dbConnection.php";
include "engine/interf.php";
include "models/indicator.php";
include "models/timeInterval.php";

set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('memory_limit', '-1');

$loader = new loader();
$interf = new interf();
$fileMgr = new FileManager();

$interf->sendnow();

try {
	@ini_set('zlib.output_compression',0);
	@ini_set('implicit_flush',1);
	@ob_end_clean();
	set_time_limit(0);
	
	$interf->hideCounter();
	
	// DATA LOADING
	$loader->loadConfig();
	$interf->message(interf::MSG_OK, "msg1", " Loading CONFIG... OK");
	$interf->message(interf::MSG_OK, "msg1", " Asked year and month: " . $loader->getConfig("askedYear") . "-" . $loader->getConfig("askedMonth"));
	
	$loader->loadIndicators();
	$str = implode(", ",$loader->getIndicArray());
	$interf->message(interf::MSG_OK, "msg1", " Loading INDIC... OK (" . count($loader->getIndicArray()) . ": " . $str . ")");
	
	$loader->loadCreationView();
	$interf->message(interf::MSG_OK, "msg1", " Loading CREATION_VIEW.SQL... OK");
	
	$loader->loadExtractReportFromView();
	$interf->message(interf::MSG_OK, "msg1", " Loading EXTRACT_REPORT_FROM_VIEW.SQL... OK"); 
	
	$loader->checkArray($loader->getConfig("askedYear"),$loader->getConfig("askedMonth"));
	$interf->message(interf::MSG_OK, "msg1", " Checking array... OK");

	$loader->checkOutputFolder();
	$interf->message(interf::MSG_OK, "msg1", " Output folder... OK (" . str_replace(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, $loader->getConfig("outputFolder")) . ")");
	
	// DATABASE CONNECTION
	$interf->message(interf::MSG_NONE, "msg1", "Trying to connect to database...");
		
	$connector = new dbConnection($interf,$fileMgr);
	$connector->initConnection($loader->getConfig("connectionString"),
								$loader->getConfig("username"),
								$loader->getConfig("password"));
	$interf->message(interf::MSG_OK, "msg1", " CONNECTED TO SERVER/DATABASE");
	
	$alterInstruction = $loader->getConfig("alterSessionSQLInstruction");
	
	$counter = 1;
	$total = count($loader->getIndicArray());
	
	$interf->addPacman();	
	$interf->showCounter();
	$interf->setCounterValue(0,$total);
	
	echo "<div id='msg2' name='msg2'></div>";
		
	foreach($loader->getIndicArray() as $indic) {
		
		$fileMgr->setFilePath($loader->getConfig("outputFolder") . DIRECTORY_SEPARATOR . $indic->getCode() . ".tsv");
		
		// 1. create view
		$connector->query_simple($indic->getCreationViewInstruction());
		$interf->addArea1($indic->getCode(),$counter,$total);
		
		// 2. alter
		$connector->query_simple($alterInstruction);
		
		// 3. select
		$interf->addArea3($indic->getCode());
		$connector->query_export($indic->getCode(), $indic->getExtractFromViewInstruction());
		
		$interf->setCounterValue($counter,$total);
		
		$counter++;
	} // end foreach
	
	$interf->hidePacman();
	
	$interf->message(interf::MSG_FINISHED, "msg1", " ALL OPERATIONS EXECUTED SUCCESSFULLY :-)");
	
	$interf->line("00F");
}
catch(FileLoadException $fle) {
	$interf->message(interf::MSG_ERROR, "msg1", " FileLoadException loading file: " . $fle->getMessage());
	exit();
}
catch(FolderWriteException $fwe) {
	$interf->message(interf::MSG_ERROR, "msg1", " FolderWriteException loading file: " . $fwe->getMessage());
	exit();
}
catch(ConnectionException $ce) {
	$interf->message(interf::MSG_ERROR, "msg1", " ConnectionException: " . $ce->getMessage());
	exit();
}
catch(NoIndicException $nie) {
	$interf->message(interf::MSG_ERROR, "msg1", " NoIndicException: " . $nie->getMessage());
	exit();
}
catch(NoSQLException $nse) {
	$interf->message(interf::MSG_ERROR, "msg1", " NoSQLException: " . $nse->getMessage());
	exit();
}
catch(BadIntervalException $bie) {
	$interf->message(interf::MSG_ERROR, "msg1", " BadIntervalException: " . $bie->getMessage());
	exit();
}

catch(Exception $e) {
	$interf->message(interf::MSG_ERROR, "msg1", " Unknown exception: " . $e->getMessage());
	exit();
}
//---------------------------------------------------------------
?>

</body>
</html>