<?php
/**
 * PIC - [P]HP [I]MG [C]SS
 *
 * Com poucas linhas de código você abre e edita imagens com PHP de forma simples e rápida usando comandos CSS.
 *
 * @author Anderson Custódio de Oliveira <acustodioo@gmail.com>
 * @link https://github.com/acustodioo/pic
 * @since Pic 0.1b
 */
  
define('PATH_PIC_CLASS', dirname(__FILE__) . '/');

class Pic {
	
	/**
	 * Source e informações da imagem
	 */
	var $img = array();
	
	/**
	 * Caminho da imagem original
	 */
	var $src = null;
	
	/**
	 * Formatos permetitos
	 */
	var $mime = array(
		'jpg' => 'image/jpeg',
		'gif' => 'image/gif',
		'png' => 'image/png',
		'bmp' => 'image/x-ms-bmp'
	);
	
	/**
	 * 
	 */
	function __call($method, $params) {
		$plugin_class = substr($method, 0, strpos($method, '_'));
		$plugin_method = substr($method, strpos($method, '_') + 1);
		
		if (file_exists(PATH_PIC_CLASS . 'plugins/' . $plugin_class . '.php'))
			require_once PATH_PIC_CLASS . 'plugins/' . $plugin_class . '.php';
		else
			require_once PATH_PIC_CLASS . 'plugins/' . $plugin_class .'/'. $plugin_class .'.php';
			
		$plugin = ucwords($plugin_class);
		$plugin = new $plugin;
		$plugin->pic = $this;
		
		$return = call_user_func_array(array($plugin, $plugin_method), $params);
		
		if ($return) return $return; else $this->this = $plugin->pic;
	}
	
	/**
	 * 
	 */
	function position ($options = array(), $base = array()) {
		$pos = array('x' => 0, 'y' => 0);
		
		// Definição da posição X do layer, estilo CSS usando position absolute: left ou right
		if ($options['left'] == 'auto' or $options['right'] == 'auto')
		$pos['x'] = (($base['width'] - $options['width']) / 2);
		
		elseif (isset($options['left']))
			$pos['x'] =  $this->percent_to_pixel($options['left'], $base['width']);
		
		elseif(isset($options['right']))
			$pos['x'] = ($base['width'] - $options['width'] -  $this->percent_to_pixel($options['right'], $base['width']));
			
		
		// Definição da posição Y do layer, estilo CSS usando position absolute: top ou bottom
		if ($options['top'] == 'auto' or $options['bottom'] == 'auto')
			$pos['y'] = (($base['height'] - $options['height']) / 2);
			
		elseif (isset($options['top']))
			$pos['y'] =  $this->percent_to_pixel($options['top'], $base['height']);
		
		elseif (isset($options['bottom']))
			$pos['y'] = ($base['height'] - $options['height'] -  $this->percent_to_pixel($options['bottom'], $base['height']));
		
		return $pos;
	}
	
	/**
	 * Color
	 * @link http://www.php.net/manual/en/ref.image.php#63064
	 */
	function color_hex_to_rgb($color = null) {
		if (is_null($color)) return array(255, 255, 255);
		
		$color = str_replace('#', null, $color);
		if (strlen($color) == 3) $color .= $color;
		return array_map('hexdec', explode('|', wordwrap($color, 2, '|', 1)));
	}
	
	/**
	 * 
	 */
	function css_paser($css = null) {
		$css = str_replace(array("\n", "\r", "\t"), null, $css);
		$css = explode(':', str_replace(';', ':', $css));
		
		for ($i = 0; $i < count($css); $i += 2)
			$options[$css[$i]] = trim($css[$i + 1]);
		
		return $options;
	}
	
	/**
	 * 
	 */
	function percent_to_pixel ($unid = null, $measure = null) {
		if (!is_null($measure) and strrpos($unid, '%'))
			return (int) ($measure * (str_replace('%', null, $unid) / 100));
		
		else
			return str_replace('px', null, $unid);
	}

	/**
	 * 
	 */
	function imagecolor ($color = '#FFFFF', $opacity = 100, $pic_img = null) {
		if ($opacity <= 100)
			$opacity = (100 - $opacity) * (127 / 100);
		else
			$opacity = 0;
		
		$color = $this->color_hex_to_rgb($color);
		$pic_img['imagecolor'] = imagecolorallocatealpha($pic_img['source'], $color[0], $color[1], $color[2], $opacity);
		return $pic_img;
	}
	
