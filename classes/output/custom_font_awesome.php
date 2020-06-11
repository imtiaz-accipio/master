<?php

namespace theme_master\output;

defined('MOODLE_INTERNAL') || die();

class custom_font_awesome extends \core\output\icon_system_fontawesome {

    /**
     * @var array $map Cached map of moodle icon names to font awesome icon names.
     */
    private $map = [];

    public function get_core_icon_map() {
        $iconmap = parent::get_core_icon_map();

        $iconmap['core:i/loading'] = 'fa-circle-notch fa-spin';
        $iconmap['core:i/loading_small'] = 'fa-circle-notch fa-spin';

        $iconmap['core:e/insert_time'] = 'fas fa-clock';
        $iconmap['core:i/calendareventtime'] = ' fas fa-clock';
        $iconmap['core:i/duration'] = 'fas fa-clock';
        $iconmap['core:i/emojicategoryrecent'] = 'fas fa-clock';

        $iconmap['core:i/report'] = 'fas fa-chart-area';

        $iconmap['core:i/backup'] = 'fas fa-file-archive';

        $iconmap['core:i/emojicategoryobjects'] = 'fas fa-lightbulb';
        $iconmap['core:e/text_highlight'] = 'fas fa-lightbulb';
        $iconmap['core:e/text_highlight_picker'] = 'fas fa-lightbulb';

        $iconmap['core:e/emoticons'] = 'fas fa-smile';
        $iconmap['core:i/emojicategorysmileyspeople'] = 'fas fa-smile';

        $iconmap['core:i/emojicategoryfooddrink'] = 'fas fa-utensils-alt';

        $iconmap['core:i/emojicategoryactivities'] = 'fas fa-futbol';

        $iconmap['core:e/special_character'] = 'fas fa-pen-square';
        $iconmap['core:i/permissions'] = 'fas fa-pen-square';

        $iconmap['core:i/competencies'] = 'fal fa-check-square';

        $iconmap['core:req'] = 'fas fa-exclamation-circle';
        $iconmap['core:i/risk_personal'] = 'fas fa-exclamation-circle';

        return $iconmap;
    }

}