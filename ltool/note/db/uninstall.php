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
 * Define uninstall function
 * @package    ltool_note
 * @copyright  bdecent GmbH 2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * ltool_note uninstall function.
 *
 * @return void
 */
function xmldb_ltool_note_uninstall() {
    global $DB;

    $plugin = 'note';
    if ($DB->record_exists('local_learningtool_products', array('shortname' => $plugin)) ) {
        $DB->delete_records('local_learningtool_products', array('shortname' => $plugin));
    }
    // Delete the table.
    $table = "learningtools_note";
    $dbman = $DB->get_manager();
    if ($dbman->table_exists($table)) {
        $droptable = new xmldb_table($table);
        $dbman->drop_table($droptable);
    }

}
