<?php

use BauerBox\PowerProcess\PowerProcess;
use BauerBox\PowerProcess\Job\Job;
use BauerBox\PowerProcess\Log\EchoLogger;

include __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/../lib/PowerProcess/Autoloader.php';

BauerBox_PowerProcess_Autoloader::register();

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


}

$threads = array(
    'UPDATER'   =>  array(
        'time'  =>  5,
        'descr' =>  'Emulates some sort of update process that would be taking place'
    ),
    'OPTIMIZER' =>  array(
        'time'  =>  15,
        'descr' =>  'Emulates some sort of optimization process that would be taking place'
    ),
    'NOTIFIER'  =>  array(
        'time'  =>  2,
        'descr' =>  'Emulates some sort of notification process that would be taking place'
    ),
    'CLEANUP'   =>  array(
        'time'  =>  10,
        'descr' =>  'Emulates a database cleanup process that can only be run after other jobs have stopped'
    )
);

$pp = new PowerProcess();

$pp->setMaxJobTime(30)->setMaxJobs(10)->setLogger(new EchoLogger());

$pp->registerCallback('SIG_JOB_COMPLETE', 'analyzeJob', 'Job Completion Analysis');
$pp->start();

$startTimer = time();
$cleanupInterval = 25;

// Start the loop
while ($pp->runLoop()) {
    // Check to make sure it is not time to cleanup
    if (time() - $startTimer > $cleanupInterval) {
        $pp->getLogger()->info("Detected that it is time to run cleanup...  Waiting for other jobs to complete");

        $ready = false;
        while ($ready === false) {
            if (false !== $pp->getJobStatus('UPDATER')) {
                // Updater is still running
                continue;
            }

            $pp->getLogger()->notice('Updater has shut down');
            if (false !== $pp->getJobStatus('OPTIMIZER')) {
                // Optimizer is still running
                continue;
            }

            $pp->getLogger()->notice('Optimizer has shut down');
            if (false !== $pp->getJobStatus('NOTIFIER')) {
                // Notifier is still running
                continue;
            }

            $pp->getLogger()->notice('Notifier has shut down');
            $ready = true;
        }

        $pp->spawnJob('CLEANUP');

        // Normally, we would wait for the cleanup process to complete and then resume the other
        // jobs, but since this is just a demo, we will shut down instead
        $pp->shutdown(0);
    } else {
        // Make sure clenup isn't running
        if (false !== $pp->getJobStatus('CLEANUP')) {
            continue;
        }

        // Check our process status
        if (false === $pp->getJobStatus('UPDATER')) {
            $pp->spawnJob('UPDATER');
            continue;
        }

        if (false === $pp->getJobStatus('OPTIMIZER')) {
            $pp->spawnJob('OPTIMIZER');
            continue;
        }

        if (false === $pp->getJobStatus('NOTIFIER')) {
            $pp->spawnJob('NOTIFIER');
            continue;
        }

        $pp->getLogger()->info('Running jobs: ' . $pp->getRunningJobCount());
        usleep(500000);
    }
}

// Once the job has been forked, it will start code execution here
// The parent process will also resume executing here after a shutdown
// unless one of the shutdown callbacks implicitly calls exit()
if ($pp->isParentProcess()) {
    exit(0);
}

// Past here we should be a Job process
$myData = $threads[$pp->whoAmI()];

$pp->getLogger()->info("I am {$pp->whoAmI()} and here's my description: " . $myData['descr']);

for ($i = 0; $i < $myData['time']; ++$i) {
    $pp->getLogger()->info($pp->whoAmI() . ' is doing stuff...');
    sleep(1);
}

$pp->getLogger()->notice($pp->whoAmI() . ' has completed all tasks');
exit(0);
