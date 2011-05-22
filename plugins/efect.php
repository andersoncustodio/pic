<?php
class Efect {
	function __call($method, $params) {
		if (method_exists(__CLASS__, $method . '_return')) {
			if (!isset($params[1])) {
				if (!isset($params[0])) $params[0] = null;
				$this->pic->img = call_user_func_array(array(__CLASS__, $method . '_return'), array($params[0], $this->pic->img));
			} else
				return call_user_func_array(array(__CLASS__, $method . '_return'), array($params[0], $params[1]));
		}
	}
	
	function sepia_return ($options, $pic_img = null) {
		$pic_img = $this->pic->filter_grayscale(null, $pic_img);
		$pic_img = $this->pic->filter_colorize(array(90, 60, 40), $pic_img);
		return $pic_img;
	}
	
	function drawing_return ($options, $pic_img = null) {
		$pic_img = $this->pic->filter_grayscale(null, $pic_img);
		$pic_img = $this->pic->filter_edgedetect(null, $pic_img);
		$pic_img = $this->pic->filter_brightness(array(120), $pic_img);
		return $pic_img;
	}
	
	function teste_return ($options, $pic_img = null) {
		
		
		
		//$pic_img = $this->pic->filter_grayscale(null, $pic_img);
		//$pic_img = $this->pic->filter_edgedetect(null, $pic_img);
		//$pic_img = $this->pic->filter_colorize(array(0, 60, 40), $pic_img);
		//$pic_img = $this->pic->filter_brightness(array(50), $pic_img);
		//$pic_img = $this->pic->filter_negate(null, $pic_img);
		return $pic_img;
	}
}
