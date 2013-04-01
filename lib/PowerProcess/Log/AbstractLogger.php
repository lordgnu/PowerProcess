<?php

namespace BauerBox\PowerProcess\Log;

use Psr\Log\AbstractLogger as BaseAbstractLogger;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

abstract class AbstractLogger extends BaseAbstractLogger implements LoggerInterface
{
    public function alert($message, array $context = array())
    {
        return $this->handleMessage(LogLevel::ALERT, $message, $context);
    }

    public function critical($message, array $context = array())
    {
        return $this->handleMessage(LogLevel::CRITICAL, $message, $context);
    }

    public function debug($message, array $context = array())
    {
        return $this->handleMessage(LogLevel::DEBUG, $message, $context);
    }

    public function emergency($message, array $context = array())
    {
        return $this->handleMessage(LogLevel::EMERGENCY, $message, $context);
    }

    public function error($message, array $context = array())
    {
        return $this->handleMessage(LogLevel::ERROR, $message, $context);
    }

    public function info($message, array $context = array())
    {
        return $this->handleMessage(LogLevel::INFO, $message, $context);
    }

    public function log($level, $message, array $context = array())
    {
        $this->validateLogLevel($level);
        return $this->handleMessage($level, $message, $context);
    }

    public function notice($message, array $context = array())
    {
        return $this->handleMessage(LogLevel::NOTICE, $message, $context);
    }

    public function warning($message, array $context = array())
    {
        return $this->handleMessage(LogLevel::WARNING, $message, $context);
    }

    abstract public function setJobName($jobName);

    abstract protected function handleMessage($level, $message, $context = array());

    protected function interpolate($message, array $context = array())
    {
        // build a replacement array with braces around the context keys
        $replace = array();
        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }

    protected function validateLogLevel($level)
    {
        switch ($level) {
            case LogLevel::ALERT:
            case LogLevel::CRITICAL:
            case LogLevel::DEBUG:
            case LogLevel::EMERGENCY:
            case LogLevel::ERROR:
            case LogLevel::INFO:
            case LogLevel::NOTICE:
            case LogLevel::WARNING:
                return true;
        }

        throw new InvalidArgumentException('Invalid log log level: ' . $level);
    }
}
