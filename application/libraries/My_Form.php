<?php
class My_Form  {
	/*
	 * Initiate Default CI Intance
	 */
	const CONFIG_PATH = 'config/';
	
	private $CI;
	private $_config;
	private $_loadConfig;
	
	/*
	 * Initiate __construct;
	 */
	public function __construct() 
	{
		$this->CI =& get_instance();
		$this->CI->load->helper('form');
		$this->CI->load->library('form_validation');
		$this->lang->load('filename', 'language');
	}
	
	public function setConfig(
		$config = 'form.ini', 
		$section = false )
	{
		$parse_section = '';
		if($section) {
			$parse_section = $section;
			$section = true;
		}
		$this->_config = self::parse(
			APPPATH . self::CONFIG_PATH . $config, 
			$section,
			$parse_section
		);
		return $this;
	}
	
	public function getConfig() 
	{
		if(is_array($this->_config)) {
			return $this->_config;
		}
	}
	
	public function form_open($arSet) 
	{
		echo $this->_config;
	}
	
	public function getElementId($id) {
		if(!isset($this->_config['elements'][$id])) {
			foreach ($this->_config as $config => $val) {
				if($this->_config[$config]['elements'][$id]) {
					$set = $this->_config[$config]['elements'][$id];
				}
			}
		} else {
			$set = $this->_config['elements'][$id];
		}
		return $this->_getFormElement($set, $id);
	}
	
	public function isValid() {
		
	}
	
	protected function _getFormElement($item, $id) {
		if (!isset($item['attributes']['name'])) {
			$item['attributes']['name'] = $id;
		} 
		$generateForm = "form_{$item['type']}";
		if(isset($item['attributes'])) {
			return $generateForm($item['attributes']);
		} else {
			return $generateForm();
		}
	}
	
	
	/**
	 * 
	 * @see http://stackoverflow.com/questions/7480833/ini-file-to-multidimensional-array-in-php
	 * @param string $filename
	 * @return Array config
	 */
    public static function parse(
    	$filename, 
    	$parseSection, 
    	$section = '') 
    {
    	
        $ini_arr = parse_ini_file($filename, $parseSection);
        if ($ini_arr === FALSE) {
            return FALSE;
        }
        if($parseSection && !is_array($section)) {
        	$ini_arr = $ini_arr[$section];
        	self::fix_ini_multi(&$ini_arr);
        } else {
        	foreach ($section as $sec) {
	        	$iniArr[$sec] = $ini_arr[$sec];
	        	self::fix_ini_multi(&$iniArr[$sec]);
        	}
        	$ini_arr = $iniArr;
        }
        
        return $ini_arr;
    }

	/**
	 * 
	 * @see http://stackoverflow.com/questions/7480833/ini-file-to-multidimensional-array-in-php
	 * @param Array $ini_arr
	 * @return Array config
	 */
    
    private static function fix_ini_multi(&$ini_arr) 
    {
        foreach ($ini_arr as $key => &$value) {
            if (is_array($value)) {
                self::fix_ini_multi($value);
            }
            if (strpos($key, '.') !== FALSE) {
                $key_arr = explode('.', $key);
                $last_key = array_pop($key_arr);
                $cur_elem = &$ini_arr;
                foreach ($key_arr AS $key_step) {
                    if (!isset($cur_elem[$key_step])) {
                        $cur_elem[$key_step] = array();
                    }
                    $cur_elem = &$cur_elem[$key_step];
                }
                $cur_elem[$last_key] = $value;
                unset($ini_arr[$key]);
            }
        }
    }
	
}