	/**
	 * opções gerais
	 * em desenvolvimento, no final ficará mais elegante
	 */
	function options_global ($options = null, $pic_img = null) {
		if (isset($options['rotate']))
			$pic_img = $this->rotate($options, $pic_img);
		
		if (isset($options['filter'])) {
			$filters = explode(',', $options['filter']);
			
			foreach ($filters as $filter) {
				$filter = explode(' ', trim($filter));
				$filter_name = $filter[0]; array_shift($filter);
				$pic_img = $this->{'filter_' . $filter_name}($filter, $pic_img);
			}
		}
		
		if (isset($options['flip']))
			$pic_img = $this->flip($options['flip'], $pic_img);
		
		if (isset($options['efect'])) {
			$filters = explode(',', $options['efect']);
			
			foreach ($filters as $filter) {
				$filter = explode(' ', trim($filter));
				$filter_name = $filter[0]; array_shift($filter);
				$pic_img = $this->{'efect_' . $filter_name}($filter, $pic_img);
			}
		}
		
		return $pic_img;
	}
	
	
	/**
	 * Redimensionamento com varias opções
	 */
	function resize($options = null, $pic_img = null) {
		if (!is_array($options)) $options = $this->css_paser($options);
		if (!is_null($pic_img)) $return_img = true; else $pic_img = $this->img;
		
		// Se definir só a largura reajusto o valor da altura para manter a proporção
		if (isset($options['width']) and !isset($options['height'])) {
			$width = $crop_width =  $this->percent_to_pixel($options['width'], $this->img['width']);
			$height = $crop_height = floor($pic_img['height'] / ($pic_img['width'] / $width));
		}
		
		// Se definir só a altura reajusto o valor da largunra para manter a proporção
		elseif (isset($options['height']) and !isset($options['width'])) {
			$height = $crop_height =  $this->percent_to_pixel($options['height'], $this->img['height']);
			$width = $crop_width = floor($pic_img['width'] / ($pic_img['height'] / $height));
		}
		
		// Se os dois foram definidos redimenciono como desejado
		else {
			$width = $crop_width =  $this->percent_to_pixel($options['width'], $this->img['width']);
			$height = $crop_height =  $this->percent_to_pixel($options['height'], $this->img['height']);
		}
		
		// Verifico se tem algum tamanho mínimo ou máximo a ser seguido na hora do corte
		if (isset($options['max-height'])) {
			$options['max-height'] =  $this->percent_to_pixel($options['max-height'], $this->img['height']);
			if ($height > $options['max-height']) $crop_height = $options['max-height'];
		}
		
		elseif (isset($options['max-width'])) {
			$options['max-width'] =  $this->percent_to_pixel($options['max-width'], $this->img['width']);
			if ($width > $options['max-width']) $crop_width = $options['max-width'];
		}
		
		// Para que a posição seja passada corretamente
		$options['width'] = $crop_width;
		$options['height'] = $crop_height;
		
		$pos = $this->position($options, array('width' => $width, 'height' => $height));
		
		$tmp = imagecreatetruecolor($crop_width, $crop_height);
		imagecopyresampled($tmp, $pic_img['source'], -$pos['x'], -$pos['y'], 0, 0, $width, $height, $pic_img['width'], $pic_img['height']);
		imagedestroy($pic_img['source']);

		$pic_img = array('width' => $crop_width, 'height' => $crop_height, 'source' => $tmp, 'format' => $pic_img['format']);
		$pic_img = $this->options_global($options, $pic_img);
		
		if (isset($return_img)) return $pic_img; else $this->img = $pic_img;
	}
	
