<?php

/**
 * This file is a part of the PowerProcess package for PHP by BauerBox Labs
 *
 * @copyright
 * Copyright (c) 2012 Don Bauer <lordgnu@me.com> BauerBox Labs
 *
 * @license https://github.com/lordgnu/PowerProcess/blob/master/LICENSE MIT License
 */

namespace BauerBox\PowerProcess\Posix;

/**
 * Utility class for friendly POSIX signal names
 *
 * @author Don Bauer <lordgnu@me.com>
 */
class Signals
{
    private static $alias;
    private static $canSignal;
    private static $contantsInstalled = false;
    private static $signalArray = array(
        'SIGHUP'    =>  1,
        'SIGINT'    =>  2,
        'SIGQUIT'   =>  3,
        'SIGILL'    =>  4,
        'SIGTRAP'   =>  5,
        'SIGABRT'   =>  6,
        'SIGIOT'    =>  6,
        'SIGBUS'    =>  7,
        'SIGFPE'    =>  8,
        'SIGKILL'   =>  9,
        'SIGUSR1'   =>  10,
        'SIGSEGV'   =>  11,
        'SIGUSR2'   =>  12,
        'SIGPIPE'   =>  13,
        'SIGALRM'   =>  14,
        'SIGTERM'   =>  15,
        'SIGSTKFLT' =>  16,
        'SIGCLD'    =>  17,
        'SIGCHLD'   =>  17,
        'SIGCONT'   =>  18,
        'SIGSTOP'   =>  19,
        'SIGTSTP'   =>  20,
        'SIGTTIN'   =>  21,
        'SIGTTOU'   =>  22,
        'SIGURG'    =>  23,
        'SIGXCPU'   =>  24,
        'SIGXFSZ'   =>  25,
        'SIGVTALRM' =>  26,
        'SIGPROF'   =>  27,
        'SIGWINCH'  =>  28,
        'SIGPOLL'   =>  29,
        'SIGIO'     =>  29,
        'SIGPWR'    =>  30,
        'SIGSYS'    =>  31,
        'SIGBABY'   =>  31
    );

    public static function installConstants()
    {
        if (false === static::$contantsInstalled) {
            foreach (static::$signalArray as $signalName => $signalValue) {
                if (false === defined($signalName)) {
                    define($signalName, $signalValue);
                }
            }

            static::$contantsInstalled = true;
        }
    }

    public static function isValidSignal($signal)
    {
        static::installConstants();

        switch ($signal) {
            case SIGHUP:
            case SIGINT:
            case SIGQUIT:
            case SIGILL:
            case SIGTRAP:
            case SIGABRT:
            case SIGIOT:
            case SIGBUS:
            case SIGFPE:
            case SIGUSR1:
            case SIGSEGV:
            case SIGUSR2:
            case SIGPIPE:
            case SIGALRM:
            case SIGTERM:
            case SIGSTKFLT:
            case SIGCLD:
            case SIGCHLD:
            case SIGCONT:
            case SIGTSTP:
            case SIGTTIN:
            case SIGTTOU:
            case SIGURG:
            case SIGXCPU:
            case SIGXFSZ:
            case SIGVTALRM:
            case SIGPROF:
            case SIGWINCH:
            case SIGPOLL:
            case SIGIO:
            case SIGPWR:
            case SIGSYS:
            case SIGBABY:
                return true;
        }

        return (is_array(static::$alias) && array_key_exists($signal, static::$alias));
    }

    public static function sendSignal($signal, $processId, &$errorMessage = false)
    {
        if (null === static::$canSignal) {
            $extentions = get_loaded_extensions();
            static::$canSignal = (in_array('posix', $extentions) && in_array('pcntl', $extentions));
            unset($extentions);
        }

        if (true === self::$canSignal) {
            if (true === static::isValidSignal($signal)) {
                // Queue the signal
                posix_kill($processId, $signal);

                // Dispatch
                return pcntl_signal_dispatch();
            }

            $errorMessage = 'Invalid signal: ' . $signal;
        } else {
            $errorMessage = 'This system is unable to send POSIX signals from PHP';
        }

        return false;
    }

    /**
     * Register an alias for a signal to be returned when signalName() is used
     *
     * Setting the $alias parameter to null will remove the alias if it is set
     *
     * @param int $signal
     * @param string|null $alias
     */
    public static function setSignalAlias($signal, $alias)
    {
        if (static::$alias === null) {
            static::$alias = array();
        }

        if (null === $alias) {
            if (true === array_key_exists($signal, static::$alias)) {
                static::$alias[$signal] = null;
                unset(self::$alias[$signal]);
            }

            return;
        }

        static::$alias[$signal] = "{$alias}";
    }

