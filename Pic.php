<?php
/**
 * PIC - [P]HP [I]MG [C]SS
 *
 * Com poucas linhas de código você abre, edita e salva imagens com PHP
 * de forma simples e rápida usando comandos CSS.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 *
 * @copyright (C) Anderson C. Oliveira <acustodioo@gmail.com>, 2011
 */

define('PATH_PIC_CLASS', dirname(__FILE__) . '/');

class Pic {

    /**
    * Source e informações da imagem
    */
    public $img = array();

    /**
    * Caminho da imagem original
    */
    private $src = null;

    /**
    * Formatos permititos
    */
    private $mime = array(
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'png' => 'image/png',
        'bmp' => 'image/x-ms-bmp'
    );

    /**
    * Atalhos amigáveis para os filtros
    */
    private $filters = array(
        'negate' => 0,            # IMG_FILTER_NEGATE
        'grayscale' => 1,         # IMG_FILTER_GRAYSCALE
        'brightness' => 2,        # IMG_FILTER_BRIGHTNESS
        'contrast' => 3,          # IMG_FILTER_CONTRAST
        'colorize' => 4,          # IMG_FILTER_COLORIZE
        'edgedetect' => 5,        # IMG_FILTER_EDGEDETECT
        'emboss' => 6,            # IMG_FILTER_EMBOSS
        'gaussian-blur' => 7,     # IMG_FILTER_GAUSSIAN_BLUR
        'selective-blur' => 8,    # IMG_FILTER_SELECTIVE_BLUR
        'mean-removal' => 9,      # IMG_FILTER_MEAN_REMOVAL
        'smooth' => 10,           # IMG_FILTER_SMOOTH
        'pixelate' => 11          # IMG_FILTER_PIXELATE
    );


    public function Pic($src = null)
    {
        if ($src != null) $this->open($src);
    }

    /**
    *
    */
    private function position ($options = array(), $base = array()) {
        $pos = array('x' => 0, 'y' => 0);

        // Defino a posição X (left ou right)
        if ((isset($options['left']) and $options['left'] == 'auto')
            or (isset($options['right']) and $options['right'] == 'auto'))
            $pos['x'] = (($base['width'] - $options['width']) / 2);

        elseif (isset($options['left']))
            $pos['x'] =  (int) $options['left'];

        elseif(isset($options['right']))
            $pos['x'] = ($base['width'] - $options['width'] -  (int) $options['right']);

        // Defino a posição Y (top ou bottom)
        if ((isset($options['top']) and $options['top'] == 'auto')
            or (isset($options['bottom']) and $options['bottom'] == 'auto'))
            $pos['y'] = (($base['height'] - $options['height']) / 2);

        elseif (isset($options['top']))
            $pos['y'] =  (int) $options['top'];

        elseif (isset($options['bottom']))
            $pos['y'] = ($base['height'] - $options['height'] -  (int) $options['bottom']);

        return $pos;
    }

    /**
    * Color
    * @link http://www.php.net/manual/en/ref.image.php#63064
    */
    private function hexrgb ($color = null) {
        if (is_null($color)) return array(255, 255, 255);
        $color = str_replace('#', null, $color);
        if (strlen($color) == 3) $color .= $color;
        return array_map('hexdec', explode('|', wordwrap($color, 2, '|', 1)));
    }

    /**
    *
    */
    private function pixel (&$unid = null, $measure = null) {
        if (!is_numeric($unid)) {
            if (strpos($unid, 'px'))
                $unid = (int) $unid;
            elseif (strpos($unid, '%'))
                $unid = round($measure * ((float) $unid * 0.01));
            elseif (strpos($unid, 'em'))
                $unid = round($measure * (float) $unid);
        }
    }

    /**
    *
    */
    private function imagecolor ($options) {
        if ($options['opacity'] <= 100)
            $opacity = (100 - $options['opacity']) * 1.27;
        else
            $opacity = 0;

            $color = $this->hexrgb($options['background']);
            $this->img['imagecolor'] = imagecolorallocatealpha($this->img['source'],
                $color[0], $color[1], $color[2], $opacity);
    }

    /**
    *
    */
    public function background($background = '#FFF') {
        if ($this->img['background'] == 'transparent') {

            $this->img['background'] = $background;

            $tmp = $this->imagebase($this->img['width'], $this->img['height']);

            imagecopyresampled($tmp, $this->img['source'], 0, 0, 0, 0, $this->img['width'],
                $this->img['height'], $this->img['width'], $this->img['height']);

            imagedestroy($this->img['source']);

            $this->img['source'] = $tmp;
        }
    }

