<?php

defined('MOODLE_INTERNAL') || die();
global $OUTPUT, $PAGE;

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

$output = $this->page->get_renderer('block_google_cse');
$this->page->requires->js_call_amd('block_google_cse/searchbar', 'init');
$data = new \block_google_cse\output\searchbar(new moodle_url("/blocks/google_cse/search.php"));

function metaChecker(){
    global $PAGE;
    $get_meta_function_return = \theme_master\output\core_renderer::get_meta('meta');
    $empty_meta_key_words = '<meta name="keywords" content="">';


    //Check if get_meta() has opening quoation " for content (so contains content)
    // if it does pass normal get_meta()
    if(strpos($get_meta_function_return, 'keywords content="')){
        $item = $get_meta_function_return;
        }

    // Check if get_meta() has no '<meta name="keywords" content=""> element,
    // if no meta element, create and pass meta keyword element
    else if(strpos($get_meta_function_return, '<meta name="keywords" content="') === false){
        $item = $get_meta_function_return . '<meta name="keywords" content="'. $PAGE->title . '">';
    }

    // some get_meta() have '<meta name="keywords" content> element with no content
    //replace the empty meta element with content
    else {
         $item = str_replace($empty_meta_key_words, '<meta name="keywords" content="'. $PAGE->title . '">', $get_meta_function_return );
        }
    return $item;
}



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
    'has_bottom_right'=>$has_bottom_right,

//    'canonical' => \theme_master\output\core_renderer::get_meta('cronical'),
//    'metatag' => metaChecker(),
    'metatag' =>\theme_master\output\core_renderer::get_a1_rank(),

    'searchbar' => $output->render($data)

];