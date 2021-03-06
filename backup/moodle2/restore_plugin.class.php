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
 * @package    moodlecore
 * @subpackage backup-moodle2
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Class implementing the plugins support for moodle2 restore
 *
 * TODO: Finish phpdocs
 * TODO: Add support for declaring decode_contents (not decode_rules)
 */
abstract class restore_plugin {

    protected $plugintype;
    protected $pluginname;
    protected $connectionpoint;
    protected $step;
    protected $task;

    public function __construct($plugintype, $pluginname, $step) {
        $this->plugintype = $plugintype;
        $this->pluginname = $pluginname;
        $this->step          = $step;
        $this->task          = $step->get_task();
        $this->connectionpoint = '';
    }

    public function define_plugin_structure($connectionpoint) {
        if (!$connectionpoint instanceof restore_path_element) {
            throw new restore_step_exception('restore_path_element_required', $connectionpoint);
        }

        $paths = array();
        $this->connectionpoint = $connectionpoint;
        $methodname = 'define_' . basename($this->connectionpoint->get_path()) . '_plugin_structure';

        if (method_exists($this, $methodname)) {
            if ($bluginpaths = $this->$methodname()) {
                foreach ($bluginpaths as $path) {
                    $path->set_processing_object($this);
                    $paths[] = $path;
                }
            }
        }
        return $paths;
    }

    /**
     * after_execute dispatcher for any restore_plugin class
     *
     * This method will dispatch execution to the corresponding
     * after_execute_xxx() method when available, with xxx
     * being the connection point of the instance, so plugin
     * classes with multiple connection points will support
     * multiple after_execute methods, one for each connection point
     */
    public function launch_after_execute_methods() {
        // Check if the after_execute method exists and launch it
        $afterexecute = 'after_execute_' . basename($this->connectionpoint->get_path());
        if (method_exists($this, $afterexecute)) {
            $this->$afterexecute();
        }
    }

// Protected API starts here

// restore_step/structure_step/task wrappers

    protected function get_restoreid() {
        if (is_null($this->task)) {
            throw new restore_step_exception('not_specified_restore_task');
        }
        return $this->task->get_restoreid();
    }

    /**
     * To send ids pairs to backup_ids_table and to store them into paths
     *
     * This method will send the given itemname and old/new ids to the
     * backup_ids_temp table, and, at the same time, will save the new id
     * into the corresponding restore_path_element for easier access
     * by children. Also will inject the known old context id for the task
     * in case it's going to be used for restoring files later
     */
    protected function set_mapping($itemname, $oldid, $newid, $restorefiles = false, $filesctxid = null, $parentid = null) {
        $this->step->set_mapping($itemname, $oldid, $newid, $restorefiles, $filesctxid, $parentid);
    }

    /**
     * Returns the latest (parent) old id mapped by one pathelement
     */
    protected function get_old_parentid($itemname) {
        return $this->step->get_old_parentid($itemname);
    }

    /**
     * Returns the latest (parent) new id mapped by one pathelement
     */
    protected function get_new_parentid($itemname) {
        return $this->step->get_new_parentid($itemname);
    }

    /**
     * Return the new id of a mapping for the given itemname
     *
     */
    protected function get_mappingid($itemname, $oldid) {
        return $this->step->get_mappingid($itemname, $oldid);
    }

    /**
     * Return the complete mapping from the given itemname, itemid
     */
    protected function get_mapping($itemname, $oldid) {
        return $this->step->get_mapping($itemname, $oldid);
    }

    /**
     * Add all the existing file, given their component and filearea and one backup_ids itemname to match with
     */
    protected function add_related_files($component, $filearea, $mappingitemname, $filesctxid = null, $olditemid = null) {
        $this->step->add_related_files($component, $filearea, $mappingitemname, $filesctxid, $olditemid);
    }

    /**
     * Apply course startdate offset based in original course startdate and course_offset_startdate setting
     * Note we are using one static cache here, but *by restoreid*, so it's ok for concurrence/multiple
     * executions in the same request
     */
    protected function apply_date_offset($value) {
        return $this->step->apply_date_offset($value);
    }

    /**
     * Simple helper function that returns the name for the restore_path_element
     * It's not mandatory to use it but recommended ;-)
     */
    protected function get_namefor($name = '') {
        $name = $name !== '' ? '_' . $name : '';
        return $this->plugintype . '_' . $this->pluginname . $name;
    }

    /**
     * Simple helper function that returns the base (prefix) of the path for the restore_path_element
     * Useful if we used get_recommended_name() in backup. It's not mandatory to use it but recommended ;-)
     */
    protected function get_pathfor($path = '') {
        $path = trim($path, '/') !== '' ? '/' . trim($path, '/') : '';
        return $this->connectionpoint->get_path() . '/' .
               'plugin_' . $this->plugintype . '_' .
               $this->pluginname . '_' . basename($this->connectionpoint->get_path()) . $path;
    }
}