	/**
	 * Se a imagem for maior na vertical inverte a largura com a altura, bom para fotos
	 */
	function photo ($options = null, $pic_img = null) {
		if (!is_array($options)) $options = $this->css_paser($options);
		if (!is_null($pic_img)) $return_img = true; else $pic_img = $this->img;
		
		$options = array_merge(array('width' => 600, 'height' => 400, 'overflow' => false), $options);
		
		if ($pic_img['width'] > $pic_img['height']) {
			if ($options['overflow'] == 'hidden')
				$options['max-height'] =  $this->percent_to_pixel($options['height'], $pic_img['height']);
			
			unset($options['height']);
		}
		
		elseif ($pic_img['width'] < $pic_img['height']) {
			$options = array_merge($options, array('width' => $options['height'], 'height' => $options['width']));
			
			if ($options['overflow'] == 'hidden')
				$options['max-width'] =  $this->percent_to_pixel($options['width'], $pic_img['width']);
			
			unset($options['width']);
		}
		
		$pic_img = $this->resize($options, $pic_img);
		if (isset($return_img)) return $pic_img; else $this->img = $pic_img;
	}
	
	/**
	 *
	 */
	function flip($type = 0, $pic_img = null){
		if (!is_null($pic_img)) $return_img = true; else $pic_img = $this->img;
		
		$tmp = imagecreatetruecolor($pic_img['width'], $pic_img['height']);
		switch($type){
			case 'vertical':
				for($i = 0; $i < $pic_img['height']; $i++)
					imagecopy($tmp, $pic_img['source'], 0, ($pic_img['height'] - $i - 1), 0, $i, $pic_img['width'], 1);
				
				imagedestroy($pic_img['source']);
				$pic_img['source'] = $tmp;
			break;
			case 'horizontal':
				for($i = 0; $i < $pic_img['width']; $i++)
					imagecopy($tmp, $pic_img['source'], ($pic_img['width'] - $i - 1), 0, $i, 0, 1, $pic_img['height']);
				
				imagedestroy($pic_img['source']);
				$pic_img['source'] = $tmp;
			break;
			case 'vertical horizontal':
			case 'hotizontal vertical':
				$pic_img = $this->flip('vertical', $pic_img);
				$pic_img = $this->flip('horizontal', $pic_img);
			break;
		}
		
		if (isset($return_img)) return $pic_img; else $this->img = $pic_img;
	}
	
	/**
	 * 
	 */
	 
	function create ($options = null, $return_img = false) {
		if (!is_array($options)) $options = $this->css_paser($options);
		
		$options = array_merge(array('width' => 600, 'height' => 400, 'background' => 'transparent'), $options);
		
		$pic_img = array(
			'source' => imagecreatetruecolor($options['width'], $options['height']),
			'width' => $options['width'],
			'height' => $options['height'],
			'format' => 'png'
		);
		
		if ($options['background'] == 'transparent') {
			$pic_img = $this->imagecolor('#000000', $options['opacity'], $pic_img);
			imagecolortransparent($pic_img['source'], $pic_img['imagecolor']);
		}
		
		else {
			$pic_img = $this->imagecolor($options['background'], $options['opacity'], $pic_img);
			imagefill($pic_img['source'], 10, 10, $pic_img['imagecolor']);
		}
		
		unset($pic_img['imagecolor']);
		if ($return_img == true) return $pic_img; else $this->img = $pic_img;
	}
	
	/**
	 * Corta imagem tamanho e local especifico
	 */
	function crop($options = null, $pic_img = null) {
		if (!is_array($options)) $options = $this->css_paser($options);
		if (!is_null($pic_img)) $return_img = true; else $pic_img = $this->img;
		
		$options = array_merge(array('width' => $pic_img['width'], 'height' => $pic_img['height']), $options);
		
		$options['width'] =  $this->percent_to_pixel($options['width'], $pic_img['width']);
		$options['height'] =  $this->percent_to_pixel($options['height'], $pic_img['height']);
		
		$pos = $this->position($options, $pic_img);
		
		$tmp = imagecreatetruecolor($options['width'], $options['height']);
		imagecopyresampled($tmp, $pic_img['source'], 0, 0, $pos['x'], $pos['y'], $pic_img['width'], $pic_img['height'], $pic_img['width'], $pic_img['height']);
		imagedestroy($pic_img['source']);
		
		$pic_img = array(
			'source' => $tmp,
			'width' => $options['width'],
			'height' => $options['height']
		);
		
		$pic_img = $this->options_global($options, $pic_img);
		if (isset($return_img)) return $pic_img; else $this->img = $pic_img;
	}