    /**
    * @link http://php.net/manual/en/function.imagesavealpha.php
    * @link http://stefangabos.ro/php-libraries/zebra-image/
    */
    private function imagebase($width, $height) {
        $base = imagecreatetruecolor($width, $height);

        if ($this->img['format'] == 'png' and $this->img['background'] == 'transparent') {
            imagealphablending($base, false);

            $transparent = imagecolorallocatealpha($base, 0, 0, 0, 127);
            imagefill($base, 0, 0, $transparent);
            imagesavealpha($base, true);
        }

        elseif ($this->img['format'] == 'gif' and $this->img['background'] == 'transparent'
            and $this->img['transparent']['color_index'] >= 0) {

            $transparent_color = imagecolorallocate(
                $base,
                $this->img['transparent']['color']['red'],
                $this->img['transparent']['color']['green'],
                $this->img['transparent']['color']['blue']
            );

            imagefill($base, 0, 0, $transparent_color);
            imagecolortransparent($base, $transparent_color);
        }

        else {
            $background = $this->hexrgb($this->img['background']);
            $background = imagecolorallocate($base, $background[0], $background[1], $background[2]);
            imagefill($base, 0, 0, $background);
        }

        return $base;
    }

    /**
    * Redimensionamento com varias opções
    */
    public function resize(array $options)
    {
        // Se definir só a largura reajusto o valor da altura para manter a proporção
        if (isset($options['width']) and !isset($options['height'])) {
            $this->pixel($options['width'], $this->img['width']);
            $options['height'] = floor($this->img['height'] / ($this->img['width'] / $options['width']));
        }

        // Se definir só a altura reajusto o valor da largura para manter a proporção
        elseif (isset($options['height']) and !isset($options['width'])) {
            $this->pixel($options['height'], $this->img['height']);
            $options['width'] = floor($this->img['width'] / ($this->img['height'] / $options['height']));
        }

        // Se os dois foram definidos redimensiono como desejado
        elseif (isset($options['height']) and isset($options['width'])) {
            $this->pixel($options['width'], $this->img['width']);
            $this->pixel($options['height'], $this->img['height']);
        }

        // Se nenhum foi definido, mantenho o tamanho atual
        else {
            $options['width'] = $this->img['width'];
            $options['height'] = $this->img['height'];
        }

        $width = $options['width'];
        $height = $options['height'];

        // Verifico se foi definido algum limiete para o canvas
        if (isset($options['canvas-height'])) {
            $this->pixel($options['canvas-height'], $this->img['height']);
            if ($height > $options['canvas-height']) $options['height'] = $options['canvas-height'];
        }

        elseif (isset($options['canvas-width'])) {
            $this->pixel($options['canvas-width'], $this->img['width']);
            if ($width > $options['canvas-width']) $options['width'] = $options['canvas-width'];
        }

        $pos = $this->position($options, array('width' => $width, 'height' => $height));
        $tmp = $this->imagebase($options['width'], $options['height']);

        imagecopyresampled($tmp, $this->img['source'], -$pos['x'], -$pos['y'],
            0, 0, $width, $height, $this->img['width'], $this->img['height']);

        imagedestroy($this->img['source']);

        $this->img['source'] = $tmp;
        $this->img['width'] = $options['width'];
        $this->img['height'] = $options['height'];
    }

    /**
    * Se a imagem for maior na vertical inverte a largura com a altura, bom para fotos
    */
    public function photo ($options = array()) {
        $options = array_merge(array('width' => 600, 'height' => 400,
            'overflow' => 'hidden'), $options);

        if ($this->img['width'] > $this->img['height']) {
            if ($options['overflow'] == 'hidden')
                $options['canvas-height'] =  $options['height'];

            unset($options['height']);
        }

        elseif ($this->img['width'] < $this->img['height']) {
            $options = array_merge($options, array('width' => $options['height'],
                'height' => $options['width']));

            if ($options['overflow'] == 'hidden')
                $options['canvas-width'] = $options['width'];

            unset($options['width']);
        }

        $this->resize($options);
    }

    /**
    *
    */
    public function flip($type = 'h'){
        $tmp = imagecreatetruecolor($this->img['width'], $this->img['height']);
        switch($type){
            case 'v':
                for($i = 0; $i < $this->img['height']; $i++)
                    imagecopy($tmp, $this->img['source'], 0, ($this->img['height'] - $i - 1),
                        0, $i, $this->img['width'], 1);

                imagedestroy($this->img['source']);
                $this->img['source'] = $tmp;
            break;
            case 'h':
                for($i = 0; $i < $this->img['width']; $i++)
                    imagecopy($tmp, $this->img['source'], ($this->img['width'] - $i - 1),
                        0, $i, 0, 1, $this->img['height']);

                imagedestroy($this->img['source']);
                $this->img['source'] = $tmp;
            break;
            case 'vh':
            case 'hv':
                $this->flip('v');
                $this->flip('h');
            break;
        }
    }

