<?php

declare(strict_types=1);

namespace atk4\ui\FormField;

use atk4\ui\View;

class UploadImg extends Upload
{
    /**
     * The thumbnail view to add to this input.
     *
     * @var View|null
     */
    public $thumbnail;

    /**
     * The template region where to add the thumbnail view.
     * Default to AfterAfterInput.
     *
     * @var string
     */
    public $thumnailRegion = 'AfterAfterInput';

    /**
     * The default thumbnail source.
     *
     * @var null
     */
    public $defaultSrc;

    public function init(): void
    {
        parent::init();

        if (!$this->accept) {
            $this->accept = ['.jpg', '.jpeg', '.png'];
        }

        if (!$this->thumbnail) {
            $this->thumbnail = (new View(['element' => 'img', 'class' => ['right', 'floated', 'image'], 'ui' => true]))
                ->setAttr(['width' => '36px', 'height' => '36px']);
        }

        if ($this->defaultSrc) {
            $this->thumbnail->setAttr(['src' => $this->defaultSrc]);
        }

        $this->add($this->thumbnail, $this->thumnailRegion);
    }

    /**
     * Set the thumbnail img src value.
     *
     * @param string $src
     */
    public function setThumbnailSrc($src)
    {
        $this->thumbnail->setAttr(['src' => $src]);
        $action = $this->thumbnail->js();
        $action->attr('src', $src);
        $this->addJSAction($action);
    }

    /**
     * Clear the thumbnail src.
     * You can also supply a default thumbnail src.
     */
    public function clearThumbnail($defaultThumbnail = null)
    {
        $action = $this->thumbnail->js();
        if (isset($defaultThumbnail)) {
            $action->attr('src', $defaultThumbnail);
        } else {
            $action->removeAttr('src');
        }
        $this->addJSAction($action);
    }
}
