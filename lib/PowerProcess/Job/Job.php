<?php

namespace BauerBox\PowerProcess\Job;

use BauerBox\PowerProcess\Posix\Signals;

class Job
{
    public static $autoJobNamePrefix = 'JOB';
    protected static $jobCount = 0;

    protected $jobId;
    protected $jobName;
    protected $jobProcessId;
    protected $jobStart;
    protected $jobStop;

    protected $jobStatus;

    protected $terminateRequested = false;

    public function __construct($processId = null, $jobName = null)
    {
        $this->jobId = static::$jobCount;
        ++static::$jobCount;

        if (null === $jobName) {
            $this->jobName = sprintf('%s-%d', static::$autoJobNamePrefix, $this->jobId);
        } else {
            $this->jobName = $jobName;
        }

        $this->jobProcessId = $processId;
        $this->jobStart = microtime(true);
    }

    public function __toString()
    {
        return $this->getJobName();
    }

    public function getRunningTime()
    {
        if (null === $this->jobStop) {
            return (microtime(true) - $this->jobStart);
        }

        return $this->jobStop - $this->jobStart;
    }

    public function getJobId()
    {
        return $this->jobId;
    }

    public function getJobName()
    {
        return $this->jobName;
    }

    public function getJobProcessId()
    {
        return $this->jobProcessId;
    }

    public function setComplete()
    {
        $this->jobStop = microtime(true);
        return $this;
    }

    public function setProcessId($processId)
    {
        if (null === $this->jobProcessId) {
            $this->jobProcessId = $processId;
            return $this;
        }

        throw new \Exception('Can not change process ID of a job once it has been assigned!');
    }

    public function setStatus($status)
    {
        // TODO: Still need status evaluation features
        $this->jobStatus = $status;
        return $this;
    }

    public function terminate()
    {
        if (false !== $this->terminateRequested && ($this->getRunningTime() - $this->terminateRequested) > 5) {
            return Signals::sendSignal(SIGKILL, $this->getJobProcessId());
        }

        if (false === $this->terminateRequested) {
            $this->terminateRequested = $this->getRunningTime();
        }

        return Signals::sendSignal(SIGTERM, $this->getJobProcessId());
    }
}
