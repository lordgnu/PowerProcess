<?php

/*
 * This file is part of the RevMatic package.
 *
 * (c) RevCo <http://www.gorevco.com>
 *
 * Unless otherwise specified, this source is proprietary
 * property of RevCo and is not to be redistributed in any
 * form without the expressed written permission of RevCo
 */

namespace BauerBox\PowerProcess\Log;

use BauerBox\PowerProcess\Log\AbstractLogger;

/**
 * Description of NullLogger
 *
 * @author Don Bauer <don.bauer@gorevco.com>
 */
class NullLogger extends AbstractLogger
{
    protected function handleMessage($level, $message, $context = array())
    {
        return $this;
    }

    public function setJobName($jobName)
    {
        return $this;
    }
}