    /**
     * Returns the signal string for a signal number or the registered alias
     *
     * @param int $signal
     * @return string
     */
    public static function signalName($signal)
    {
        static::installConstants();

        if (null !== static::$alias) {
            if (true === array_key_exists($signal, static::$alias)) {
                return static::$alias[$signal];
            }
        }

        switch ($signal) {
            case SIGHUP:
                return 'SIGHUP';
            case SIGINT:
                return 'SIGINT';
            case SIGQUIT:
                return 'SIGQUIT';
            case SIGILL:
                return 'SIGILL';
            case SIGTRAP:
                return 'SIGTRAP';
            case SIGABRT:
                return 'SIGABRT';
            case SIGIOT:
                return 'SIGIOT';
            case SIGBUS:
                return 'SIGBUS';
            case SIGFPE:
                return 'SIGFPE';
            case SIGUSR1:
                return 'SIGUSR1';
            case SIGSEGV:
                return 'SIGSEGV';
            case SIGUSR2:
                return 'SIGUSR2';
            case SIGPIPE:
                return 'SIGPIPE';
            case SIGALRM:
                return 'SIGALRM';
            case SIGTERM:
                return 'SIGTERM';
            case 16:
                return 'SIGSTKFLT';
            case SIGCHLD:
                return 'SIGCHLD';
            case SIGCONT:
                return 'SIGCONT';
            case SIGTSTP:
                return 'SIGTSTP';
            case SIGTTIN:
                return 'SIGTTIN';
            case SIGTTOU:
                return 'SIGTTOU';
            case SIGURG:
                return 'SIGURG';
            case SIGXCPU:
                return 'SIGXCPU';
            case SIGXFSZ:
                return 'SIGXFSZ';
            case SIGVTALRM:
                return 'SIGVTALRM';
            case SIGPROF:
                return 'SIGPROF';
            case SIGWINCH:
                return 'SIGWINCH';
            case SIGPOLL:
                return 'SIGPOLL';
            case SIGIO:
                return 'SIGIO';
            case SIGPWR:
                return 'SIGPWR';
            case SIGSYS:
                return 'SIGSYS';
            case SIGBABY:
                return 'SIGBABY';
            default:
                return "Unknown Signal (#{$signal})";
        }
    }

    public static function signalNumber($signalName)
    {
        static::installConstants();

        if (null !== static::$alias) {
            if (true === in_array($signalName, static::$alias)) {
                return static::$alias[array_search($signalName, static::$alias)];
            }
        }

        switch ($signalName) {
            case 'SIGHUP':
                return SIGHUP;
            case 'SIGINT':
                return SIGINT;
            case 'SIGQUIT':
                return SIGQUIT;
            case 'SIGILL':
                return SIGILL;
            case 'SIGTRAP':
                return SIGTRAP;
            case 'SIGABRT':
                return SIGABRT;
            case 'SIGIOT':
                return SIGIOT;
            case 'SIGBUS':
                return SIGBUS;
            case 'SIGFPE':
                return SIGFPE;
            case 'SIGUSR1':
                return SIGUSR1;
            case 'SIGSEGV':
                return SIGSEGV;
            case 'SIGUSR2':
                return SIGUSR2;
            case 'SIGPIPE':
                return SIGPIPE;
            case 'SIGALRM':
                return SIGALRM;
            case 'SIGTERM':
                return SIGTERM;
            case 'SIGSTKFLT':
                return 16;
            case 'SIGCHLD':
                return SIGCHLD;
            case 'SIGCONT':
                return SIGCONT;
            case 'SIGTSTP':
                return SIGTSTP;
            case 'SIGTTIN':
                return SIGTTIN;
            case 'SIGTTOU':
                return SIGTTOU;
            case 'SIGURG':
                return SIGURG;
            case 'SIGXCPU':
                return SIGXCPU;
            case 'SIGXFSZ':
                return SIGXFSZ;
            case 'SIGVTALRM':
                return SIGVTALRM;
            case 'SIGPROF':
                return SIGPROF;
            case 'SIGWINCH':
                return SIGWINCH;
            case 'SIGPOLL':
                return SIGPOLL;
            case 'SIGIO':
                return SIGIO;
            case 'SIGPWR':
                return SIGPWR;
            case 'SIGSYS':
                return SIGSYS;
            case 'SIGBABY':
                return SIGBABY;
            default:
                return -1;
        }
    }
}
