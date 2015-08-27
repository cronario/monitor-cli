<?php

require_once(realpath(__DIR__ . '/bootstrap.inc.php'));


$output = [];
$output['call'] = array_values($argv);

while (true) {

    // for ($i = 0; $i < 50; $i++) echo "\r\n";

    $out = '';
    $out .= '                                                                Data update speed : '. $stepper++ .PHP_EOL;

    // ===============================================================================
    // ===============================================================================

    // $mask = '___________________________________________________________________________________________'.PHP_EOL;
    $mask  = PHP_EOL;
    $mask .= 'Daemon "%s" : '.PHP_EOL;
    $mask .= "         pid %15.5s   |   created %5.10s   |   state %20.5s".PHP_EOL;
    $mask .= "         m_usage %11.7s   |   mp_usage %9.7s   |   life %21.5s ".PHP_EOL;
    $mask .= "         last circle %7.7s   |   pid_exists %7.7s ".PHP_EOL;
    $mask .= '___________________________________________________________________________________________'.PHP_EOL;

    $producersStats = \Cronario\Facade::getProducersStats();
    if(is_array($producersStats)) {
        foreach($producersStats as $appId => $stats) {
            $out .= sprintf($mask
                , $appId
                , $stats['pid']
                , $stats['created']
                , $stats['state']
                , $stats['m_usage']
                , $stats['mp_usage']
                , time() - $stats['created']
                , time() - $stats['circle']
                , $stats['pid_exists']
            );

    //        $stats['pid_exists'] = $process_exists;
    //        $stats['pid_exists_msg'] = ($process_exists) ? 'exists and run' : 'NOT exists';

        }
    }
    // ===============================================================================
    // ===============================================================================
//
//    /** @var \Cronario\RepositoryInterface $repository */
//    $repository = \Cronario\Producer::getRepository('repository');
//    $jobSet = $repository::getAll();
//    $out .= PHP_EOL. 'database job Job: ' . $repository::count() . PHP_EOL. PHP_EOL;
//
//    if(is_array($jobSet)) {
//        foreach ($jobSet as $i => $job) {
//            // $out .= print_r($params['comment'] , true);
//
//            /** @var \Cronario\AbstractJob $job */
//
//            $jobId = $job->getId();
//            $status = $job->getStatus();
//            $author = $job->getAuthor() ?: 'undefined-author';
//            $comment = $job->getComment() ?: 'undefined-comment';
//
//            /** @var \Cronario\Exception\ResultException $resultException */
//            $resultException = $job->getResult();
//            $result = $resultException ? json_encode([
//                $resultException->getMessage(),
//                $resultException->getCode() ,
//                $resultException->getData()
//            ]) : 'null';
//
//            // $param = json_encode($fields['param']);
//            // $allData = json_encode($fields);
//
//            $out .= "    {$jobId} : {$comment}";
//            $out .= " / s: {$status}";
//            $out .= " / a: {$author}";
//            $out .= " / r: {$result}";
//            // $out .= " / param {$param}";
//            // $out .= " / allData {$allData}";
//
//            $out .= PHP_EOL;
//        }
//    }

    // $out .= "____________________________________________________________________________________________".PHP_EOL;

    // ===============================================================================
    // ===============================================================================

    $queuesStats = \Cronario\Facade::getQueuesStats();

    if(is_array($queuesStats)) {
        foreach($queuesStats as $appId => $stats) {
            $out .= PHP_EOL. "Queues : \"{$appId}\"" . PHP_EOL;
            if(is_array($stats) && is_array($stats['queues'])){
                foreach($stats['queues'] as $queueName => $fields){
                    $out .= "        - {$queueName} : ";
                    $out .= " total {$fields['jobs-total']}";
                    $out .= " ready {$fields['jobs-ready']}";
                    $out .= " buried {$fields['jobs-buried']}";
                    $out .= " reserved {$fields['jobs-reserved']}";
                    $out .= " delayed {$fields['jobs-delayed']}";
                    $out .= PHP_EOL;
                }
            }
        }
    }

    $out .= "____________________________________________________________________________________________".PHP_EOL;

    // ===============================================================================
    // ===============================================================================

    $jobsReserved = \Cronario\Facade::getJobsReserved();
    if(is_array($jobsReserved)){
        foreach($jobsReserved as $appId => $jobsPayload){
            $out .= PHP_EOL. "jobs Reserved : \"{$appId}\"" . PHP_EOL;
            foreach($jobsPayload as $jobId => $queueName){
                $out .= " {$queueName} : {$jobId}" . PHP_EOL;
            }
        }
    }

    $out .= "____________________________________________________________________________________________".PHP_EOL;

    // ===============================================================================
    // ===============================================================================
    $managersStats = \Cronario\Facade::getManagersStats();
    if(is_array($managersStats)) {
        foreach($managersStats as $appId => $stats) {
            $out .= PHP_EOL. "Managers : \"{$appId}\"" . PHP_EOL;

            if(is_array($stats['stat']) && count($stats['stat']) > 0){
                foreach($stats['stat'] as $i => $fields){
                    $out .= " stat : {$fields['workerClass']} : ";
                    $out .= " fail {$fields[\Cronario\Manager::EVENT_FAIL]} / ";
                    $out .= " success {$fields[\Cronario\Manager::EVENT_SUCCESS]} / ";
                    $out .= " error {$fields[\Cronario\Manager::EVENT_ERROR]} / ";
                    $out .= " retry {$fields[\Cronario\Manager::EVENT_RETRY]} / ";
                    $out .= " redirect {$fields[\Cronario\Manager::EVENT_REDIRECT]}";
                    $out .= PHP_EOL;
                }
            }

            if(is_array($stats['live']) && count($stats['live']) > 0){
                foreach($stats['live'] as $i => $fields){
                    $out .= " live : {$fields['workerClass']} : ";
                    $out .= " fail {$fields[\Cronario\Manager::EVENT_FAIL]} / ";
                    $out .= " success {$fields[\Cronario\Manager::EVENT_SUCCESS]} / ";
                    $out .= " error {$fields[\Cronario\Manager::EVENT_ERROR]} / ";
                    $out .= " retry {$fields[\Cronario\Manager::EVENT_RETRY]} / ";
                    $out .= " redirect {$fields[\Cronario\Manager::EVENT_REDIRECT]}";
                    $out .= PHP_EOL;
                }
            }

        }
    }

    $out .= "____________________________________________________________________________________________".PHP_EOL;

    // ===============================================================================
    // ===============================================================================


//
//    $redis = \Cronario\Producer::getInstance()->getRedis();
//    $keys = $redis->keys('*');
//    $out .= PHP_EOL. 'Redis keys' . PHP_EOL;
//    foreach($keys as $key ){
//        $out .= " - {$key}".PHP_EOL;
//    }
//    $out .= "____________________________________________________________________________________________".PHP_EOL;

    // ===============================================================================
    // ===============================================================================


    system('clear');
    echo $out;
    usleep(100000);
}
