## Abrindo e Salvando
	$image = new Pic;
	
	$image->open('imagem.jpg');
	$image->save('imagem1.jpg');
	$image->clear(); // Apago a imagem da memória
	
	$image->open('http://example.com/image.jpg');
	$image->save('imagem2.jpg');
	$image->clear(); // Apago a imagem da memória

## Apagando a imagem da memória e a aberta
	$image->open($_FILES['foto']['tmp_name']);
	$image->save('foto-enviada.jpg'); // Salvo a foto
	$image->clear(); // Apago a imagem da memória
	$image->delete(); // Apago a imagem original nos arquivos temporários

Se não houver modificações na imagem a melhor coisa a fazer é movê-la, este é um exemplo de como usar os métodos.

## Mostrar na tela
	$image = new Pic;
	$image->open('imagem2.jpg');
	$image->display('jpg');

## Download
	$image = new Pic;
	$image->open('imagem2.jpg');
	$image->download('imagem.jpg');

## Redimensionamento de fotos
	$image = new Pic;
	$image->open('imagem2.jpg');
	$image->photo(array('width' => '600', 'height' => '400px', 'overflow' => 'hidden'));
	$image->display();

## Image com largura fixa
	$image = new Pic;
	$image->open('imagem2');
	$image->resize(array('width' => '200px', 'max-height' => '350px'));
	$image->display();

## Avatar com medida fixa
	$image = new Pic;
	$image->open('imagem2.jpg');
	$image->thumbnail(array('width' => '128px', 'height' => '128px', 'left' => 'auto'));
	$image->display();

## Camadas
	$image = new Pic;
	$image->open('imagem2.jpg');
	$image->layer('logo.png', array('right' => '5px', 'bottom' => '5px'));

	$layer = new Pic;
	$layer->open('http://example.com');
	$layer->efect('drawing');

	$image->layer($layer->img, array('left' => '5px', 'top' => '5px'));
	$image->display();

## Invertendo a imagem na horizontal e/ou vertical
	$image = new Pic;
	$image->open('imagem2.jpg');
	$image->flip('v'); // vertical
	$image->flip('h'); // horizontal
	$image->display();

## Crop
	$image = new Pic;
	$image->open('imagem2.jpg');
	$image->crop(array(
		'height' => '350px',
		'width' => '250px',
		'top' => '10px',
		'left' => '10px'
	));
	$image->display();

## Geometric
### Retângulo
	$image = new Pic;
	$image->open('imagem2.jpg');
	$image->geometric('rectangle', array(
		'height' => '350px',
		'width' => '250px',
		'top' => '10px',
		'left' => '10px',
		'background' => '#FF0000',
		'opacity' => '50'
	));
	$image->display();

## Efeitos
### Transformar uma foto em desenho.
	$image = new Pic;
	$image->open('imagem2.jpg');
	$image->efect('drawing');
	$image->display();

## Filtros
	$image = new Pic;
	$image->open('imagem2.jpg');
	$image->filter('brightness', array(50));
	$image->filter('colorize', array(90, 60, 50, 10));
	$image->display();
