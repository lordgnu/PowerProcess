<?php

// Include PowerProcess
require_once 'PowerProcess.class.php';

// Instance new PowerProcess class with 2 threads, 10 second timeout, 
// standalone, log to STDOUT and include debug logging
// Since we have a 10 second timeout, the last five threads will be
// terminated by PowerProcess
$pp = new PowerProcess(2,10,false,'php://stdout',true);

// Make some fake data
$data = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15);

// Start the control loop
while ($pp->RunControlCode()) {
	// Check if we have data to process
	if (count($data) > 0) {
		// We have data to process
		if ($pp->SpawnReady()) {
			// Assign the thread data
			$pp->threadData = array_shift($data);
			
			// Try to spawn the thread
			if (!$pp->SpawnThread()) {
				$pp->Log("Error spawning thread");
				
				// Sleep just in case
				sleep(1);
				
				// Add the data back to the queue
				$data[] = $pp->threadData;
			}
		}
	} else {
		// No more data to process - shutdown
		$pp->Shutdown(true);
	}
}

// Start the thread code

$pp->Log("Processing: {$pp->threadData}");
for ($i = 0; $i < $pp->threadData; $i++) {
	sleep(1);
	$pp->Log("Processed to {$i}");
}
// Exit thread
exit(0);

?>