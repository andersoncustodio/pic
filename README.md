## Abrindo e Salvando
### Local
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

Se não houver modificações na imagem a melhor coisa a fazer é movê-la, este é só um exemplo de como usar os métodos.

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

## Imagem com largura e altura fixa
	$image = new Pic;
	$image->open('imagem.jpg');
	$image->resize(array('width' => '200px', 'max-height' => '350px'));
	$image->display();

## Avatar com medida fixa
	$image = new Pic;
	$image->open('imagem.jpg');
	$image->thumbnail(array('width' => '128px', 'height' => '128px', 'left' => 'auto'));
	$image->display();

## Camadas
	$image = new Pic;
	$image->open('imagem.jpg');
	$image->layer('logo.png', array('right' => '5px', 'bottom' => '5px'));

	$layer = new Pic;
	$layer->open('logo.jpg');
	$image->layer($layer->img, array('left' => '5px', 'top' => '5px'));

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

Baixe as fontes que serão usadas e indique o caminho completo, ex: `'font' => 'arial.ttf'`.

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
	$image->efect('drawing');

### Sepia
	$image->efect('sepia');

## Filtros
$this->filter($filtertype [, int $arg1 [, int $arg2 [, int $arg3 [, int $arg4 ]]]]);

### filtertype
*negate*: Reverses all colors of the image.
*grayscale*: Converts the image into grayscale.
*brightness*: Changes the brightness of the image. Use arg1 to set the level of brightness.
*contrast*: Changes the contrast of the image. Use arg1 to set the level of contrast.
*colorize*: Like IMG_FILTER_GRAYSCALE, except you can specify the color. Use arg1, arg2 and arg3 in the form of red, blue, green and arg4 for the alpha channel. The range for each color is 0 to 255.
*edgedetect*: Uses edge detection to highlight the edges in the image.
*emboss*: Embosses the image.
*gaussian-blur*: Blurs the image using the Gaussian method.
*selective-blur*: Blurs the image.
*mean-removal*: Uses mean removal to achieve a "sketchy" effect.
*smooth*: Makes the image smoother. Use arg1 to set the level of smoothness.
*pixelate*: Applies pixelation effect to the image, use arg1 to set the block size and arg2 to set the pixelation effect mode.

### arg1
*brightness*: Brightness level.
*contrast*: Contrast level.
*colorize*: Value of red component.
*edgedetect*: Edge detection level.
*emboss*: Embosses level.
*gaussian-blur*: Gaussian Blurs level.
*selective-blur*: Blurs level.
*mean-removal*: Removal level.
*smooth*: Smoothness level.
*pixelate*: Block size in pixels.

### arg2
*colorize*: Value of green component.
*pixelate*: Whether to use advanced pixelation effect or not (defaults to FALSE).

### arg3
*colorize*: Value of blue component.

### arg4
*colorize*: Alpha channel, A value between 0 and 127. 0 indicates completely opaque while 127 indicates completely transparent.
