<?php

$header = <<<EOF
This file is part of the systemctl PHP library.

(c) Martin Janser <martin@duss-janser.ch>

This source file is subject to the GPL license that is bundled
with this source code in the file LICENSE.
EOF;

Symfony\CS\Fixer\Contrib\HeaderCommentFixer::setHeader($header);

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(__DIR__)
;

return Symfony\CS\Config\Config::create()
    ->fixers([
        'header_comment',
        'newline_after_open_tag',
        'no_empty_comment',
        'no_useless_return',
        'ordered_use',
        'php_unit_construct',
        'php_unit_dedicate_assert',
        'php_unit_strict',
        'phpdoc_order',
        'short_array_syntax',
        'strict',
        'strict_param',
    ])
    ->finder($finder)
;
