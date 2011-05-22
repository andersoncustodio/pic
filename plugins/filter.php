<?php
class Filter {
	function __call($method, $params) {
		if (!isset($params[1])) {
			if (!isset($params[0])) $params[0] = null;
			$this->pic->img = call_user_func_array(array(__CLASS__, 'img_filter'), array($method, $params[0], $this->pic->img));
		} else
			return call_user_func_array(array(__CLASS__, 'img_filter'), array($method, $params[0], $params[1]));
	}
	
	var $filters = array(
		'negate' => IMG_FILTER_NEGATE,
		'grayscale' => IMG_FILTER_GRAYSCALE,
		'brightness' => IMG_FILTER_BRIGHTNESS,
		'contrast' => IMG_FILTER_CONTRAST,
		'colorize' => IMG_FILTER_COLORIZE,
		'edgedetect' => IMG_FILTER_EDGEDETECT,
		'emboss' => IMG_FILTER_EMBOSS,
		'gaussian-blur' => IMG_FILTER_GAUSSIAN_BLUR,
		'selective-blur' => IMG_FILTER_SELECTIVE_BLUR,
		'mean-removal' => IMG_FILTER_MEAN_REMOVAL,
		'smooth' => IMG_FILTER_SMOOTH,
		'pixelate' => IMG_FILTER_PIXELATE
	);

	function img_filter ($filter, $options, $pic_img = null) {
		
		switch ($this->filters[$filter]) {
			case IMG_FILTER_COLORIZE:
				imagefilter($pic_img['source'], $this->filters[$filter], $options[0], $options[1], $options[2], $options[3]);
			break;
			
			case IMG_FILTER_PIXELATE:
				imagefilter($pic_img['source'], $this->filters[$filter], $options[0], $options[1]);
			break;
			
			case IMG_FILTER_BRIGHTNESS:
			case IMG_FILTER_CONTRAST:
			case IMG_FILTER_SMOOTH:
				imagefilter($pic_img['source'], $this->filters[$filter], $options[0]);
			break;
			
			default:
				imagefilter($pic_img['source'], $this->filters[$filter]);
			break;
		}
			
		return $pic_img;
	}
}
