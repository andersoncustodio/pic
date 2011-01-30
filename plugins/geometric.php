<?php
class Geometric {
	function __call($method, $params) {
		if (method_exists(__CLASS__, $method . '_return')) {
			if (is_null($params[1]))
				$this->pic->img = call_user_func_array(array(__CLASS__, $method . '_return'), array($params[0], $this->pic->img));
			else
				return call_user_func_array(array(__CLASS__, $method . '_return'), array($params[0]));
		}
	}
	
	function rectangle_return ($options = null, $pic_img = null) {
		if (!is_array($options)) $options = $this->pic->css_paser($options);
		
		$options = array_merge(array('width' => $pic_img['width'], 'height' => $pic_img['height'], 'opacity' => 100), $options);
		
		$pos = $this->pic->position($options, $pic_img);
		
		$pic_img = $this->pic->imagecolor($options['background'], $options['opacity'], $pic_img);
		
		imagefilledrectangle($pic_img['source'], $pos['x'], $pos['y'],  $pos['x'] + $options['width'], $pos['y'] + $options['height'], $pic_img['imagecolor']);

		return $pic_img;
	}
	
	/*
	function line_return ($options = null, $pic_img = null) {
		if (!is_array($options)) $options = pic_css_paser($options);
		
		$default_options = array('width' => $pic_img['width'], 'height' => $pic_img['height'], 'opacity' => 100);
		$options = array_merge($default_options, $options);
		
		$pos = $this->position($options, $pic_img);
		
		$pic_img = $this->imagecolor($options['background'], $options['opacity'], $pic_img);
		
		imageline($pic_img['source'], $pos['x'], $pos['y'],  $pos['x'] + $options['width'], $pos['y'] + $options['height'], $pic_img['imagecolor']);

		return $pic_img;
	}*/
}