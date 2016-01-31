<?php

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers(array(
	'-psr0',
        'ordered_use',
        'concat_with_spaces',
        'header_comment',
        'newline_after_open_tag',
        'phpdoc_order',
	'align_double_arrow',
	'align_equals'
    ))
    ->finder(
        Symfony\CS\Finder\DefaultFinder::create()
            ->exclude('vendor')
	    ->name('*.php')
            ->in(__DIR__.'/upload')
    )
;
