<?php

defined('MOODLE_INTERNAL') || die();
global $OUTPUT;

$bodyattributes = $OUTPUT->body_attributes($extraclasses);

$top_left = $OUTPUT->blocks('page-top-left');
$has_top_left= strpos($top_left, 'data-block=') !== false;

$top_center = $OUTPUT->blocks('page-pre');
$has_top_center = strpos($top_center, 'data-block=') !== false;

$top_right = $OUTPUT->blocks('page-top-right');
$has_top_right = strpos($top_right, 'data-block=') !== false;

$main_left = $OUTPUT->blocks('page-left');
$has_main_left = strpos($main_left, 'data-block=') !== false;

$main_center = $OUTPUT->blocks('center-pre');
$has_main_center = strpos($main_center, 'data-block=') !== false;

$main_right = $OUTPUT->blocks('page-right');
$has_main_right = strpos($main_right, 'data-block=') !== false;

$bottom_left = $OUTPUT->blocks('bottom-left');
$has_bottom_left = strpos($bottom_left, 'data-block=') !== false;

$bottom_center = $OUTPUT->blocks('center-post');
$has_bottom_center = strpos($bottom_center, 'data-block=') !== false;

$bottom_right = $OUTPUT->blocks('bottom-right');
$has_bottom_right = strpos($bottom_right, 'data-block=') !== false;

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'bodyattributes' => $bodyattributes,

    'top_left'=>$top_left,
    'has_top_left'=>$has_top_left,

    'top_center'=>$top_center,
    'has_top_center'=>$has_top_center,

    'top_right'=>$top_right,
    'has_top_right'=>$has_top_right,

    'main_left'=>$main_left,
    'has_main_left'=>$has_main_left,

    'main_center'=>$main_center,
    'has_main_center'=>$has_main_center,

    'main_right'=>$main_right,
    'has_main_right'=>$has_main_right,

    'bottom_left'=>$bottom_left,
    'has_bottom_left'=>$has_bottom_left,

    'bottom_center'=>$bottom_center,
    'has_bottom_center'=>$has_bottom_center,

    'bottom_right'=>$bottom_right,
    'has_bottom_right'=>$has_bottom_right
];