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
 * Form for filtering recording analytics table.
 *
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_bigbluebuttonbn\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/bigbluebuttonbn/locallib.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/mod/bigbluebuttonbn/mod_form.php');

/**
 * Form for filtering recording analytics table.
 *
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class analytics_recording_filter_form extends \moodleform {

    /**
     * Define (add) particular settings this activity can have.
     *
     * @return void
     */
    public function definition() {
        $mform = &$this->_form;

        $mform->addElement('header', 'newfilter', get_string('filter'));

        // Create a form group to store the filters.
        $group = [];
        $group[] =& $mform->createElement('date_time_selector', 'filterstart');
        $group[] =& $mform->createElement('advcheckbox', 'filterall', '', get_string('filter_form_showall', 'mod_bigbluebuttonbn'));
        $mform->addGroup($group, 'filtergroup', get_string('from'), array(' '), false);

        $mform->setType('filterstart', PARAM_INT);
        $mform->setDefault('filterstart', time() - 2 * WEEKSECS);

        $mform->setType('filterall', PARAM_BOOL);
        $mform->setDefault('filterall', false);

        $mform->addElement('submit', 'submitbutton', get_string('filter'));
    }
}