    /**
    * Corta imagem com tamanho e local especifico
    */
    public function crop($options = array()) {
        $options = array_merge(array('width' => $this->img['width'],
            'height' => $this->img['height']), $options);

        $this->pixel($options['width'], $this->img['width']);
        $this->pixel($options['height'], $this->img['height']);

        $pos = $this->position($options, $this->img);

        $tmp = imagecreatetruecolor($options['width'], $options['height']);
        imagecopyresampled($tmp, $this->img['source'], 0, 0, $pos['x'], $pos['y'],
            $this->img['width'], $this->img['height'], $this->img['width'], $this->img['height']);
        imagedestroy($this->img['source']);

        $this->img['source'] = $tmp;
        $this->img['width'] = $options['width'];
        $this->img['height'] = $options['height'];
    }

    /**
    *
    */
    public function write($string = null, $options = array()) {
        $options = array_merge(array('color' => '#FFF', 'background' => 'transparent',
            'opacity' => '100', 'font' => null, 'size' => '14', 'rotate' => 0), $options);

        $string = utf8_decode($string);
        $this->pixel($options['size'], $this->img['height']);

        $bbox = imagettfbbox($options['size'], 0, './' . $options['font'], $string);
        $options['width'] = $bbox[2];
        $options['height'] = $options['size'];

        if ($options['background'] != 'transparent')
            $this->geometric('rectangle', $options);

        $pos = $this->position($options, $this->img);
        $pos['y'] += $options['size'];

        $options['background'] = $options['color'];
        $this->imagecolor($options);

        imagettftext($this->img['source'], $options['size'], $options['rotate'],
            $pos['x'], $pos['y'], $this->img['imagecolor'], './' . $options['font'], $string);
    }

    /**
    *
    */
    public function thumbnail($options = array()) {
        $options = array_merge(array('width' => 90, 'height' => 90), $options);

        $this->pixel($options['width'], $this->img['width']);
        $this->pixel($options['height'], $this->img['height']);

        if (floor($this->img['width'] / ($this->img['height'] / $options['height'])) < $options['width']) {
            $options['canvas-height'] = $options['height'];
            unset($options['height']);
        } else {
            $options['canvas-width'] = $options['width'];
            unset($options['width']);
        }

        $this->resize($options);
    }

    /**
    * Gira a imagem
    */
    public function rotate($rotate, $options = array()) {
        $options = array_merge(array('background' => '#FFF', 'opacity' => '100'), $options);

        $this->imagecolor($options);

        $this->img['source'] = imagerotate($this->img['source'], $rotate,
            $this->img['imagecolor'], 0);

        unset($this->img['imagecolor']);
        $this->img['width'] = imagesx($this->img['source']);
        $this->img['height'] = imagesy($this->img['source']);
    }

    /**
    *
    */
    public function layer ($src = null, $options = array()) {
        if (is_array($src))
            $img = $src;
        else
            $img = $this->imagecreate($src);

        if (!$img) return false;

        $options = array_merge(array('opacity' => 100), $options);

        $options['width'] = $img['width'];
        $options['height'] = $img['height'];

        $pos = $this->position($options, $this->img);

        if ($img['format'] == 'png') {
            require_once PATH_PIC_CLASS . 'imagecopymerge_alpha.function.php';

            imagecopymerge_alpha($this->img['source'], $img['source'], $pos['x'],
                $pos['y'], 0, 0, $img['width'], $img['height'], $options['opacity']);
        }

        else
            imagecopymerge($this->img['source'], $img['source'], $pos['x'], $pos['y'],
                0, 0, $img['width'], $img['height'], $options['opacity']);

        if (!is_array($src)) imagedestroy($img['source']);

        return true;
    }

    /**
    * Imagecreate
    */
    private function imagecreate($src) {
        $info = @getimagesize($src);

        if (!in_array($info['mime'], $this->mime))
            return false;

        $img = array();

        $img['width'] = $info[0];
        $img['height'] = $info[1];

        $img['format'] = str_replace(array('.', 'e'), null, image_type_to_extension($info[2]));

        switch ($img['format']) {
            case 'jpg':
                $img['source'] = imagecreatefromjpeg($src);
                $img['background'] = '#FFF';
            break;
            case 'png':
                $img['source'] = imagecreatefrompng($src);
                imagealphablending($img['source'], false);
                imagesavealpha($img['source'], true);
                $img['background'] = 'transparent';
            break;
            case 'gif':
                $img['source'] = imagecreatefromgif($src);
                $img['transparent']['color_index'] = imagecolortransparent($img['source']);

                if ($img['transparent']['color_index'] >= 0)
                    $img['transparent']['color'] = imagecolorsforindex($img['source'], $img['transparent']['color_index']);

                $img['background'] = 'transparent';

            break;
            case 'bmp':
                require_once PATH_PIC_CLASS . 'imagecreatefrombmp.function.php';
                $img['source'] = imagecreatefrombmp($src);
                $img['background'] = '#FFF';
            break;
        }

        return $img;
    }

