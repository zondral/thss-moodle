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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains all necessary code to define and process an edit form
 *
 * @package mod-wiki-2.0
 * @copyrigth 2009 Marc Alier, Jordi Piguillem marc.alier@upc.edu
 * @copyrigth 2009 Universitat Politecnica de Catalunya http://www.upc.edu
 *
 * @author Josep Arus
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . "/mod/wiki/editors/wikieditor.php");
require_once($CFG->dirroot . "/mod/wiki/editors/wikifiletable.php");

class mod_wiki_edit_form extends moodleform {

    protected function definition() {
        global $CFG;

        $mform =& $this->_form;

        $version = $this->_customdata['version'];
        $format = $this->_customdata['format'];
        $tags = !isset($this->_customdata['tags'])?"":$this->_customdata['tags'];


        if ($format != 'html') {
            $contextid = $this->_customdata['contextid'];
            $filearea = $this->_customdata['filearea'];
            $fileitemid = $this->_customdata['fileitemid'];
        }

        //editor
        $mform->addElement('header', 'general', get_string('general'));

        if ($format != 'html') {
            $mform->addElement('wikieditor', 'newcontent', get_string('content'), array('cols' => 50, 'rows' => 20, 'wiki_format' => $format));
            $mform->addHelpButton('newcontent', 'format'.$format, 'wiki');
        } else {
            $mform->addElement('editor', 'newcontent_editor', get_string('content'), null, page_wiki_edit::$attachmentoptions);
            $mform->addHelpButton('newcontent_editor', 'formathtml', 'wiki');

        }

        //hiddens
        if ($version >= 0) {
            $mform->addElement('hidden', 'version');
            $mform->setDefault('version', $version);
        }

        $mform->addElement('hidden', 'contentformat');
        $mform->setDefault('contentformat', $format);

//        if ($format != 'html') {
//            //uploads
//            $mform->addElement('header', 'attachments_tags', get_string('attachments', 'wiki'));
//            $mform->addElement('filemanager', 'attachments', get_string('attachments', 'wiki'), null, page_wiki_edit::$attachmentoptions);
//            $fileinfo = array(
//                'contextid'=>$contextid,
//                'component'=>'mod_wiki',
//                'filearea'=>$filearea,
//                'itemid'=>$fileitemid,
//                );
//
//            $mform->addElement('wikifiletable', 'deleteuploads', get_string('wikifiletable', 'wiki'), null, $fileinfo, $format);
//            $mform->addElement('submit', 'editoption', get_string('upload', 'wiki'), array('id' => 'tags'));
//        }

        if (!empty($CFG->usetags)) {
            $mform->addElement('header', 'tagshdr', get_string('tags', 'tag'));
            $mform->addElement('tags', 'tags', get_string('tags'));
            $mform->setDefault('tags', $tags);
        }

        $buttongroup = array();
        $buttongroup[] =& $mform->createElement('submit', 'editoption', get_string('save', 'wiki'), array('id' => 'save'));
        $buttongroup[] =& $mform->createElement('submit', 'editoption', get_string('preview'), array('id' => 'preview'));
        $buttongroup[] =& $mform->createElement('submit', 'editoption', get_string('cancel'), array('id' => 'cancel'));

        $mform->addGroup($buttongroup, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }

}
