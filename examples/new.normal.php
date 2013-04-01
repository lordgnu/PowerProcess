<?php

use BauerBox\PowerProcess\PowerProcess;
use BauerBox\PowerProcess\Job\Job;
use BauerBox\PowerProcess\Log\EchoLogger;

include __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/../lib/PowerProcess/Autoloader.php';

BauerBox_PowerProcess_Autoloader::register();

$timers = array(
    'threaded' => array()
);

function analyzeJob(Job $job)
{
    global $pp;

    $pp->getLogger()
        ->info("User-Function analog job called!")
        ->info('Execution Time : ' . $job->getRunningTime())
        ->info('Job Name       : ' . $job->getJobName())
        ->info('Job ID         : ' . $job->getJobId())
        ->info('Job PID        : ' . $job->getJobProcessId());

    return PowerProcess::CALLBACK_CONTINUE;
}

function runJob($currentJob)
{
    global $pp;

    for ($i = 0; $i < $currentJob; ++$i) {
        $pp->getLogger()->info("Counting!  {$i}  (Max: {$currentJob})");
        sleep(1);
    }
}

$pp = new PowerProcess(5, 30);
$pp->setLogger(new EchoLogger(null, true));
$pp->registerCallback('SIG_JOB_COMPLETE', 'analyzeJob', 'Job Completion Analysis');
$pp->start();

$jobs = array(10,9,8,7,6,5,4,3,2,1);
$currentJob = null;

// Run Linear First
/*
$timers['linear']['start'] = microtime(true);
foreach ($jobs as $job) {
    runJob($job);
}
$timers['linear']['stop'] = microtime(true);
*/

$timers['threaded']['start'] = microtime(true);
while ($pp->runLoop()) {
    // Check for work
    if (count($jobs) > 0) {
        if ($pp->isReadyToSpawn()) {
            $currentJob = array_pop($jobs);
            $pp->spawnJob('STUB-' . $currentJob);
        }
    } else {
        // No more work
        $pp->shutdown();
        $timers['threaded']['stop'] = microtime(true);

        foreach ($timers as $type => $timer) {
            $timer['total'] = $timer['stop'] - $timer['start'];

            echo str_repeat('=', 79) . PHP_EOL;
            echo "RUN REPORT: " . strtoupper($type) . PHP_EOL;
            echo str_repeat('=', 79) . PHP_EOL;
            echo " * START: {$timer['start']}" . PHP_EOL;
            echo " * STOP : {$timer['stop']}" . PHP_EOL;
            echo " * TOTAL: {$timer['total']}" . PHP_EOL;
            echo str_repeat('=', 79) . PHP_EOL . PHP_EOL;
        }
    }
}

if ($pp->isJobProcess()) {
    // Run the job code
    runJob($currentJob);
}

exit(0);
