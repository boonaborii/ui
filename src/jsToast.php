<?php

declare(strict_types=1);

namespace atk4\ui;

use atk4\core\DIContainerTrait;

/**
 * Class jsToast
 * Generate a Fomantic-ui toast module command in js.
 *  $('body').toast({options}).
 */
class jsToast implements jsExpressionable
{
    use DIContainerTrait;

    /**
     * Various setting options as per Fomantic ui toast module.
     *
     * @var array|string
     */
    public $settings = [];

    public function __construct($settings = null)
    {
        if ($settings && is_array($settings)) {
            $this->settings = $settings;
        } elseif (is_string($settings)) {
            $this->settings['message'] = $settings;
        }
    }

    /**
     * Set message to display in Toast.
     */
    public function setMessage($msg): self
    {
        $this->settings['message'] = $msg;

        return $this;
    }

    public function jsRender()
    {
        return (new jQuery('body'))->toast($this->settings)->jsRender();
    }
}
