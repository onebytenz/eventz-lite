<?php
/**
 * Template Class.
 * @link       http://onebyte.nz
 * @since      1.0.0.
 *
 * @package    Eventz Lite
 * @subpackage Eventz/includes
 * @author     Craig Sugden - onebyte.nz <info@onebyte.nz>
 */
class Eventz_Lite_Template {
    protected $file;
    protected $values = array();
    public function __construct($file) {
        $this->file = $file;
    }
    public function set($key, $value) {
        $this->values[$key] = $value;
    }
    public function output() {
        if (!file_exists($this->file)) {
            return "Error loading template file ($this->file).";
        }
        $output = file_get_contents($this->file);
        foreach ($this->values as $key => $value) {
            $tagToReplace = "[$key]";
            $output = str_replace($tagToReplace, $value, $output);
        }
        return $output;
    }
}

