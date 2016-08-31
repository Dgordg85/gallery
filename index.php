<?php 
session_start();
include('dbconnect.php');

//функция для сжимания изображения
function imageResize($outfile,$infile,$newWidth,$quality) {
	
	$im=imagecreatefromjpeg($infile);

	//считаем новые размеры изображений (пропорция)
	$kef = imagesx($im) / imagesy($im);
	$newHeight = $newWidth / $kef;
	$newHeight = (int) $newHeight;

    $im1=imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($im1, $im, 0, 0, 0, 0, $newWidth, $newHeight, imagesx($im), imagesy($im));
    imagejpeg($im1, $outfile, $quality);
    
    imagedestroy($im);
    imagedestroy($im1);
}

//функция оформления вывода изображения
function postImage($id,$file) { 
	?>	<div class="col-xs-3">
		    <div class="thumbnail">
		        <a href="photo.php?id=<?=$id?>">
		        	<img src="img/small_<?=$file?>" alt="" class="img-responsive">
		        </a>
		    </div>
		</div>
	<?
}
// перемещает файлы из загрузчика в наш каталог + в базу (имя)
$uploaddir = getcwd()."\img\\";
$imageName = $_FILES['image']['name'];

$refreshWithImageInMassivFiles = ($_SESSION['image_name'] != $imageName);
$firstOpenPage = (!empty($_POST['upload']));

if ($firstOpenPage && $refreshWithImageInMassivFiles) {

	$imageFrom = $_FILES['image']['tmp_name'];
	$imageTo = $uploaddir.$_FILES['image']['name'];

	move_uploaded_file($imageFrom, $imageTo);
	
	// запоминаем в сессию последнюю загруженную картину, чтобы по F5 повторно в базу не добавлялась
	$_SESSION['image_name'] = $imageName;
	
	// добавляем картинку в базу
	$query = "INSERT INTO images (image_name) VALUES ('$imageName');";
	queryRun($link, $query); 
	
	// уменьшаем изображение с помощью функции до ширины 250 пикселей с качеством 75
	$smallImagePath = "$uploaddir/small_$imageName";
	$uploadImageInFolder = "$uploaddir$imageName";

	imageresize("$smallImagePath","$uploadImageInFolder",250,75);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="style.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<title>Фотогалерея</title>
</head>
<body>
	<div class="container">
		<h1 align="center">Фотогаллерея</h1>

		<form name="upload-images" class="form-horizontal" method="POST" action="index.php" enctype="multipart/form-data">
			<fieldset>

			<!-- Form Name -->
			<legend>Загрузка фотографии</legend>

			<!-- File Button --> 
			<div class="form-group">
			  <label class="col-md-4 control-label" for="filebutton"></label>
			  <div align="center" class="col-md-4">
			    <input required id="filebutton" name="image" class="input-file" type="file" accept="image/jpeg,image/png,image/gif,image/jpg"> 
			  </div>
			</div>

			<!-- Button -->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="singlebutton"></label>
			  <div align="center" class="col-md-4">
			    <input id="singlebutton" type="submit" value="Загрузить" name="upload" class="btn btn-primary">
			  </div>
			</div>

			</fieldset>
		</form>
		<legend>Фото</legend>
		<!--Запрос к БД и получение всех имен из базы и ID-->
		<? $arrImages = queryRunWithResult($link, "SELECT * FROM images ORDER BY views_count DESC");

		//Вывод изображений по 3 в ряд
		for ($i = 0; $i < count($arrImages); $i++) {
			if (($i % 3) == 0) {
				?><div class="row"><?
			}
			postImage($arrImages[$i]['id_image'],$arrImages[$i]['image_name']);
			if ((($i + 1) % 3) == 0) {
				?></div><?
			}
		} 
		?>
	</div>
</body>
</html>

<?php
mysqli_close($link);
?>