    /**
    * Abre imagem para edição
    */
    public function open($src = null) {
        if (!is_null($src)) $this->src = $src;
        if ($this->img = $this->imagecreate($src)) return true;
    }

    /**
    * Reabre a imagem, dando empressão de desfazer todas modificações
    */
    public function reset() {
        imagedestroy($this->img['source']);
        return $this->open($this->src);
    }

    /**
    *
    */
    private function filter_name($name = null) {
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
    private function image($save = null, $qualite = 90) {
        imageinterlace($this->img['source'], true);

        switch ($this->img['format']) {
            case 'jpg': return imagejpeg($this->img['source'], $save, $qualite); break;
            case 'png': return imagepng($this->img['source'], $save); break;
            case 'gif': return imagegif($this->img['source'], $save); break;
            case 'bmp':
                require_once PATH_PIC_CLASS . 'imagebmp.function.php';
                return imagebmp($this->img['source'], $save);
            break;
        }
    }

    /**
    *
    */
    public function filter ($filtertype = null, $arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null) {
        switch ($this->filters[$filtertype]) {
            case IMG_FILTER_COLORIZE:
                imagefilter($this->img['source'], $this->filters[$filtertype], $arg1, $arg2, $arg3, $arg4);
            break;

            case IMG_FILTER_PIXELATE:
                imagefilter($this->img['source'], $this->filters[$filtertype], $arg1, $arg2);
            break;

            case IMG_FILTER_BRIGHTNESS:
            case IMG_FILTER_CONTRAST:
            case IMG_FILTER_SMOOTH:
                imagefilter($this->img['source'], $this->filters[$filtertype], $arg1);
            break;

            case IMG_FILTER_NEGATE:
                imagefilter($this->img['source'], $this->filters[$filtertype]);
            break;

            default:
                if (is_null($arg1)) $arg1 = 1;

                for ($i = 0; $i < $arg1; $i++)
                    imagefilter($this->img['source'], $this->filters[$filtertype]);
            break;
        }
    }

    /**
    *
    */
    public function geometric($geometric = null, $options = array()) {
        switch ($geometric) {
            case 'rectangle':
                $options = array_merge(array('width' => '50%', 'height' => '50%',
                    'background' => '#FFF', 'opacity' => 100), $options);

                $this->pixel($options['width'], $this->img['width']);
                $this->pixel($options['height'], $this->img['height']);

                $pos = $this->position($options, $this->img);
                $this->imagecolor($options);

                imagefilledrectangle($this->img['source'], $pos['x'], $pos['y'],
                    $pos['x'] + $options['width'], $pos['y'] + $options['height'],
                    $this->img['imagecolor']);
            break;
        }
    }

    /**
    *
    */
    public function effect($effect = null) {
        switch ($effect) {
            case 'sepia':
                $this->filter('grayscale');
                $this->filter('colorize', 90, 60, 40);
            break;
            case 'drawing':
                $this->filter('grayscale');
                $this->filter('edgedetect');
                $this->filter('brightness', 120);
            break;
        }
    }

    /**
    * Mostra imagem
    */
    public function display($format = null, $qualite = 90) {
        if (!is_null($format)) $this->img['format'] = $format;
        header('Content-type: ' . $this->mime[$this->img['format']]);
        $this->image(null, $qualite);
        imagedestroy($this->img['source']);
        exit;
    }

    /**
    * Download da imagem
    */
    public function download($name = null, $qualite = 90) {
        $name = $this->filter_name($name);
        header('Content-type: ' . $this->mime[$this->img['format']]);
        header('Content-Disposition: attachment; filename="' . $name . '"');
        $this->image(null, $qualite);
        readfile($name);
        imagedestroy($this->img['source']);
        exit;
    }

    /**
    * Salva imagem
    */
    public function save($name = null, $qualite = 90, $chmod = 0777){
        if (is_null($qualite)) $qualite = 90;
        $name = $this->filter_name($name);
        if (!is_dir($dir = dirname($name))) mkdir($dir, $chmod, true);
        if ($this->image($name, $qualite)) chmod($name, $chmod);
    }

    /**
    * Apaga a imagem da memória
    */
    public function clear() {
        imagedestroy($this->img['source']);
    }

    /**
    * Deleta a imagem aberta pelo Pic::open
    */
    public function delete() {
        if (file_exists($this->src)) unlink($this->src);
    }
}
