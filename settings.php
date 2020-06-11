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

/**
 * @package   theme_master
 * @copyright 2016 Ryan Wyllie
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings = new theme_master_admin_settingspage_tabs('themesettingmaster', get_string('configtitle', 'theme_master'));
    $page = new admin_settingpage('theme_master_general', get_string('generalsettings', 'theme_master'));

    // Preset.
    $name = 'theme_master/preset';
    $title = get_string('preset', 'theme_master');
    $description = get_string('preset_desc', 'theme_master');
    $default = 'default.scss';

    $context = context_system::instance();
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'theme_master', 'preset', 0, 'itemid, filepath, filename', false);

    $choices = [];
    foreach ($files as $file) {
        $choices[$file->get_filename()] = $file->get_filename();
    }
    // These are the built in presets.
    $choices['default.scss'] = 'default.scss';
    $choices['plain.scss'] = 'plain.scss';

    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Preset files setting.
    $name = 'theme_master/presetfiles';
    $title = get_string('presetfiles','theme_master');
    $description = get_string('presetfiles_desc', 'theme_master');

    $setting = new admin_setting_configstoredfile($name, $title, $description, 'preset', 0,
        array('maxfiles' => 20, 'accepted_types' => array('.scss')));
    $page->add($setting);

    // Background image setting.
    $name = 'theme_master/backgroundimage';
    $title = get_string('backgroundimage', 'theme_master');
    $description = get_string('backgroundimage_desc', 'theme_master');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'backgroundimage');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Login Background image setting.
    $name = 'theme_master/loginbackgroundimage';
    $title = get_string('loginbackgroundimage', 'theme_master');
    $description = get_string('login_backgroundimage_desc', 'theme_master');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'loginbackgroundimage');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Logo image setting.
    $name = 'theme_master/logo_image';
    $title = get_string('logo_image', 'theme_master');
    $description = get_string('logo_image_desc', 'theme_master');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'logo_image');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Banner image setting.
    $name = 'theme_master/banner_image';
    $title = get_string('banner_image', 'theme_master');
    $description = get_string('banner_image_desc', 'theme_master');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'banner_image');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Fav Icon setting.
    $name = 'theme_master/fav_icon_image';
    $title = get_string('fav_icon_image', 'theme_master');
    $description = get_string('fav_icon_image_desc', 'theme_master');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'fav_icon_image');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Variable primary-color.
    // We use an empty default value because the default colour should come from the preset.
    $name = 'theme_master/brandcolor';
    $title = get_string('brandcolor', 'theme_master');
    $description = get_string('brandcolor_desc', 'theme_master');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Variable secondary color.
    // We use an empty default value because the default colour should come from the preset.
    $name = 'theme_master/secondary_color';
    $title = get_string('secondary_color', 'theme_master');
    $description = get_string('secondary_color_desc', 'theme_master');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Variable base color.
    // We use an empty default value because the default colour should come from the preset.
    $name = 'theme_master/base_color';
    $title = get_string('base_color', 'theme_master');
    $description = get_string('base_color_desc', 'theme_master');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Must add the page after definiting all the settings!
    $settings->add($page);

    // Advanced settings.
    $page = new admin_settingpage('theme_master_advanced', get_string('advancedsettings', 'theme_master'));

    // Raw SCSS to include before the content.
    $setting = new admin_setting_scsscode('theme_master/scsspre',
        get_string('rawscsspre', 'theme_master'), get_string('rawscsspre_desc', 'theme_master'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Raw SCSS to include after the content.
    $setting = new admin_setting_scsscode('theme_master/scss', get_string('rawscss', 'theme_master'),
        get_string('rawscss_desc', 'theme_master'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);
}
