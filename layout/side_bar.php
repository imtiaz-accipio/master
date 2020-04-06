<?php

defined('MOODLE_INTERNAL') || die();

global $OUTPUT;

$side_pre_blocks = $OUTPUT->blocks('side-pre');
$has_blocks= strpos($side_pre_blocks, 'data-block=') !== false;

$templatecontext['side_pre_blocks'] = $side_pre_blocks;
$templatecontext['has_blocks'] = $has_blocks;
