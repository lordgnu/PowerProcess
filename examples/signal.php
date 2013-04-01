<?php

use BauerBox\PowerProcess\Posix\Signals;

include __DIR__ . '/../lib/PowerProcess/Autoloader.php';

BauerBox_PowerProcess_Autoloader::register();

if (false === array_key_exists(1, $_SERVER['argv']) || false === array_key_exists(2, $_SERVER['argv'])) {
    echo 'Usage: signal.php <PID> <SIGNAL>' . PHP_EOL .
        '  <PID>     The process ID to send signal to' . PHP_EOL .
        '  <SIGNAL>  The signal name or integer value' . PHP_EOL;
    exit(1);
}

$processId  =   (int) $_SERVER['argv'][1];
$signalName =   $_SERVER['argv'][2];

if (preg_match('@[^(0-9)]@', $signalName)) {
    $signal = Signals::signalNumber($signalName);
} else {
    $signal = (int) $signalName;
    $signalName = Signals::signalName($signal);
}

echo "Sending signal {$signalName} ($signal) to PID: {$processId}" . PHP_EOL;

if (false === Signals::sendSignal($signal, $processId, $errorMessage)) {
    echo ' - Sending failed! (' . $errorMessage . ')' . PHP_EOL;
    exit(1);
}

exit(0);
