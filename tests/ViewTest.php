<?php

declare(strict_types=1);

namespace atk4\ui\tests;

use atk4\core\AtkPhpunit;
use atk4\core\Exception;

class ViewTest extends AtkPhpunit\TestCase
{
    /**
     * Test redering multiple times.
     */
    public function testMultipleRender()
    {
        $v = new \atk4\ui\View();
        $v->set('foo');

        $a = $v->render();
        $b = $v->render();
        $this->assertSame($a, $b);
    }

    public function testAddAfterRender()
    {
        $this->expectException(Exception::class);

        $v = new \atk4\ui\View();
        $v->set('foo');

        $a = $v->render();
        \atk4\ui\View::addTo($v);  // this should fail. No adding after rendering.
        $b = $v->render();
        $this->assertSame($a, $b);
    }
}
