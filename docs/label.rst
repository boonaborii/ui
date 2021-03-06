

.. _label:

=====
Label
=====

.. php:namespace:: atk4\ui

.. php:class:: Label

Labels can be used in many different cases, either as a stand-alone objects, inside tables or inside
other components.

To see what possible classes you can use on the Label, see: https://fomantic-ui.com/elements/label.html.

Demo: https://ui.agiletoolkit.org/demos/label.php

Basic Usage
===========

First argument of constructor or first element in array passed to constructor will be the text that will
appear on the label::

    $label = Label::addTo($app, ['hello world']);

    // or

    $label = new \atk4\ui\Label('hello world');
    $app->add($label);


Label has the following properties:

.. php:attr:: icon

.. php:attr:: iconRight

.. php:attr:: image

.. php:attr:: imageRight

.. php:attr:: detail

All the above can be string, array (passed to Icon, Image or View class) or an object.

Icons
=====

There are two properties (icon, iconRight) but you can set only one at a time::

    Label::addTo($app, ['23', 'icon'=>'mail']);
    Label::addTo($app, ['new', 'iconRight'=>'delete']);

You can also specify icon as an object::

    Label::addTo($app, ['new', 'iconRight'=>new \atk4\ui\Icon('delete')]);

For more information, see: :php:class:`Icon`

Image
=====

Image cannot be specified at the same time with the icon, but you can use PNG/GIF/JPG image on your label::

    $img = 'https://raw.githubusercontent.com/atk4/ui/2.0.4/public/logo.png';
    Label::addTo($app, ['Coded in PHP', 'image'=>$img]);

Detail
======

You can specify "detail" component to your label::

    Label::addTo($app, ['Number of lines', 'detail'=>'33']);

Groups
======

Label can be part of the group, but you would need to either use custom HTML template or
composition::

    $group = View::addTo($app, [false, 'blue tag', 'ui'=>'labels']);
    Label::addTo($group, ['$9.99']);
    Label::addTo($group, ['$19.99', 'red tag']);
    Label::addTo($group, ['$24.99']);

Combining classes
=================

Based on Fomantic UI documentation, you can add more classes to your labels::

    $columns = Columns::addTo($app);

    $c = $columns->addColumn();
    $col = View::addTo($c, ['ui'=>'raised segment']);

    // attach label to the top of left column
    Label::addTo($col, ['Left Column', 'top attached', 'icon'=>'book']);

    // ribbon around left column
    Label::addTo($col, ['Lorem', 'red ribbon', 'icon'=>'cut']);

    // add some content inside column
    LoremIpsum::addTo($col, ['size'=>1]);

    $c = $columns->addColumn();
    $col = View::addTo($c, ['ui'=>'raised segment']);

    // attach label to the top of right column
    Label::addTo($col, ['Right Column', 'top attached', 'icon'=>'book']);

    // some content
    LoremIpsum::addTo($col, ['size'=>1]);

    // right bottom corner label
    Label::addTo($col, ['Ipsum', 'orange bottom right attached', 'icon'=>'cut']);

Added labels into Table
=======================

You can even use label inside a table, but because table renders itself by repeating periodically, then
the following code is needed::

    $table->onHook('getHTMLTags', function ($table, Model $row) {
        if ($row->id == 1) {
            return [
                'name'=> $table->app->getTag('div', ['class'=>'ui ribbon label'], $row->get('name')),
            ];
        }
    });

Now while $table will be rendered, if it finds a record with id=1, it will replace $name value with a HTML tag.
You need to make sure that 'name' column appears first on the left.

