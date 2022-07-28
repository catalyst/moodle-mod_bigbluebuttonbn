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
 * Page to grant external users access to a BBB session
 *
 * @package    mod_bigbluebuttonbn
 * @author     Angela Baier
 * @copyright  2020 University of Vienna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once(__DIR__ . '/lib.php');
require_once(__DIR__ . '/locallib.php');

global $PAGE, $OUTPUT;

$gid = required_param('gid', PARAM_ALPHANUM); // This is required.
$grouphash = optional_param('group', '', PARAM_ALPHANUM);
$guestname = trim(optional_param('guestname', null, PARAM_TEXT));
$guestpass = optional_param('guestpass', '', PARAM_TEXT);
$PAGE->set_url(new moodle_url('/mod/bigbluebuttonbn/guestlink.php', [
    'gid' => $gid,
    'group' => $grouphash,
    'guestname' => $guestname,
    'guestpass' => $guestpass,
]));
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');

if (!\mod_bigbluebuttonbn\locallib\config::get('participant_guestlink')) {
    echo get_string('guestlink_form_guestlink_disabled', 'bigbluebuttonbn');
    die();
}

$bigbluebuttonbn = bigbluebuttonbn_get_bigbluebuttonbn_by_guestlinkid($gid);
if (!$bigbluebuttonbn->guestlinkenabled) {
    echo get_string('guestlink_form_guestlink_disabled_instance', 'bigbluebuttonbn');
    die();
} else if ($bigbluebuttonbn->guestlinkexpiresat != 0 && $bigbluebuttonbn->guestlinkexpiresat < time()) {
    echo get_string('guestlink_form_guestlink_access_expired', 'bigbluebuttonbn');
    die;
}

// Fetch the course and cm from the given bbb instance.
list($course, $cm) = get_course_and_cm_from_instance($bigbluebuttonbn, 'bigbluebuttonbn');

// Validate and resolve the group id for the given hash provided.
if (!empty($grouphash)) {
    // Fetch groups.
    $groups = groups_get_all_groups($course->id, 0, $course->defaultgroupingid);
    $groupids = array_keys($groups);
    // Add the "All participants" group as it is not included by default.
    $groupids[] = 0;
    $groupid = null;

    // Find the matching group given a hash.
    foreach ($groupids as $groupid) {
        $hash = bigbluebuttonbn_generate_group_hash($groupid, $bigbluebuttonbn->groupsalt);
        // If the hash matches with the one provided.
        if ($hash === $grouphash) {
            break;
        }
    }
}

$valid = (!empty($guestname) && ($bigbluebuttonbn->guestpass == $guestpass || !$bigbluebuttonbn->guestpass));

if (!$valid) {
    $guestpasserrormessage = false;
    $guestnameerrormessage = false;
    if ($guestpass && $bigbluebuttonbn->guestpass != $guestpass) {
        $guestpasserrormessage = true;
    }
    // Ensure initial load does not display guestname error message and only when submitted with no name.
    if (empty($guestname)) {
        $guestnameerrormessage = true;
    }

    // Append the group name in the name of the meeting as well, if required.
    // This displays similarly when a normal user enters a grouped BBB session.
    $name = $bigbluebuttonbn->name;
    if (isset($groupid) && isset($groups[$groupid])) {
        $group = $groups[$groupid];
        $name .= " ({$group->name})";
    }

    $context = [
        'name' => $name,
        'gid' => $gid,
        'guestpassenabled' => $bigbluebuttonbn->guestpass,
        'guestpasserrormessage' => $guestpasserrormessage,
        'guestnameerrormessage' => $guestnameerrormessage,
        'guestname' => $guestname,
        'group' => $grouphash,
    ];

    echo $OUTPUT->header();

    // Setup access policy modal window
    // If global editing is disabled, it is always the global version.
    // Note: Duplicate block @ mod/bigbluebuttonbn/viewlib.php:258
    if ((int) $CFG->bigbluebuttonbn_accessmodal_editable) {
        $policytext = format_text($bbbsession['bigbluebuttonbn']->accesspolicy ?? '');
        // If empty here, try the default policy.
        if (empty(strip_tags($policytext))) {
            $policytext = format_text($CFG->bigbluebuttonbn_accessmodal_default ?? '');
        }
    } else {
        $policytext = format_text($CFG->bigbluebuttonbn_accessmodal_default ?? '');
    }

    // Check if there is any content with HTML stripped.
    if (!empty(strip_tags($policytext))) {
        echo $OUTPUT->render_from_template('mod_bigbluebuttonbn/accesspolicy', [
            'body' => $policytext,
            'forward' => null,
            'hash' => md5($policytext),
            'alert' => get_string('accesspolicyalert', 'mod_bigbluebuttonbn')
        ]);
        // Add the JS var to setup the modal from JS.
        $context['accesspolicy'] = true;
    }
    echo $OUTPUT->render_from_template('mod_bigbluebuttonbn/guestaccess_view', $context);

    echo $OUTPUT->footer();
} else {
    $context = context_module::instance($cm->id);
    $bbbsession = [];
    $bbbsession['course'] = $course;
    $bbbsession['coursename'] = $course->fullname;
    $bbbsession['cm'] = $cm;
    $bbbsession['bigbluebuttonbn'] = $bigbluebuttonbn;
    $bbbsession['guest'] = true;

    \mod_bigbluebuttonbn\locallib\bigbluebutton::view_bbbsession_set($context, $bbbsession);

    // Groups handling
    if (isset($groupid)) {
        $bbbsession['meetingid'] .= '['.$groupid.']';
    }

    if (bigbluebuttonbn_is_meeting_running($bbbsession['meetingid'])) {
        $bbbsession['username'] = $guestname;
        // Since the meeting is already running, we just join the session.
        bigbluebuttonbn_join_meeting($bbbsession, $bigbluebuttonbn);
    } else {
        $pinginterval = (int)\mod_bigbluebuttonbn\locallib\config::get('waitformoderator_ping_interval') * 1000;
        echo $OUTPUT->header();
        echo get_string('guestlink_form_join_waiting', 'bigbluebuttonbn');
        echo "<script> setTimeout(function () {location.reload();}, $pinginterval);</script>";
        echo $OUTPUT->footer();
    }
}
