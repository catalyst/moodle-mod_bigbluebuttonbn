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
 * Bootstrap file to define $CFG variables and constants.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    mikhailgolenkov@catalyst-au.net
 */

global $CFG;

// Do not declare new $CFG variables if unit tests are running
// as it can cause "unexpected new $CFG->xxx value" warnings.
if (!defined('PHPUNIT_TEST') || !PHPUNIT_TEST) {
    // JWT is included in Moodle 3.7 core, but a local package is still needed for backward compatibility.
    if (!class_exists('\Firebase\JWT\JWT')) {
        if (file_exists($CFG->libdir.'/php-jwt/src/JWT.php')) {
            require_once($CFG->libdir.'/php-jwt/src/JWT.php');
        } else {
            require_once($CFG->dirroot.'/mod/bigbluebuttonbn/vendor/firebase/php-jwt/src/JWT.php');
        }
    }

    if (!isset($CFG->bigbluebuttonbn)) {
        $CFG->bigbluebuttonbn = array();
    }

    if (file_exists(dirname(__FILE__).'/config.php')) {
        require_once(dirname(__FILE__).'/config.php');
    }

    /*
     * DURATIONCOMPENSATION: Feature removed by configuration
     */
    $CFG->bigbluebuttonbn['scheduled_duration_enabled'] = 0;
    /*
     * Remove this block when restored
     */
}

/** @var BIGBLUEBUTTONBN_DEFAULT_SERVER_URL string of default bigbluebutton server url */
const BIGBLUEBUTTONBN_DEFAULT_SERVER_URL = 'http://test-install.blindsidenetworks.com/bigbluebutton/';
/** @var BIGBLUEBUTTONBN_DEFAULT_SHARED_SECRET string of default bigbluebutton server shared secret */
const BIGBLUEBUTTONBN_DEFAULT_SHARED_SECRET = '8cd8ef52e8e101574e400365b55e11a6';
/** @var BIGBLUEBUTTONBN_LOG_EVENT_ADD string of event add for bigbluebuttonbn_logs */
const BIGBLUEBUTTONBN_LOG_EVENT_ADD = 'Add';
/** @var BIGBLUEBUTTONBN_LOG_EVENT_EDIT string of event edit for bigbluebuttonbn_logs */
const BIGBLUEBUTTONBN_LOG_EVENT_EDIT = 'Edit';
/** @var BIGBLUEBUTTONBN_LOG_EVENT_CREATE string of event create for bigbluebuttonbn_logs */
const BIGBLUEBUTTONBN_LOG_EVENT_CREATE = 'Create';
/** @var BIGBLUEBUTTONBN_LOG_EVENT_JOIN string of event join for bigbluebuttonbn_logs */
const BIGBLUEBUTTONBN_LOG_EVENT_JOIN = 'Join';
/** @var BIGBLUEBUTTONBN_LOG_EVENT_PLAYED string of event record played for bigbluebuttonbn_logs */
const BIGBLUEBUTTONBN_LOG_EVENT_PLAYED = 'Played';
/** @var BIGBLUEBUTTONBN_LOG_EVENT_LOGOUT string of event logout for bigbluebuttonbn_logs */
const BIGBLUEBUTTONBN_LOG_EVENT_LOGOUT = 'Logout';
/** @var BIGBLUEBUTTONBN_LOG_EVENT_IMPORT string of event import for bigbluebuttonbn_logs */
const BIGBLUEBUTTONBN_LOG_EVENT_IMPORT = 'Import';
/** @var BIGBLUEBUTTONBN_LOG_EVENT_DELETE string of event delete for bigbluebuttonbn_logs */
const BIGBLUEBUTTONBN_LOG_EVENT_DELETE = 'Delete';
/** @var BIGBLUEBUTTON_LOG_EVENT_CALLBACK string defines the bigbluebuttonbn callback event */
const BIGBLUEBUTTON_LOG_EVENT_CALLBACK = 'Callback';