	/**
	 * 
	 */
	function thumbnail($options = null, $pic_img = null) {
		if (!is_array($options)) $options = $this->css_paser($options);
		if (!is_null($pic_img)) $return_img = true; else $pic_img = $this->img;
		
		$options = array_merge(array('width' => 90, 'height' => 90), $options);
		
		$options['width'] =  $this->percent_to_pixel($options['width'], $this->img['width']);
		$options['height'] =  $this->percent_to_pixel($options['height'], $this->img['height']);
		
		if (floor($pic_img['width'] / ($pic_img['height'] / $options['height'])) < $options['width']){
			$options['max-height'] = $options['height'];
			unset($options['height']);
			$pic_img = $this->resize($options, $pic_img);
		}
		else {
			$options['max-width'] = $options['width'];
			unset($options['width']);
			$pic_img = $this->resize($options, $pic_img);
		}
		
		if (isset($return_img)) return $pic_img; else $this->img = $pic_img;
	}
	
	/**
	 * Gira a imagem
	 */
	function rotate($options = null, $pic_img = null) {
		if (!is_array($options)) $options = $this->css_paser($options);
		
		$options = array_merge(array('background' => '#FFFFFF'), $options);
		
		$pic_img = $this->imagecolor($options['background'], $options['opacity'], $pic_img);
		
		$pic_img['source'] = imagerotate($pic_img['source'], $options['rotate'], $pic_img['imagecolor'], 0);
		
		unset($pic_img['imagecolor']);
		
		$pic_img['width'] = imagesx($pic_img['source']);
		$pic_img['height'] = imagesy($pic_img['source']);
		
		return $pic_img;
	}
	
	/**
	 * Em desenvolvimento, baseado na opção canvas do photoshop
	 */
	function canvas($options = null, $pic_img = null) {
		if (!is_array($options)) $options = $this->css_paser($options);
		if (!is_null($pic_img)) $return_img = true; else $pic_img = $this->img;
		
		$pic_img = $this->img;
		
		$options = array_merge(array('width' => $pic_img['width'], 'height' => $pic_img['height'], 'background' => '#FFF'), $options);
		
		$canvas = array(
			'width' =>  $this->percent_to_pixel($options['width'], $pic_img['width']),
			'height' =>  $this->percent_to_pixel($options['height'], $pic_img['height'])
		);
		
		if (isset($options['relative'])) {
			$canvas['width'] += $pic_img['width'];
			$canvas['height'] += $pic_img['height'];
		}
		
		$options['width'] = $pic_img['width'];
		$options['height'] = $pic_img['height'];
		
		$pos = $this->position($options, $canvas);
		
		$tmp = imagecreatetruecolor($canvas['width'], $canvas['height']);
		$color = $this->color_hex_to_rgb($options['background']);
		$color = imagecolorallocate($tmp, $color[0], $color[1], $color[2]);
		imagefilledrectangle($tmp, 0, 0, $canvas['width'], $canvas['height'], $color);
		imagecopyresampled($tmp, $pic_img['source'], $pos['x'], $pos['y'], 0, 0, $pic_img['width'], $pic_img['height'], $pic_img['width'], $pic_img['height']);
		imagedestroy($pic_img['source']);
		
		$pic_img = array(
			'width' => $canvas['width'],
			'height' => $canvas['height'],
			'source' => $tmp
		);
		
		$this->img = $this->options_global($options, $pic_img);
		
		if (isset($return_img)) return $pic_img; else $this->img = $pic_img;
	}
	
	/**
	 *
	 */
	function layer ($src = null, $options = array(), $pic_img = null) {
		if (!is_array($options)) $options = $this->css_paser($options);
		if (!is_null($pic_img)) $return_img = true; else $pic_img = $this->img;
		
		$options = array_merge(array('opacity' => 100), $options);
		
		if (is_array($src))
			$img = $src;
		
		else
			$img = $this->imagecreate($src);
		
		if (!$img) return false;

		if ($img['format'] != 'png' and $img['format'] != 'gif')
			$img = $this->resize($options, $img);
			
		else
			if ($options['rotate']) $img = $this->rotate($options, $img);
	
		
		$options['width'] = $img['width'];
		$options['height'] = $img['height'];
		
		$pos = $this->position($options, $pic_img);
		
		if ($img['format'] == 'png') {
			require_once PATH_PIC_CLASS . 'core/imagecopymerge_alpha.function.php';
			imagecopymerge_alpha($pic_img['source'], $img['source'], $pos['x'], $pos['y'], 0, 0, $img['width'], $img['height'], $options['opacity']);
		}
		
		else
			imagecopymerge($pic_img['source'], $img['source'], $pos['x'], $pos['y'], 0, 0, $img['width'], $img['height'], $options['opacity']);
		
		imagedestroy($img['source']);
		if (isset($return_img)) return $pic_img; else $this->img = $pic_img;
	}
	
