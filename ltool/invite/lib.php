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
 * ltool plugin "Learning Tools Invite" - library file.
 *
 * @package   ltool_invite
 * @copyright bdecent GmbH 2021
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use core_user\output\myprofile\tree;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");
/**
 * Learning tools invite template function.
 * @param array $templatecontent template content
 * @return string display html content.
 */
function ltool_invite_render_template($templatecontent) {
    global $OUTPUT;
    return $OUTPUT->render_from_template('ltool_invite/invite', $templatecontent);
}

/**
 * Load invite js.
 * @return void
 */
function load_invite_js_config() {
    global $PAGE, $USER;

    $params =[];
    if (!empty($PAGE->course->id)) {
        $params['course'] = $PAGE->course->id;
        $params['user'] = $USER->id;
        $params['contextid'] = $PAGE->context->id;
        $params['contextlevel'] = $PAGE->context->contextlevel;
        $params['pageurl'] = $PAGE->url->out(false);
        $params['strinviteusers'] = get_string('inviteusers', 'local_learningtools');
        $PAGE->requires->js_call_amd('ltool_invite/ltoolsinvite', 'init', array($params));
    }
}

/**
 * Load the invite users form
 * @param array $args page arguments
 * @return string Display the html invite users form.
 */
function ltool_invite_output_fragment_get_inviteusers_form($args) {
    $inviteform = new ltool_inviteusers_mform($args['pageurl']);
    $formhtml = html_writer::start_tag('div', array('id' => 'invite-users-area'));
    $formhtml .= $inviteform->render();
    $formhtml .= html_writer::end_tag('div');
    return $formhtml;
}

function invite_users_action($params, $data) {
    global $DB;
    $record = new stdClass;
    if (isset($data['inviteusers']) && !empty($data['inviteusers'])) {
        $useremails = $data['inviteusers'];
        $useremails = explode("\n",$useremails);
        $teacher = $DB->get_record('user', array('id' => $params->user));
        $course = $DB->get_record('course', array('id' => $params->course));
        $coursecontext = context_course::instance($course->id);
        $record->teacher = $teacher->id;
        $record->course = $course->id;
        if (has_capability('ltool/invite:accessinvite', $coursecontext)) {
            if (!empty($useremails)) {
                foreach ($useremails as $useremail) {
                    if ($DB->record_exists('user', array('email' => $useremail))) {
                        $user = $DB->get_record('user', array('email' => $useremail));
                        $record->userid = $user->id;
                        if (!empty($user) && !$user->suspended) {
                            if (!is_enrolled($coursecontext, $user)) {
                                $plugin = enrol_get_plugin('manual');
                                $instance = $DB->get_record('enrol', array('courseid' => $course->id, 'enrol' => 'manual'));
                                $student = $DB->get_record('role', array('shortname' => 'student'));
                                $plugin->enrol_user($instance, $user->id, $student->id);
                                $record->status = 'enrolled';
                                $record->enrolled = 1;
                            } else {
                                $record->status = "alredyenrolled";
                                $record->enrolled = 0;
                            }
                        } else if ($user->suspended)  {
                            $record->status = "suspended";
                            $record->enrolled = 0;
                        }       
                    }
                }
                
            }
        }
        $record->timecreated = time();
    }
}

class ltool_inviteusers_mform extends moodleform {
    // Add elements to form.
    public function definition() {
        $mform = $this->_form;
        $mform->addElement('textarea', 'inviteusers', get_string('usersemail', 'local_learningtools'),
             'wrap="virtual" rows="15" cols=50"');
    }
}


