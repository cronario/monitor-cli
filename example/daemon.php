<?php


require_once(realpath(__DIR__ . '/bootstrap.inc.php'));

$command = (isset($argv[1])) ? $argv[1] : '';
$appId = (isset($argv[2])) ? $argv[2] : Cronario\Producer::DEFAULT_APP_ID;


echo PHP_EOL;
echo " > Call daemon command: '{$command}' for appId '{$appId}'" . PHP_EOL.PHP_EOL;

// Cronario\Facade::dump();

$producer = Cronario\Facade::getProducer($appId);

switch ($command) {
    case '-start':
        $producer->start();
        break;

    case '-stop':
        $producer->stop();
        break;

    case '-kill':
        $producer->kill();
        break;

    case '-restart':
        $producer->restart();
        break;

    case '-reset':
        $producer->reset();
        break;

    case '-stats':
        print_r($producer->getStats());
        break;

    default:
        echo implode(PHP_EOL, [
            "   Error, command is undefined '{$command}' for appId '{$appId}' ",
            '   Daemon commands:',
            '       -start [appId]',
            '       -stop [appId]',
            '       -kill [appId]',
            '       -restart [appId]',
            '       -reset [appId]',
            '       -stats [appId]',
        ]);
        break;
}

echo PHP_EOL;
