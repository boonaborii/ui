<?php

declare(strict_types=1);
/**
 * Add js trigger for executing an action.
 */

namespace atk4\ui\ActionExecutor;

interface jsInterface_
{
    /**
     * Return js expression that will trigger action executor.
     *
     * @return mixed
     */
    public function jsExecute(array $urlArgs);
}
