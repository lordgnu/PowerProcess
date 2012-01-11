<?php

// Include PowerProcess
require_once '../PowerProcess.class.php';

// Instance new PowerProcess class
$pp = new PowerProcess(2,30,false,'php://stdout',true);

// Make some fake data (We'll use this for names)
$data = array(
	'updater',
	'patcher',
	'watchdog'
);

// Start the Control Loop
while ($pp->RunControlCode()) {
	// Check if we still have data in our stack
	if (count($data)) {
		// Check to see if we can spawn a thread
		if ($pp->SpawnReady()) {
			// Assign thread data and spawn
			$pp->threadData = 10;
			$pp->SpawnThread(array_shift($data));
		}
	} else {
		// No more data so let's shutdown
		$pp->Shutdown();
	}
}

// Start the thread code
if ($pp->RunThreadCode()) {
	// Announce who we are
	$pp->Log("Hello! I am '" . $pp->WhoAmI() . "' and I am going to pretend to do some work now");
	
	// Sleep for 10 seconds
	for ($i = 0; $i < $pp->threadData; $i++) {
		sleep(1);
	}
}

?>