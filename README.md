## Abrindo e Salvando
### Local
Caso algum diretório ou subdiretório indicado no `Pic::save()` não existir, o mesmo será criado automaticamente.

	$image = new Pic;
	$image->open('imagem.jpg');
	$image->save('imagem-local.jpg');
	$image->clear(); // Apago a imagem da memória

### Externa
	$image = new Pic;
	$image->open('http://example.com/imagem.jpg');
	$image->save('imagem-externa.jpg');
	$image->clear(); // Apago a imagem da memória

## Apagando a imagem aberta
	$image->open($_FILES['foto']['tmp_name']);
	$image->save('foto-enviada.jpg'); // Salvo uma cópia da imagem
	$image->clear(); // Apago a imagem da memória
	$image->delete(); // Apago a imagem original

É possível fundir outros arquivos com uma imagem, usando o `Pic::save()` você garante que só está sendo salvo dados necessários da imagem.

## Mostrar na tela
	$image = new Pic;
	$image->open('imagem.jpg');
	$image->display('jpg'); // Sem especificar o formato será usado o original

## Download
	$image = new Pic;
	$image->open('imagem.jpg');
	$image->download('imagem.jpg');

## Redimensionamento de fotos
	$image = new Pic;
	$image->open('imagem.jpg');
	$image->photo(array('width' => '600', 'height' => '400px', 'overflow' => 'hidden'));
	$image->display();

Se a altura for maior que a largura as mesmas são invertidas.

## Imagem com largura fixa e altura limitada mantendo a proporção
	$image = new Pic;
	$image->open('imagem.jpg');
	$image->resize(array('width' => '200px', 'canvas-height' => '350px'));
	$image->display();

## Avatar com medida fixa
	$image = new Pic;
	$image->open('imagem.jpg');
	$image->thumbnail(array('width' => '128px', 'height' => '128px', 'left' => 'auto'));
	$image->display();

## Camadas
### Caminho direto
	$image = new Pic;
	$image->open('imagem.jpg');
	$image->layer('logo.png', array('right' => '5px', 'bottom' => '5px', 'opacity' => '50'));
	$image->display();

### Outra imagem aberta pelo Pic
	$image = new Pic;
	$image->open('imagem.jpg');

	$layer = new Pic;
	$layer->open('logo.jpg');

	$image->layer($layer->img, array('left' => '5px', 'top' => '5px', 'opacity' => '50'));

	$layer->clear();

	$image->display();

## Invertendo a imagem na horizontal e/ou vertical
	$image = new Pic;
	$image->open('imagem.jpg');
	$image->flip('v'); // vertical
	$image->flip('h'); // horizontal
	$image->display();

## Crop
	$image = new Pic;
	$image->open('imagem.jpg');
	$image->crop(array(
		'height' => '350px',
		'width' => '250px',
		'top' => '10px',
		'left' => '10px'
	));
	$image->display();

## Background
Coloque background em PNG e GIF transparente.

	$image = new Pic;
	$image->open('imagem.png');
	$image->background('#FFF'); // Não pode ser usado antes do Pic::open()
	$image->displa('jpg');

## Escrever
	$image = new Pic;
	$image->open('imagem.jpg');
	$image->write('Olá Mundo!', array(
		'bottom' => '5px',
		'right' => '5px',
		'font' => 'arial.ttf',
		'size' => '20px',
		'color' => '#FFFF53'
	));
	$image->display();

Baixe as fontes que serão usadas e indique o caminho, ex: `'font' => '../fonts/arial.ttf'`.

## Geometric
### Retângulo
	$image->geometric('rectangle', array(
		'height' => '350px',
		'width' => '250px',
		'top' => '10px',
		'left' => '10px',
		'background' => '#FF0000',
		'opacity' => '50'
	));

## Efeitos
### Transformar em desenho.
	$image->effect('drawing');

### Sepia
	$image->effect('sepia');

## Filtros
$this->filter($filtertype [, int $arg1 [, int $arg2 [, int $arg3 [, int $arg4 ]]]]);

### filtertype
<table>
<tr> <th>negate <td>Reverses all colors of the image.
<tr> <th>grayscale <td>Converts the image into grayscale.
<tr> <th>brightness <td>Changes the brightness of the image. Use arg1 to set the level of brightness.
<tr> <th>contrast <td>Changes the contrast of the image. Use arg1 to set the level of contrast.
<tr> <th>colorize <td>Like IMG_FILTER_GRAYSCALE, except you can specify the color. Use arg1, arg2 and arg3 in the form of red, blue, green and arg4 for the alpha channel. The range for each color is 0 to 255.
<tr> <th>edgedetect <td>Uses edge detection to highlight the edges in the image.
<tr> <th>emboss <td>Embosses the image.
<tr> <th>gaussian-blur <td>Blurs the image using the Gaussian method.
<tr> <th>selective-blur <td>Blurs the image.
<tr> <th>mean-removal <td>Uses mean removal to achieve a "sketchy" effect.
<tr> <th>smooth <td>Makes the image smoother. Use arg1 to set the level of smoothness.
<tr> <th>pixelate <td>Applies pixelation effect to the image, use arg1 to set the block size and arg2 to set the pixelation effect mode.
</table>

### arg1
<table>
<tr> <th>brightness <td>Brightness level.
<tr> <th>contrast <td>Contrast level.
<tr> <th>colorize <td>Value of red component.
<tr> <th>edgedetect <td>Edge detection level.
<tr> <th>emboss <td>Embosses level.
<tr> <th>gaussian-blur <td>Gaussian Blurs level.
<tr> <th>selective-blur <td>Blurs level.
<tr> <th>mean-removal <td>Removal level.
<tr> <th>smooth <td>Smoothness level.
<tr> <th>pixelate <td>Block size in pixels.
</table>

### arg2
<table>
<tr> <th>colorize <td>Value of green component.
<tr> <th>pixelate <td>Whether to use advanced pixelation effect or not (defaults to FALSE).
</table>

### arg3
<table>
<tr> <th>colorize <td>Value of blue component.
</table>

### arg4
<table>
<tr> <th>colorize <td>Alpha channel, A value between 0 and 127. 0 indicates completely opaque while 127 indicates completely transparent.
</table>

<!-- vim:noet -->
