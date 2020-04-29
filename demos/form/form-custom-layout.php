<?php

declare(strict_types=1);

require_once __DIR__ . '/../atk-init.php';

// Demonstrates how to use fields with form.
\atk4\ui\Header::addTo($app, ['Custom Form Layout']);

$form = \atk4\ui\Form::addTo($app, ['layout' => ['Custom', 'defaultTemplate' => __DIR__ . '/form-custom-layout.html']]);
$form->setModel(new CountryLock($db))->loadAny();

$form->onSubmit(function ($f) {
    return new \atk4\ui\jsToast('Saving is disabled');
});
