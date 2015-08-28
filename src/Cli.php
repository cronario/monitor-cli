<?php

namespace Monitor;

class Cli
{

    // region INIT *************************************************

    const OPTIONS_SHOW_PROGRESS = 'showProgress';
    const OPTIONS_SHOW_PRODUCERS = 'showProducer';
    const OPTIONS_SHOW_QUEUES = 'showQueues';
    const OPTIONS_SHOW_JOBS_RESERVED = 'showJobsReserved';
    const OPTIONS_SHOW_MANAGERS = 'showManagers';
    const OPTIONS_SLEEP_TIME = 'sleepTime'; // in seconds
    const OPTIONS_AUTO_START = 'autoStart';

    protected $options
        = [
            self::OPTIONS_SHOW_PROGRESS      => true,
            self::OPTIONS_SHOW_PRODUCERS     => true,
            self::OPTIONS_SHOW_QUEUES        => true,
            self::OPTIONS_SHOW_JOBS_RESERVED => true,
            self::OPTIONS_SHOW_MANAGERS      => true,
            self::OPTIONS_SLEEP_TIME         => 1,
            self::OPTIONS_AUTO_START         => false,
        ];

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->options, $options);

        if ($this->options[self::OPTIONS_AUTO_START]) {
            $this->start();
        }
    }

    // endregion *************************************************

    // region SEGMENTS *************************************************

    protected $progress;

    /**
     * @return string
     */
    protected function getProgress()
    {
        $output = '';
        if (!$this->options[self::OPTIONS_SHOW_PROGRESS]) {
            return $output;
        }

        $this->progress = microtime(true);

        $output .= 'progress : ' . $this->progress;

        return $output;
    }

    /**
     * @return string
     */
    protected function getProducerStats()
    {
        $output = '';
        if (!$this->options[self::OPTIONS_SHOW_PRODUCERS]) {
            return $output;
        }

        $producersStats = \Cronario\Facade::getProducersStats();
        if (!is_array($producersStats)) {

        }

        $mask = PHP_EOL;
        $mask .= 'Daemon "%s" : ' . PHP_EOL;
        $mask .= "         pid %15.5s   |   created %5.10s   |   state %20.5s" . PHP_EOL;
        $mask .= "         m_usage %11.7s   |   mp_usage %9.7s   |   life %21.5s " . PHP_EOL;
        $mask .= "         last circle %7.7s   |   pid_exists %7.7s " . PHP_EOL;
        $mask .= '___________________________________________________________________________________________';
        $mask .= PHP_EOL;

        foreach ($producersStats as $appId => $stats) {
            $output .= sprintf($mask
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


        $output .= ' ... ';

        return $output;
    }

    /**
     * @return string
     */
    protected function getQueuesStats()
    {
        $output = '';
        if (!$this->options[self::OPTIONS_SHOW_QUEUES]) {
            return $output;
        }

        $queuesStats = \Cronario\Facade::getQueuesStats();

        if (!is_array($queuesStats)) {
            return $output;
        }

        foreach ($queuesStats as $appId => $stats) {
            $output .= PHP_EOL . "Queues : \"{$appId}\"" . PHP_EOL;
            if (is_array($stats) && is_array($stats['queues'])) {
                foreach ($stats['queues'] as $queueName => $fields) {
                    $output .= "        - {$queueName} : ";
                    $output .= " total {$fields['jobs-total']}";
                    $output .= " ready {$fields['jobs-ready']}";
                    $output .= " buried {$fields['jobs-buried']}";
                    $output .= " reserved {$fields['jobs-reserved']}";
                    $output .= " delayed {$fields['jobs-delayed']}";
                    $output .= PHP_EOL;
                }
            }
        }

        return $output;
    }

    /**
     * @return string
     */
    protected function getJobsReserved()
    {
        $output = '';
        if (!$this->options[self::OPTIONS_SHOW_JOBS_RESERVED]) {
            return $output;
        }

        $jobsReserved = \Cronario\Facade::getJobsReserved();

        if (!is_array($jobsReserved)) {
            return $output;
        }

        foreach ($jobsReserved as $appId => $jobsPayload) {
            $output .= PHP_EOL . "jobs Reserved : \"{$appId}\"" . PHP_EOL;
            foreach ($jobsPayload as $jobId => $queueName) {
                $output .= " {$queueName} : {$jobId}" . PHP_EOL;
            }
        }

        return $output;
    }

    /**
     * @return string
     */
    protected function getManagersStats()
    {
        $output = '';
        if (!$this->options[self::OPTIONS_SHOW_MANAGERS]) {
            return $output;
        }

        $managersStats = \Cronario\Facade::getManagersStats();
        if (is_array($managersStats)) {

        }
        foreach ($managersStats as $appId => $stats) {
            $output .= PHP_EOL . "Managers : \"{$appId}\"" . PHP_EOL;

            if (is_array($stats['stat']) && count($stats['stat']) > 0) {
                foreach ($stats['stat'] as $i => $fields) {
                    $output .= " stat : {$fields['workerClass']} : ";
                    $output .= " fail {$fields[\Cronario\Manager::EVENT_FAIL]} / ";
                    $output .= " success {$fields[\Cronario\Manager::EVENT_SUCCESS]} / ";
                    $output .= " error {$fields[\Cronario\Manager::EVENT_ERROR]} / ";
                    $output .= " retry {$fields[\Cronario\Manager::EVENT_RETRY]} / ";
                    $output .= " redirect {$fields[\Cronario\Manager::EVENT_REDIRECT]}";
                    $output .= PHP_EOL;
                }
            }

            if (is_array($stats['live']) && count($stats['live']) > 0) {
                foreach ($stats['live'] as $i => $fields) {
                    $output .= " live : {$fields['workerClass']} : ";
                    $output .= " fail {$fields[\Cronario\Manager::EVENT_FAIL]} / ";
                    $output .= " success {$fields[\Cronario\Manager::EVENT_SUCCESS]} / ";
                    $output .= " error {$fields[\Cronario\Manager::EVENT_ERROR]} / ";
                    $output .= " retry {$fields[\Cronario\Manager::EVENT_RETRY]} / ";
                    $output .= " redirect {$fields[\Cronario\Manager::EVENT_REDIRECT]}";
                    $output .= PHP_EOL;
                }
            }

        }

        return $output;
    }

    // endregion *************************************************

    /**
     * Main loop
     */
    public function start()
    {
        $loopSleep = $this->options[self::OPTIONS_SLEEP_TIME] * 1000000;
        $finishLoop = false;

        while (!$finishLoop) {
            $output = '';

            try {
                $output .= $this->getProgress();
                $output .= $this->getProducerStats();
                $output .= $this->getQueuesStats();
                $output .= $this->getJobsReserved();
                $output .= $this->getManagersStats();
            } catch (\Exception $ex) {
                $finishLoop = true;
                $output .= 'We have Exception : ' . PHP_EOL;
                $output .= print_r($ex, true) . PHP_EOL;
                $output .= 'Try fix it ... : ' . PHP_EOL;
            }

            system('clear');
            echo $output;
            usleep($loopSleep);

            if ($finishLoop) {
                break;
            }
        }

    }

}