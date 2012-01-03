<?php

// Include PowerProcess
require_once 'PowerProcess.class.php';

// Instance new PowerProcess class with 2 threads, 30 second timeout, 
// as daemon with logging to daemonlog.txt
$pp = new PowerProcess(2,30,true,'daemonlog.txt',false);

// Write some stuff to the log file
$pp->Log("Greetings! I am a Daemon example!");
$pp->Log("My PID is " . $pp->GetPID());
$pp->Log("I am going to write some stuff to a file now.");

// Echo this so the user can see that the daemon is starting
// The echo lines will go to STOUT instead of the log file
echo "Daemon started with 2 threads...\n";
echo "You can run `tail -f daemonlog.txt` to follow my progress...\n";

// Generate some fake data
$data = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15);

// Start the control loop
while ($pp->RunControlCode()) {
	// Check for data in the queue
	if (count($data)) {
		// Check if we can spawn
		if ($pp->SpawnReady()) {
			// Spawn a new thread
			$pp->SpawnThread('Thread-'.array_shift($data));
		}
	} else {
		// No more data - shutdown
		$pp->Shutdown();
		$pp->Log("The daemon is now exiting...");
	}
}

// Start the thread code
if ($pp->RunThreadCode()) {
	// Just write some stuff and sleep
	$pp->Log("This is " . $pp->WhoAmI() . " just saying hello world! Now to sleep for 5 seconds");
	sleep(5);
}