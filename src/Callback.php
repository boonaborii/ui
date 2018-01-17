<?php

namespace atk4\ui;

use atk4\core\AppScopeTrait;
use atk4\core\DIContainerTrait;
use atk4\core\InitializerTrait;
use atk4\core\TrackableTrait;

/**
 * Add this object to your render tree and it will expose a unique URL which, when
 * executed directly will perform a PHP callback that you set().
 *
 * $button = $layout->add('Button');
 * $button->set('Click to do something')->link(
 *      $button
 *          ->add('Callback')
 *          ->set(function(){
 *              do_something();
 *          })
 *          ->getURL()
 *  );
 */
class Callback
{
    use TrackableTrait;
    use AppScopeTrait;
    use DIContainerTrait;
    use InitializerTrait {
        init as _init;
    }

    /**
     * Will look for trigger in the POST data. Will re-use existing URL, but
     * $_POST[$this->name] will have to be set.
     *
     * @var bool
     */
    public $POST_trigger = false;

    /**
     * Contains either false if callback wasn't triggered or the value passed
     * as an argument to a call-back.
     *
     * e.g. following URL of getURL('test') will result in $triggered = 'test';
     *
     * @var string|false
     */
    public $triggered = false;

    /**
     * Specify a custom GET trigger here.
     */
    public $urlTrigger = null;

    /**
     * Initialize object and set default properties.
     *
     * @param array|string $defaults
     */
    public function __construct($defaults = [])
    {
        $this->setDefaults($defaults);
    }

    /**
     * Initialization.
     */
    public function init()
    {
        $this->_init();

        if (!$this->urlTrigger) {
            $this->urlTrigger = $this->name;
        }

        if (!$this->app) {
            throw new Exception(['Call-back must be part of a RenderTree']);
        }
    }

    /**
     * Executes user-specified action when call-back is triggered.
     *
     * @param callback $callback
     * @param array    $args
     *
     * @return mixed|null
     */
    public function set($callback, $args = [])
    {
        $this->owner->stickyGet($this->urlTrigger);

        if ($this->POST_trigger) {
            if (isset($_POST[$this->name])) {
                $this->triggered = $_POST[$this->urlTrigger];

                $t = $this->app->run_called;
                $this->app->run_called = true;
                $ret = call_user_func_array($callback, $args);
                $this->app->run_called = $t;

                return $ret;
            }
        } else {
            if (isset($_GET[$this->urlTrigger])) {
                $this->triggered = $_GET[$this->urlTrigger];

                $t = $this->app->run_called;
                $this->app->run_called = true;
                $this->owner->stickyGet($this->urlTrigger);
                $ret = call_user_func_array($callback, $args);
                //$this->app->stickyForget($this->name);
                $this->app->run_called = $t;

                return $ret;
            }
        }
    }

    /**
     * Is callback triggered?
     *
     * @return bool
     */
    public function triggered()
    {
        return isset($_GET[$this->urlTrigger]) ? $_GET[$this->urlTrigger] : false;
    }

    /**
     * Return URL that will trigger action on this call-back.
     *
     * @param string $mode
     *
     * @return string
     */
    public function getURL($mode = 'callback')
    {
        return $this->owner->url([$this->urlTrigger => $mode, '__atk_callback'=>1], $this->POST_trigger);
    }
}
