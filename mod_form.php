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
 * The main mod_gamoteca configuration form.
 *
 * @package     mod_gamoteca
 * @copyright   2020 Catalyst IT Europe (http://www.catalyst-eu.net/)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form.
 *
 * @package    mod_gamoteca
 * @copyright  2020 Catalyst IT Europe (http://www.catalyst-eu.net/)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_gamoteca_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are shown.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('name', 'mod_gamoteca'), array('size' => '64'));

        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }

        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'name', 'mod_gamoteca');

        $mform->addElement('url', 'gamotecaurl', get_string('gamotecaurl', 'mod_gamoteca'), array('size'=>'255'), array('usefilepicker' => false));
        $mform->setType('gamotecaurl', PARAM_URL);
        $mform->addRule('gamotecaurl', null, 'required', null, 'client');

        $this->standard_intro_elements();
        $element = $mform->getElement('introeditor');
        $attributes = $element->getAttributes();
        $attributes['rows'] = 5;
        $element->setAttributes($attributes);

        // Add standard elements.
        $this->standard_coursemodule_elements();

        // Add standard buttons.
        $this->add_action_buttons();
    }

    /**
     * Add any custom completion rules to the form.
     *
     * @return array Contains the names of the added form elements
     */
    public function add_completion_rules() {
        $mform =& $this->_form;

        $group = array();
        $group[] =& $mform->createElement('text', 'completionscorerequired', '', array('size' => 5));
        $group[] =& $mform->createElement('advcheckbox', 'completionscoredisabled', null, get_string('disable'));
        $mform->setType('completionscorerequired', PARAM_INT);
        $mform->addGroup($group, 'completionscoregroup', get_string('completionscorerequired', 'mod_gamoteca'), '', false);
        $mform->addHelpButton('completionscoregroup', 'completionscorerequired', 'mod_gamoteca');
        $mform->disabledIf('completionscorerequired', 'completionscoredisabled', 'checked');
        $mform->setDefault('completionscorerequired', 0);
        $items[] = 'completionscoregroup';

        $group = array();
        $group[] =& $mform->createElement('text', 'completionstatusrequired', '', array('size' => 20));
        $group[] =& $mform->createElement('advcheckbox', 'completionstatusdisabled', null, get_string('disable'));
        $mform->setType('completionstatusrequired', PARAM_CLEANHTML);
        $mform->addGroup($group, 'completionstatusgroup', get_string('completionstatusrequired', 'mod_gamoteca'), '', false);
        $mform->addHelpButton('completionstatusgroup', 'completionstatusrequired', 'mod_gamoteca');
        $mform->disabledIf('completionstatusrequired', 'completionstatusdisabled', 'checked');
        $mform->setDefault('completionstatusrequired', 'passed');
        $items[] = 'completionstatusgroup';

        return $items;
    }

    /**
     * Determines if completion is enabled for this module.
     *
     * @param array $data
     * @return bool
     */
    public function completion_rule_enabled($data) {

        if ($data['completionscoredisabled'] == 1 && $data['completionstatusdisabled'] == 1) {
            return false;
        }

        if ($data['completionscoredisabled'] == 0) {
            if (empty($data['completionscorerequired']) || empty($data['completionscorerequired'])) {
                return false;
            }
        }

        if ($data['completionstatusdisabled'] == 0 && empty($data['completionstatusrequired'])) {
            return false;
        }

        return true;
    }

    /**
     * Only available on moodleform_mod.
     *
     * @param array $default_values passed by reference
     */
    public function data_preprocessing(&$defaultvalues) {
        if (!isset($defaultvalues['completionscorerequired']) || !strlen($defaultvalues['completionscorerequired'])) {
            $defaultvalues['completionscoredisabled'] = 1;
        }
        if (!isset($defaultvalues['completionstatusrequired']) || !strlen($defaultvalues['completionstatusrequired'])) {
            $defaultvalues['completionstatusdisabled'] = 1;
        }
    }

    /**
     * Form validation
     *
     * @param array $data data from the form.
     * @param array $files files uploaded.
     *
     * @return array of errors.
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (!empty($data['gamotecaurl'])) {
            $url = $data['gamotecaurl'];

            // Check if URL starts with either http or https.
            if (!preg_match('|^[a-z]+://|i', $url) || !preg_match('|^https?:|i', $url)) {
                $errors['gamotecaurl'] = get_string('invalidurl', 'mod_gamoteca');
            }
        }
        return $errors;
    }

    /**
     * Return submitted data if properly submitted or returns NULL if validation fails or
     * if there is no submitted data.
     *
     * @return object submitted data; NULL if not valid or not submitted or cancelled
     */
    public function get_data() {
        $data = parent::get_data();
        if (!$data) {
            return $data;
        }

        if (isset($data->completionscoredisabled) && $data->completionscoredisabled == 1) {
            $data->completionscorerequired = 0;
        }
        if (isset($data->completionstatusdisabled) && $data->completionstatusdisabled == 1) {
            $data->completionstatusrequired = '';
        }

        return $data;
    }
}
