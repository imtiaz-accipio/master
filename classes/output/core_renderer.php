<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace theme_master\output;

use coding_exception;
use html_writer;
use tabobject;
use tabtree;
use core_text;
use custom_menu_item;
use custom_menu;
use block_contents;
use navigation_node;
use action_link;
use stdClass;
use moodle_url;
use preferences_groups;
use action_menu;
use help_icon;
use single_button;
use paging_bar;
use context_course;
use pix_icon;
use action_menu_filler;
use context_system;
use moodle_page;
use page_requirements_manager;
use core_course_list_element;

defined('MOODLE_INTERNAL') || die;

/**
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_master
 * @copyright  2012 Bas Brands, www.basbrands.nl
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class core_renderer extends \core_renderer {

    /**
     * We don't like these...
     *
     */
    public function edit_button(moodle_url $url) {
        return '';
    }

    /**
     * Wrapper for header elements.
     *
     * @return string HTML to display the main header.
     */
    public function full_header()
    {
        global $PAGE;

        $html = html_writer::start_tag('header', array('id' => 'page-header', 'class' => 'page-header'));

        $html .= html_writer::div($this->context_header(), 'header-caption container');

        $html .= $this->render_header_actions();

        $html .= html_writer::tag('div', $this->course_header(), array('id' => 'course-header'));

        $html .= html_writer::end_tag('header');

        $imageurl = $this->course_header_imageurl($PAGE->course);

        $leftcolour   = '#c2136aaa';
        $rightcolour   = '#ee4d9daa';

        $html .= "
                <style>
                    #page-header{
                        background-image: linear-gradient(to right, $leftcolour,$rightcolour), url('$imageurl');
                        height: 300px;
                        background-size: cover;
                        background-position: center;
                        display: flex;
                        margin-bottom: 2rem;
                    }
                </style>
                ";

        return $html;
    }

    public function render_context_header(\context_header $contextheader){
        global $PAGE;

        // All the html stuff goes here.
        $html = '';
        $htmltemp = ''; // prepare html based on overlay is on or off

        // moved from full_header for proper remui html structure
        $pageheadingbutton = $this->page_heading_button();

        // Headings
        if (!isset($contextheader->heading)) {
            $headings = $this->heading($this->page->heading, $contextheader->headinglevel, 'page-title');
        } else {
            $headings = $this->heading($contextheader->heading, $contextheader->headinglevel, 'page-title');
        }

        if ($PAGE->activityname == 'accipioaccelerate') {
            $headings = str_replace('<h1', '<span', $headings);
            $headings = str_replace('</h1', '</span', $headings);
        }

        $html .= "<div class='float-left mr-10'>";
        $html .= $headings;

        if (empty($PAGE->layout_options['nonavbar'])) {
            $html .= $this->accipio_custom_breadcrumb();
        } else {
            $html .= '<ol class="breadcrumb"><li class="breadcrumb-item"><a href="#"><p></p></a></li></ol>';
        }
        $html .= "</div>";

        // little hack for now, to always show overlay buttons for mobile devices
        // will be transfered to html and css later
        $actualdevice = \core_useragent::get_device_type();
        $currentdevice = $this->page->devicetypeinuse;
        $overlay = false;
        if(!$overlay) {
            if($actualdevice == 'mobile' && $currentdevice == 'mobile') {
                $overlay = 1;
            }
        }

        // add heading and additional buttons in temp var
        // additional context header buttons
        if($overlay && !strpos($PAGE->bodyclasses, 'path-mod-forum')) {
            $htmltemp .= $pageheadingbutton;
        }
        if (isset($contextheader->additionalbuttons)) {
            foreach ($contextheader->additionalbuttons as $button) {
                if (!isset($button->page)) {
                    // Include js for messaging.
                    if ($button['buttontype'] === 'togglecontact') {
                        \core_message\helper::togglecontact_requirejs();
                    }

                    $image = $this->pix_icon($button['formattedimage'], $button['title'], 'moodle', array(
                        'class' => 'iconsize-button',
                        'role' => 'presentation'
                    ));

                    $image = html_writer::span($image.'&nbsp;&nbsp;'.$button['title']);
                } else {
                    $image = html_writer::empty_tag('img', array(
                        'src' => $button['formattedimage'],
                        'role' => 'presentation'
                    ));
                }

                $button['linkattributes']['class'] .= '  btn btn-inverse mr-5';
                $htmltemp .= html_writer::link($button['url'], $image, $button['linkattributes']);
            }
        }

        $html .= '<div style="clear: both;"></div>';

        return $html;
    }

    public function render_header_actions(){

        $html ='';
        // page header actions
        $html .= html_writer::start_div('page-header-actions float-right', array('style' => 'position: absolute!important;'));

        $html .= $this->page_heading_button();

        $html .= html_writer::end_div();

        return $html;
    }

    public function accipio_custom_breadcrumb(){
        global $CFG, $PAGE, $DB;
        $html = '';
        $html .= "<ol class='breadcrumb' itemscope  itemtype='http://schema.org/BreadcrumbList'>";

        $html .= "<li style='padding-right: 5px;' class='breadcrumb-item'>You are here:</li>";


        $homeurl = new moodle_url($CFG->wwwroot);
        $html .= "<li class='breadcrumb-item' itemprop='itemListElement' itemscope itemtype='http://schema.org/ListItem'><a href='{$homeurl}' itemprop='item'><span itemprop='name'>Home</span></a><meta itemprop='position' content='1'/></li>";

        if(fnmatch($CFG->wwwroot . '/blog/*/', $PAGE->url)|| $PAGE->url == $CFG->wwwroot . '/blog/'){

            $blogurl = new moodle_url($CFG->wwwroot . '/blog/');
            $html .= "<li class='breadcrumb-item' itemprop='itemListElement' itemscope itemtype='http://schema.org/ListItem'><a href='{$blogurl}' itemprop='item'><span itemprop='name'>Blog</span></a><meta itemprop='position' content='2'/></li>";
            if($PAGE->url != $CFG->wwwroot . '/blog/'){
                $name = str_replace('Free Online Learning for Work and Life | businessballs.com: Business Balls: ','',$PAGE->title);
                $html .= "<li class='breadcrumb-item' itemprop='itemListElement' itemscope itemtype='http://schema.org/ListItem'><a href='{$PAGE->url}' itemprop='item'><span itemprop='name'>$name</span></a><meta itemprop='position' content='3'/></li>";
            }
        } else {

            if(!($PAGE->course->shortname && $PAGE->course->shortname != '' && $PAGE->course->id != 1) && $PAGE->url != $CFG->wwwroot . '/blog/' && !fnmatch($CFG->wwwroot . '/search/*', $PAGE->url)) {

                $coursehomeurl = new moodle_url($CFG->wwwroot . '/');
                $html .= "<li class='breadcrumb-item' itemprop='itemListElement' itemscope itemtype='http://schema.org/ListItem'><a href='{$coursehomeurl}' itemprop='item'><span itemprop='name'>Courses</span></a><meta itemprop='position' content='2'/></li>";
                if($PAGE->category->name && $PAGE->category->name != ''){

                    $categoryurl = new moodle_url($CFG->wwwroot . '/' . $PAGE->category->idnumber . '/');
                    $html .= "<li class='breadcrumb-item' itemprop='itemListElement' itemscope itemtype='http://schema.org/ListItem'><a href='{$categoryurl}' itemprop='item'><span itemprop='name'>{$PAGE->category->name}</span></a><meta itemprop='position' content='3'/></li>";
                }
            } else {
                if($PAGE->category->name && $PAGE->category->name != ''){
                    $categoryurl = new moodle_url($CFG->wwwroot . '/' . $PAGE->category->idnumber . '/');
                    $html .= "<li class='breadcrumb-item' itemprop='itemListElement' itemscope itemtype='http://schema.org/ListItem'><a href='{$categoryurl}' itemprop='item'><span itemprop='name'>{$PAGE->category->name}</span></a><meta itemprop='position' content='2'/></li>";
                }
            }

            if($PAGE->course->shortname && $PAGE->course->shortname != '' && $PAGE->course->id != 1){
                $courseurl = new moodle_url($CFG->wwwroot . '/' . $PAGE->course->idnumber . '/');
                $html .= "<li class='breadcrumb-item' itemprop='itemListElement' itemscope itemtype='http://schema.org/ListItem'><a href='{$courseurl}' itemprop='item'><span itemprop='name'>{$PAGE->course->shortname}</span></a><meta itemprop='position' content='3'/></li>";
            }

            if($PAGE->cm->name && $PAGE->cm->name != ''){
                $cmurl = $DB->get_record('cus_headcontent', array('tagtype' => 'cleanurl', 'cmid' => $PAGE->cm->id))->value;
                if(!$cmurl){
                    $cmurl = $PAGE->cm->url;
                }
                $html .= "<li class='breadcrumb-item' itemprop='itemListElement' itemscope itemtype='http://schema.org/ListItem'><a href='{$cmurl}' itemprop='item'><span itemprop='name'>{$PAGE->cm->name}</span></a><meta itemprop='position' content='4'/></li>";
            }
        }

        if(fnmatch($CFG->wwwroot . '/search/*', $PAGE->url)){
            $html .= "<li class='breadcrumb-item' itemprop='itemListElement' itemscope itemtype='http://schema.org/ListItem'><a href='{$PAGE->url}' itemprop='item'><span itemprop='name'>Search</span></a><meta itemprop='position' content='2'/></li>";
        }

        $html .= "</ol>";

        return $html;
    }

    protected function course_header_imageurl($course) {
        global $PAGE, $CFG, $DB;

        if ($course instanceof stdClass) {
            $course_obj = new core_course_list_element($course);
        }

        $headerimage_url = 'https://www.businessballs.com/pluginfile.php/1/theme_accipio_bb/slideimage/0/home.jpg';


        foreach ($course_obj->get_course_overviewfiles() as $file) {
            $isimage = $file->is_valid_image();
            $url = file_encode_url("$CFG->wwwroot/pluginfile.php",
                '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
            if ($isimage) {
                $headerimage_url = $url;
            }
        }
        return $headerimage_url;
    }

    public function search_icon($id = false)
    {
        global $CFG;

        // Accessing $CFG directly as using \core_search::is_global_search_enabled would
        // result in an extra included file for each site, even the ones where global search
        // is disabled.
        if (empty($CFG->enableglobalsearch) || !has_capability('moodle/search:query', context_system::instance())) {
            return '';
        }

        // JS to animate the form.
        //   $this->page->requires->js_call_amd('core/search-input', 'init', array($id));

        if ($id == false) {
            $id = uniqid();
        } else {
            // Needs to be cleaned, we use it for the input id.
            $id = clean_param($id, PARAM_ALPHANUMEXT);
        }
        $searchicon =   html_writer::start_tag('i', array('class'=>'fas fa-search'));
        $searchicon .= html_writer::end_tag('i');

        return $searchicon;
    }

    /**
     * Returns a search box.
     *
     * @param  string $id     The search box wrapper div id, defaults to an autogenerated one.
     * @return string         HTML with the search form hidden by default.
     */
    public function search_box($id = false)
    {
        global $CFG;

        // Accessing $CFG directly as using \core_search::is_global_search_enabled would
        // result in an extra included file for each site, even the ones where global search
        // is disabled.
        if (empty($CFG->enableglobalsearch) || !has_capability('moodle/search:query', context_system::instance())) {
            return '';
        }

        if ($id == false) {
            $id = uniqid();
        } else {
            // Needs to be cleaned, we use it for the input id.
            $id = clean_param($id, PARAM_ALPHANUMEXT);
        }

        $formattrs = array('role' => 'search', 'class' => 'col-12', 'action' => $CFG->wwwroot . '/search/index.php');
        $inputattrs = array('type' => 'text', 'name' => 'q', 'placeholder' => get_string('search', 'search'),
            'size' => 13, 'tabindex' => -1, 'id' => 'id_q_' . $id, 'class' => 'form-control');

        $formcontent =  html_writer::tag('input', '', $inputattrs).
            '<i class="fad fa-times fa-lg" data-toggle="collapse" data-target="#searchInput"
                role="button" aria-expanded="true" aria-controls="searchInput"></i>';

        $form = html_writer::tag('form', $formcontent, $formattrs);


        return $form;
    }

    /**
     * Construct a user menu, returning HTML that can be echoed out by a
     * layout file.
     *
     * @param stdClass $user A user object, usually $USER.
     * @param bool $withlinks true if a dropdown should be built.
     * @return string HTML fragment.
     */
    public function user_menu($user = null, $withlinks = null)
    {
        global $USER, $CFG;
        require_once($CFG->dirroot . '/user/lib.php');
        require_once($CFG->dirroot . '/lib/moodlelib.php');

        if (is_null($user)) {
            $user = $USER;
        }

        // Note: this behaviour is intended to match that of core_renderer::login_info,
        // but should not be considered to be good practice; layout options are
        // intended to be theme-specific. Please don't copy this snippet anywhere else.
        if (is_null($withlinks)) {
            $withlinks = empty($this->page->layout_options['nologinlinks']);
        }

        // Add a class for when $withlinks is false.
        $usermenuclasses = array('class'=> 'usermenu nav-item dropdown user-menu login-menu');
        if (!$withlinks) {
            $usermenuclasses = array('class'=> ' nav-item dropdown user-menu login-menu withoutlinks');
        }

        $returnstr = "";

        // If during initial install, return the empty return string.
        if (during_initial_install()) {
            return $returnstr;
        }

        $loginpage = $this->is_login_page();
        $loginurl = get_login_url();
        $loginurl_datatoggle = '';

        // If not logged in, show the typical not-logged-in string.
        if (!isloggedin()) {
            //$returnstr = get_string('loggedinnot', 'moodle');
            $returnstr = '';
            if (!$loginpage) {
                $returnstr = '<a href="'.$loginurl.'" class="nav-link" '.$loginurl_datatoggle.' data-animation="scale-up">
                <i class="fa fa-user"></i>&nbsp;'.get_string('login').'</a>';
            }

            if(!empty($CFG->registerauth) && (!isloggedin() || isguestuser())){
                $returnstr .= "<li class='nav-item'>
                    <a href='/login/signup.php'><i class='fa fa-user-plus'></i> Create new Account</a>
                </li>";
            }

            return html_writer::tag('li', $returnstr, $usermenuclasses);
        }

        // If logged in as a guest user, show a string to that effect.
        if (isguestuser()) {
            //$returnstr = get_string('loggedinasguest');
            $returnstr = '';
            if (!$loginpage && $withlinks) {
                $returnstr = '<a href="'.$loginurl.'" class="nav-link" '.$loginurl_datatoggle.' data-animation="scale-up">
                <i class="fa fa-user"></i>&nbsp;'.get_string('login').'</a>';
            }

            if(!empty($CFG->registerauth) && (!isloggedin() || isguestuser())){
                $returnstr .= "<li class='nav-item'>
                    <a href='/login/signup.php'><i class='fa fa-user-plus'></i> Create new Account</a>
                </li>";
            }

            //return html_writer::tag('li', '<span class="text-white" style="line-height:66px;">'.get_string('loggedinasguest').'</span>', array('class' => 'nav-item'))
            return html_writer::tag('li', $returnstr, $usermenuclasses);
        }

        // Get some navigation opts.
        $opts = user_get_user_navigation_info($user, $this->page);

        $avatarclasses = "avatars";
        $avatarcontents = html_writer::span($opts->metadata['useravatar'], 'avatar current');
        $usertextcontents = $opts->metadata['userfullname'];

        // Other user.
        if (!empty($opts->metadata['asotheruser'])) {
            $avatarcontents .= html_writer::span(
                $opts->metadata['realuseravatar'],
                'avatar realuser'
            );
            $usertextcontents = $opts->metadata['realuserfullname'];
            $usertextcontents .= html_writer::tag(
                'span',
                get_string(
                    'loggedinas',
                    'moodle',
                    html_writer::span(
                        $opts->metadata['userfullname'],
                        'value'
                    )
                ),
                array('class' => 'meta viewingas')
            );
        }

        // Role.
        if (!empty($opts->metadata['asotherrole'])) {
            $role = core_text::strtolower(preg_replace('#[ ]+#', '-', trim($opts->metadata['rolename'])));
            $usertextcontents .= html_writer::span(
                ' ('. $opts->metadata['rolename'] .')',
                'meta text-uppercase font-size-12 role role-' . $role
            );
        }

        // User login failures.
        if (!empty($opts->metadata['userloginfail'])) {
            $usertextcontents .= html_writer::span(
                $opts->metadata['userloginfail'],
                'meta loginfailures'
            );
        }

        // MNet.
        if (!empty($opts->metadata['asmnetuser'])) {
            $mnet = strtolower(preg_replace('#[ ]+#', '-', trim($opts->metadata['mnetidprovidername'])));
            $usertextcontents .= html_writer::span(
                $opts->metadata['mnetidprovidername'],
                'meta mnet mnet-' . $mnet
            );
        }

        $returnstr .= html_writer::span(
            html_writer::span($usertextcontents, 'usertext') .
            html_writer::span($avatarcontents, $avatarclasses),
            'userbutton'
        );

        // Create a divider
        $divider = '<div class="dropdown-divider" role="presentation"></div>';

        $usermenu = '';
        $usermenu .= '<a class="nav-link navbar-avatar" data-toggle="dropdown" href="#" aria-expanded="false" data-animation="scale-up" role="button">
            <span class="username">'.$usertextcontents.'</span>
            <span class="avatar avatar-online current">
            '.$opts->metadata['useravatar'].'
            <i></i>
            </span>
        </a>';

        $usermenu .= '<div class="dropdown-menu" role="menu">';
        if ($withlinks) {
            $navitemcount = count($opts->navitems);
            $idx = 0;
            foreach ($opts->navitems as $key => $value) {
                switch ($value->itemtype) {
                    case 'divider':
                        // If the nav item is a divider, add one and skip link processing.
                        $usermenu .= $divider;
                        break;

                    case 'invalid':
                        // Silently skip invalid entries (should we post a notification?).
                        break;

                    case 'link':
                        // Process this as a link item.
                        $pix = null;
                        if (isset($value->pix) && !empty($value->pix)) {
                            $pix = new pix_icon($value->pix, $value->title, null, array('class' => 'iconsmall'));
                        } elseif (isset($value->imgsrc) && !empty($value->imgsrc)) {
                            $value->title = html_writer::img(
                                    $value->imgsrc,
                                    $value->title,
                                    array('class' => 'iconsmall')
                                ) . $value->title;
                        }

                        $icon = $this->pix_icon($pix->pix, '', 'moodle', $pix->attributes);
                        $usermenu .= '<a class="dropdown-item" href="'.$value->url.'" role="menuitem">'.$icon.$value->title.'</a>';
                        break;
                }

                $idx++;

                // Add dividers after the first item and before the last item.
                if ($idx == 1 || $idx == $navitemcount - 1) {
                    $usermenu .= $divider;
                }
            }
        }
        $usermenu .= '</div>';

        return html_writer::tag('li', $usermenu, $usermenuclasses);
    }

    /**
     * The standard tags (meta tags, links to stylesheets and JavaScript, etc.)
     * that should be included in the <head> tag. Designed to be called in theme
     * layout.php files.
     *
     * @return string HTML fragment.
     */
    public function standard_head_html() {
        global $CFG, $SESSION, $SITE, $PAGE;

        // Before we output any content, we need to ensure that certain
        // page components are set up.

        // Blocks must be set up early as they may require javascript which
        // has to be included in the page header before output is created.
        foreach ($this->page->blocks->get_regions() as $region) {
            $this->page->blocks->ensure_content_created($region, $this);
        }

        $output = '';

        // Give plugins an opportunity to add any head elements. The callback
        // must always return a string containing valid html head content.
        $pluginswithfunction = get_plugins_with_function('before_standard_html_head', 'lib.php');
        foreach ($pluginswithfunction as $plugins) {
            foreach ($plugins as $function) {
                $output .= $function();
            }
        }

        // Allow a url_rewrite plugin to setup any dynamic head content.
        if (isset($CFG->urlrewriteclass) && !isset($CFG->upgraderunning)) {
            $class = $CFG->urlrewriteclass;
            $output .= $class::html_head_setup();
        }

        $output .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "\n";
        $output .= '<meta name="keywords" content="moodle, ' . $this->page->title . '" />' . "\n";
        // This is only set by the {@link redirect()} method
        $output .= $this->metarefreshtag;

        // Check if a periodic refresh delay has been set and make sure we arn't
        // already meta refreshing
        if ($this->metarefreshtag=='' && $this->page->periodicrefreshdelay!==null) {
            $output .= '<meta http-equiv="refresh" content="'.$this->page->periodicrefreshdelay.';url='.$this->page->url->out().'" />';
        }

        // Set up help link popups for all links with the helptooltip class
        $this->page->requires->js_init_call('M.util.help_popups.setup');

        $focus = $this->page->focuscontrol;
        if (!empty($focus)) {
            if (preg_match("#forms\['([a-zA-Z0-9]+)'\].elements\['([a-zA-Z0-9]+)'\]#", $focus, $matches)) {
                // This is a horrifically bad way to handle focus but it is passed in
                // through messy formslib::moodleform
                $this->page->requires->js_function_call('old_onload_focus', array($matches[1], $matches[2]));
            } else if (strpos($focus, '.')!==false) {
                // Old style of focus, bad way to do it
                debugging('This code is using the old style focus event, Please update this code to focus on an element id or the moodleform focus method.', DEBUG_DEVELOPER);
                $this->page->requires->js_function_call('old_onload_focus', explode('.', $focus, 2));
            } else {
                // Focus element with given id
                $this->page->requires->js_function_call('focuscontrol', array($focus));
            }
        }

//         Get the theme stylesheet - this has to be always first CSS, this loads also styles.css from all plugins;
//         any other custom CSS can not be overridden via themes and is highly discouraged
        $urls = $this->page->theme->css_urls($this->page);
        foreach ($urls as $url) {
            $this->page->requires->css_theme($url);
        }

        // Get the theme javascript head and footer
        if ($jsurl = $this->page->theme->javascript_url(true)) {
            $this->page->requires->js($jsurl, true);
        }
        if ($jsurl = $this->page->theme->javascript_url(false)) {
            $this->page->requires->js($jsurl);
        }

        // Get any HTML from the page_requirements_manager.
        $output .= $this->page->requires->get_head_code($this->page, $this);

        // List alternate versions.
        foreach ($this->page->alternateversions as $type => $alt) {
            $output .= html_writer::empty_tag('link', array('rel' => 'alternate',
                'type' => $type, 'title' => $alt->title, 'href' => $alt->url));
        }

        // Add noindex tag if relevant page and setting applied.
        $allowindexing = isset($CFG->allowindexing) ? $CFG->allowindexing : 0;
        $loginpages = array('login-index', 'login-signup');
        if ($allowindexing == 2 || ($allowindexing == 0 && in_array($this->page->pagetype, $loginpages))) {
            if (!isset($CFG->additionalhtmlhead)) {
                $CFG->additionalhtmlhead = '';
            }
            $CFG->additionalhtmlhead .= '<meta name="robots" content="noindex" />';
        }

        if (!empty($CFG->additionalhtmlhead)) {
            $output .= "\n".$CFG->additionalhtmlhead;
        }

        if ($PAGE->pagelayout == 'frontpage') {
            $summary = s(strip_tags(format_text($SITE->summary, FORMAT_HTML)));
            if (!empty($summary)) {
                $output .= "<meta name=\"description\" content=\"$summary\" />\n";
            }
        }

        return $output;
    }

    /**
     * The standard tags (typically skip links) that should be output just inside
     * the start of the <body> tag. Designed to be called in theme layout.php files.
     *
     * @return string HTML fragment.
     */
    public function standard_top_of_body_html() {
        global $CFG;
        $output = '';

        if(is_siteadmin()){
            $output = $this->page->requires->get_top_of_body_code($this);
        } else {
            $output = $this->get_top_of_body_code();
        }

        if ($this->page->pagelayout !== 'embedded' && !empty($CFG->additionalhtmltopofbody)) {
            $output .= "\n".$CFG->additionalhtmltopofbody;
        }

        // Give subsystems an opportunity to inject extra html content. The callback
        // must always return a string containing valid html.
        foreach (\core_component::get_core_subsystems() as $name => $path) {
            if ($path) {
                $output .= component_callback($name, 'before_standard_top_of_body_html', [], '');
            }
        }

        // Give plugins an opportunity to inject extra html content. The callback
        // must always return a string containing valid html.
        $pluginswithfunction = get_plugins_with_function('before_standard_top_of_body_html', 'lib.php');
        foreach ($pluginswithfunction as $plugins) {
            foreach ($plugins as $function) {
                $output .= $function();
            }
        }

        $output .= $this->maintenance_warning();

        return $output;
    }

    /**
     * Generate any HTML that needs to go at the start of the <body> tag.
     *
     * Normally, this method is called automatically by the code that prints the
     * <head> tag. You should not normally need to call it in your own code.
     *
     * @return string the HTML code to go at the start of the <body> tag.
     */
    public function get_top_of_body_code() {
        $output = '';
        $output .= '<script defer type="text/javascript" src="https://beta.businessballs.com/lib/javascript-static.js"></script>';
        $output .= '<script defer type="text/javascript" src="https://beta.businessballs.com/theme/yui_combo.php?3.17.2/event-mousewheel/event-mousewheel-min.js&3.17.2/event-resize/event-resize-min.js&3.17.2/event-hover/event-hover-min.js&3.17.2/event-touch/event-touch-min.js&3.17.2/event-move/event-move-min.js&3.17.2/event-flick/event-flick-min.js&3.17.2/event-valuechange/event-valuechange-min.js&3.17.2/event-tap/event-tap-min.js"></script>';
        $output .= '<script defer type="text/javascript" src="https://beta.businessballs.com/theme/yui_combo.php?m/1586538764/core/event/event-debug-min.js&m/1586538764/filter_mathjaxloader/loader/loader-debug-min.js"></script>';
        $output .= '<script defer type="text/javascript" src="https://beta.businessballs.com/theme/yui_combo.php?rollup/3.17.2/yui-moodlesimple-min.js"></script>';
        return $output;
    }
}