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
 * All events consumed by local reminder plugin.
 *
 * @package    ltool_invite
 * @copyright  2012 Isuru Madushanka Weerarathna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace ltool_invite;

defined('MOODLE_INTERNAL') || die();

class event_observer {

    public static function delete_invite_reports($event) {
        global $DB;
        $eventdata = $event->get_data();
        $userid = $eventdata['objectid'];
        $DB->delete_records('learningtools_invite', array('userid' => $userid));
        return true;
    }
}