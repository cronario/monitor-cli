<?php
/**
 * Bootstrapping application
 */

// .....................
// .....................
// .....................
// .....................


use \Cronario\Facade as Facade;
use \Cronario\Producer as Producer;
use \Cronario\Logger\Journal as LoggerJournal;
use \Cronario\Storage\Mongo as StorageMongo;
use \Cronario\Queue as Queue;

Facade::addProducer(
    new Producer([
        Producer::P_CONFIG => [
            Producer::CONFIG_BOOTSTRAP_FILE            => __DIR__ . '/bootstrap.inc.php',
            Producer::CONFIG_SLEEP_MAIN_LOOP           => 2,
            Producer::CONFIG_SLEEP_STOP_LOOP           => 2,
            Producer::CONFIG_SLEEP_FINISH_MANAGER_LOOP => 2,
        ],
        Producer::P_LOGGER  => new LoggerJournal([
            LoggerJournal::P_CONSOLE_LEVEL  => LoggerJournal::LEVEL_DEBUG,
            LoggerJournal::P_JOURNAL_LEVEL  => LoggerJournal::LEVEL_DEBUG,
            LoggerJournal::P_JOURNAL_FOLDER => __DIR__ . '/logs/',
        ]),
        Producer::P_QUEUE   => new Queue(),
        Producer::P_STORAGE => new StorageMongo(),
        Producer::P_REDIS   => new \Predis\Client('127.0.0.1:6379')
    ])
);

Facade::addProducer(
    new Producer([
        Producer::P_APP_ID => 'inter-app',
        Producer::P_CONFIG => [
            Producer::CONFIG_BOOTSTRAP_FILE            => __DIR__ . '/bootstrap.inc.php',
            Producer::CONFIG_SLEEP_MAIN_LOOP           => 2,
            Producer::CONFIG_SLEEP_STOP_LOOP           => 2,
            Producer::CONFIG_SLEEP_FINISH_MANAGER_LOOP => 2,
        ],
        Producer::P_LOGGER  => new LoggerJournal([
            LoggerJournal::P_CONSOLE_LEVEL  => LoggerJournal::LEVEL_DEBUG,
            LoggerJournal::P_JOURNAL_LEVEL  => LoggerJournal::LEVEL_DEBUG,
            LoggerJournal::P_JOURNAL_FOLDER => __DIR__ . '/logs/',
        ]),
        Producer::P_QUEUE   => new Queue(),
        Producer::P_STORAGE => new StorageMongo(),
        Producer::P_REDIS   => new \Predis\Client('127.0.0.1:6379')
    ])
);


\Ik\Lib\Exception\ResultException::setClassIndexMap([
    'Cronario\\Exception\\ResultException' => 1,
    'Cronario\\Example\\ResultException'   => 2,
    'Messenger\\Curl\\ResultException'     => 3,
    'Messenger\\Hipchat\\ResultException'  => 4,
    'Messenger\\Sms\\ResultException'      => 5,
    'Messenger\\Cli\\ResultException'      => 6,
    'Messenger\\Mail\\ResultException'      => 7,
]);

$templates = realpath(__DIR__ . '/templates') . '/';
\Messenger\Template::setTemplateDir($templates);


// ==============================================================
// ==============================================================
// ==============================================================
