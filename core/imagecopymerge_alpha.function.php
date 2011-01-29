<?php
/**
 * Função que possibilita sobrepor um PNG sobre outra imagem
 * e manter a transparência
 *
 * @author Sina Salek
 * @link http://www.php.net/manual/en/function.imagecopymerge.php#92787
 */
function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){
	$w = imagesx($src_im);
	$h = imagesy($src_im);
	
	// creating a cut resource
	$cut = imagecreatetruecolor($src_w, $src_h);
	
	// copying that section of the background to the cut
	imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);
	
	// placing the watermark now 
	imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);
	imagecopymerge($dst_im, $cut, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct);
}