	/**
	 * Imagecreate
	 */
	function imagecreate($src) {
		if (!$info = @getimagesize($src)) return false;
		
		$format = str_replace(array('.', 'e'), null, image_type_to_extension($info[2]));
		
		switch ($format) {
			case 'jpg': $img = imagecreatefromjpeg($src); break;
			case 'png':
				$img = imagecreatefrompng($src);
				imagealphablending($img, false);
				imagesavealpha($img, true);
			break;
			case 'gif': $img = imagecreatefromgif($src); break;
			case 'bmp':
				require_once PATH_PIC_CLASS . 'core/imagecreatefrombmp.function.php';
				$img = imagecreatefrombmp($src);
			break;
		}
		
		return array(
			'source' => $img,
			'width'  => $info[0],
			'height' => $info[1],
			'format' => $format
		);
	}
	
	
	/**
	 * Abre imagem pra edição
	 */
	function open($src = null) {
		if (!is_null($src)) $this->src = $src;
		$this->img = $this->imagecreate($src);
		if ($this->img['format']) return $this;
	}
	
	/**
	 * Reabre a imagem, dando empressão de desfazer todas modificações
	 */
	function reset() {
		$this->open($this->src);
	}
	
	/**
	 * 
	 */
	function filter_name($name = null) {
		$default = 'image.' . $this->img['format'];
		
		if(!is_null($name)) {
			$format = strtolower(str_replace('.', null, strrchr($name, '.')));
			
			if (isset($this->mime[$format]))
				$this->img['format'] = $format;
			else
				$name .= '.' . $this->img['format'];
		}
		
		else
			$name = $default;
		
		return $name;
	}
	
	/**
	 * Image
	 */
	function image($save = null, $qualite = 90) {
		imageinterlace($this->img['source'], true);
		
		switch ($this->img['format']) {
			case 'jpg': imagejpeg($this->img['source'], $save, $qualite); break;
			case 'png': imagepng($this->img['source'], $save); break;
			case 'gif': imagegif($this->img['source'], $save); break;
			case 'bmp':
				require_once PATH_PIC_CLASS . 'core/imagebmp.function.php';
				imagebmp($this->img['source'], $save);
			break;
		}
	}
	
	/**
	 * Mostra imagem
	 */
	function display($format = null, $qualite = 90) {
		if (!is_null($format)) $this->img['format'] = $format;
		header('Content-type: ' . $this->mime[$this->img['format']]);
		$this->image(null, $qualite);
		exit;
	}
	
	/**
	 * Download da imagem
	 */
	function download($name = null, $qualite = 90) {
		$name = $this->filter_name($name);
		header('Content-type: ' . $this->mime[$this->img['format']]);
		header('Content-Disposition: attachment; filename="' . $name . '"');
		readfile($this->image(null, $qualite));
		imagedestroy($this->img['source']);
		exit;
	}
	
	/**
	 * Salva imagem
	 */
	function save($name = null, $qualite = 90, $chmod = 0777){
		if (is_null($qualite)) $qualite = 90;
		$name = $this->filter_name($name);
		if (!is_dir($dir = dirname($name))) mkdir($dir, $chmod, true);
		if ($this->image($name, $qualite)) chmod($name, $chmod);
	}

	/**
	 * Apaga a imagem da memória
	 */
	function clear() {
		imagedestroy($this->img['source']);
	}
	
	/**
	 * Apaga a imagem aberta se carregada no servidor e limpa da memória
	 */
	function clear_all() {
		imagedestroy($this->img['source']);
		if (file_exists($this->src)) unlink($this->src);
	}
	
}