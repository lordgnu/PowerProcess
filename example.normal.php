<?php

require_once 'PowerProcess.class.php';

echo "----------Start----------\n";

$pp = new PowerProcess(10,10,false,'php://stdout');

$data = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15);

while ($pp->RunControlCode()) {
	if (count($data) > 0) {
		// We have data to process
		if ($pp->SpawnReady()) {
			$pp->threadData = array_shift($data);
			if (!$pp->SpawnThread()) {
				$pp->Log("Error spawning thread");
				
				// Sleep just in case
				sleep(1);
				
				// Add the data back to the queue
				$data[] = $pp->threadData;
			}
		}
	} else {
		// No more data to process
		$pp->Shutdown();
	}
}

if ($pp->RunThreadCode()) {
	$pp->Log("Processing: {$pp->threadData}");
	for ($i = 0; $i < $pp->threadData; $i++) {
		sleep(1);
		$pp->Log("Processed to {$i}");
	}
	exit(0);
}

echo "----------Stop-----------\n";

?>