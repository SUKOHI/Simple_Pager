<?php

class Simple_Pager {

	private $_current_page;
	private $_per_page;
	private $_max_page = 1;
	private $_max_pager_cout;
	private $_total;
	private $_generate_links = array();
	
	public function setParams($params) {
		
		$this->_page_key = $params['argument_key'];
		$this->_current_page = $this->getDefaultNumber($_GET[$this->_page_key], 1);
		$this->_per_page = $this->getDefaultNumber($params['per_page'], 10);
		$this->_max_pager_cout = $this->getDefaultNumber($params['pager_count'], 10) - 1;
		$this->_total = intval($params['total']);
		
		if($this->_per_page > 0 && $this->_total > 0) {
			
			$this->_max_page = ceil($this->_total/$this->_per_page);
			
		}
		
		$this->_generate_links = array();
		
	}
	
	public function currentPage() {
		
		return $this->_current_page;
		
	}
	
	public function previous($text, $properties=array()) {
		
		if($this->_current_page > 1) {
			
			$properties['id'] = 'simple_pager_prev';
			$link = $this->getLinkTag($text, ($this->_current_page-1), $properties);
			$this->_generate_links[] = $link;
			return $link;
			
		}
		
		return '';
		
	}
	
	public function next($text, $properties=array()) {
		
		if($this->_current_page < $this->_max_page) {
				
			$properties['id'] = 'simple_pager_prev';
			$link = $this->getLinkTag($text, ($this->_current_page+1), $properties);
			$this->_generate_links[] = $link;
			return $link;
				
		}
		
		return '';
		
	}
	
	public function min($properties=array()) {
		
		if($this->_current_page != 1) {
			
			$properties['id'] = 'simple_pager_min';
			$link = $this->getLinkTag('[1]', 1, $properties);
			$this->_generate_links[] = $link;
			return $link;
			
		}
		
		return '';
		
	}
	
	public function max($properties=array()) {
		
		if($this->_current_page != $this->_max_page) {

			$properties['id'] = 'simple_pager_max';
			$link = $this->getLinkTag('['. $this->_max_page .']', $this->_max_page, $properties);
			$this->_generate_links[] = $link;
			return $link;
				
		}
		
		return '';
		
	}
	
	public function pageNumbers($property_data, $delimiter='&nbsp;') {
		
		$number_links = array();
		$start = $end = 0;
		$current_properties = $property_data['current'];
		$numbers_properties = $property_data['numbers'];
		
		$remainder = $this->_max_pager_cout % 2;
		$prev_page_count = floor($this->_max_pager_cout/2);
		$next_page_count = $prev_page_count + $remainder;
		
		$start = $this->_current_page - $prev_page_count;
		$end = $this->_current_page + $next_page_count;
		
		if($start < 1) {
			
			$end += 1 - $start;
			
		}
		
		if($end > $this->_max_page) {
			
			$start -= abs($this->_max_page - $end);
			
		}
		
		for ($i = $this->getMinNumber($start); $i <= $this->getMaxNumber($end); $i++) {
			
			if($i == $this->_current_page) {
				
				$number_links[] = '<span'. $this->getProperty($current_properties) .'>'. $i .'</span>';
				
			} else {
				
				$properties['id'] = 'simple_pager_number_'. $i;
				$number_links[] = $link = $this->getLinkTag($i, $i, $numbers_properties);
				
			}
			
		}
		
		$link = implode($delimiter, $number_links);
		
		if(!empty($link)) {
			
			$this->_generate_links[] = $link;
			
		}
		
		return $link;
		
	}
	
	private function getMinNumber($target_number) {
		
		return ($target_number < 1) ? 1 : $target_number;
		
	}
	
	private function getMaxNumber($target_number) {

		return ($target_number > $this->_max_page) ? $this->_max_page : $target_number;
		
	}
	
	
	public function all($delimiter='&nbsp;') {
		
		return implode($delimiter, $this->_generate_links);
		
	}
	
	private function getLinkTag($text, $page, $properties) {
		
		$property = $this->getProperty($properties);
		$params = $_GET;
		$params[$this->_page_key] = $page;
		return '<a href="'. $_SERVER['SCRIPT_NAME'] .'?'. http_build_query($params) .'"'. $property .'>'. $text .'</a>';
		
	}
	
	private function getProperty($properties) {
		
		$property = '';
		
		foreach ($properties as $key =>  $value) {
				
			$property .= ' '. $key .'="'. $value .'"';
				
		}
		
		return $property;
		
	}
	
	private function getDefaultNumber($number, $default) {
		
		return (intval($number) == 0) ? $default : intval($number);
		
	}
	
}
/*** Example

	require 'simple_pager.php';
	
	$sp = new Simple_Pager();
	$sp->setParams(array(
	
			'argument_key' => 'p',
			'per_page' => 7, 
			'pager_count' => 7, 
			'total' => 100
			
	));
	echo $page = $sp->currentPage();
	
	echo $sp->previous('Prev Text', array(
			'class' => 'prev'
	));
	
	echo $sp->min(array(
			'class' => 'min'
	));
	
	echo $sp->pageNumbers(array(
			
		'current' => array(
				'style' => 'font-weight:bold;'
		), 
		'numbers' => array(
				'class' => 'numbers'
		)
			
	), '&nbsp;');		// $delimiter is skippable (Default: &nbsp;)
	
	echo $sp->max(array(
			'class' => 'max'
	));
	
	echo $sp->next('Next Text', array(
			'class' => 'next'
	));
	
	echo '<hr>'."\n";
	echo $sp->all();		// $delimiter is skippable (Default: &nbsp;)

***/
