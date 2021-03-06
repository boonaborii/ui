<?php

namespace atk4\ui;

/**
 * Virtual page normally does not render, yet it has it's own trigger and will respond
 * to the trigger in a number of useful way depending on trigger's argument:.
 *
 *  - cut = will only output HTML of this VirtualPage and it's sub-elements
 *  - popup = will add VirtualPage directly into body, ideal for pop-up windows
 *  - normal = will get rid of all the normal components inside Layout's content replacing them
 *      the render of this page. Will preserve menus and top bar but that's it.
 */
class VirtualPage extends View
{
    /** @var callback */
    public $cb;

    /** @var callable Optional callback function of virtual page */
    public $fx;

    /** @var string specify custom callback trigger for the URL (see Callback::$urlTrigger) */
    public $urlTrigger;

    /** @var string UI container class */
    public $ui = 'container';

    /**
     * Initialization.
     */
    public function init(): void
    {
        parent::init();

        $this->cb = $this->_add([Callback::class, 'urlTrigger' => $this->urlTrigger ?: $this->name]);
        $this->app->stickyGet($this->cb->urlTrigger);
    }

    /**
     * Set callback function of virtual page.
     *
     * Note that only one callback function can be defined.
     *
     * @param array $fx   Need this to be defined as array otherwise we get warning in PHP7
     * @param mixed $junk
     *
     * @return $this
     */
    public function set($fx = [], $junk = null)
    {
        if (!$fx) {
            return $this;
        }

        if ($this->fx) {
            throw new Exception([
                'Callback for this Virtual Page is already defined',
                'vp' => $this,
                'old_fx' => $this->fx,
                'new_fx' => $fx,
            ]);
        }
        $this->fx = $fx;

        return $this;
    }

    /**
     * Is virtual page active?
     *
     * @return bool
     */
    public function triggered()
    {
        return $this->cb->triggered();
    }

    /**
     * Returns URL which you can load directly in the browser location, open in a new tab,
     * new window or inside iframe. This URL will contain HTML for a new page.
     *
     * @param string $mode
     *
     * @return string
     */
    public function getURL($mode = 'callback')
    {
        return $this->cb->getURL($mode);
    }

    /**
     * Return URL that is designed to be loaded from inside JavaScript and contain JSON code.
     * This is useful for dynamically loaded Modal, Tab or Loader.
     *
     * @param string $mode
     *
     * @return string
     */
    public function getJSURL($mode = 'callback')
    {
        return $this->cb->getJSURL($mode);
    }

    /**
     * VirtualPage is not rendered normally. It's invisible. Only when
     * it is triggered, it will exclusively output it's content.
     */
    public function getHTML()
    {
        $this->cb->set(function () {
            // if virtual page callback is triggered
            if ($type = $this->cb->triggered()) {
                // process callback
                if ($this->fx) {
                    call_user_func($this->fx, $this);
                }

                // special treatment for popup
                if ($type === 'popup') {
                    $this->app->html->template->set('title', $this->app->title);
                    $this->app->html->template->setHTML('Content', parent::getHTML());
                    $this->app->html->template->appendHTML('HEAD', $this->getJS());

                    $this->app->terminateHTML($this->app->html->template);
                }

                // render and terminate
                if (isset($_GET['__atk_json'])) {
                    $this->app->terminateJSON($this);
                }

                if (isset($_GET['__atk_tab'])) {
                    $this->app->terminateHTML($this->renderTab());
                }

                // do not terminate if callback supplied (no cutting)
                if ($type !== 'callback') {
                    $this->app->terminateHTML($this);
                }
            }

            // Remove all elements from inside the Content
            foreach ($this->app->layout->elements as $key => $view) {
                if ($view instanceof View && $view->region === 'Content') {
                    unset($this->app->layout->elements[$key]);
                }
            }

            // Prepare modals in order to include them in VirtualPage.
            $modalHtml = '';
            foreach ($this->app->html !== null ? $this->app->html->elements : [] as $view) {
                if ($view instanceof Modal) {
                    $modalHtml .= $view->getHTML();
                    $this->app->layout->_js_actions = array_merge($this->app->layout->_js_actions, $view->_js_actions);
                }
            }

            $this->app->layout->template->setHTML('Content', parent::getHTML());
            $this->app->layout->_js_actions = array_merge($this->app->layout->_js_actions, $this->_js_actions);

            $this->app->html->template->setHTML('Content', $this->app->layout->getHTML());
            $this->app->html->template->setHTML('Modals', $modalHtml);

            $this->app->html->template->appendHTML('HEAD', $this->app->layout->getJS());

            $this->app->terminateHTML($this->app->html->template);
        });
    }
}
