<?
include('dbconnect.php');

// Получение ID изображения из POST
$id = (int) $_GET['id'];

// Запрос изображения по ID
$query = "SELECT * FROM images WHERE id_image='$id'";
$imageName = queryRunWithResult($link,"$query");
$imageName = $imageName[0];

// Увеличение счетчика просмотров
$imageName['views_count'] += 1;
$count = $imageName['views_count'];
$query = "UPDATE images SET views_count = '$count' WHERE id_image = '$id'";
queryRun($link, $query)
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="style.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<title>Детальный просмотр фотографии</title>
</head>
<body>
	<div class="container">
		<h1 align="center">Детальный просмотр фотографии</h1>
		<div class="col-md-12">
	    	<div class="thumbnail">
	        	<img src="img/<?=$imageName['image_name'];?>" alt="" class="img-responsive">
	   		</div>
	    </div>
	    <h3 align="center">Количество просмотров: <?=$imageName['views_count'];?></h3>
	    <div align="center"><a href="index.php">Назад</a></div>
	</div>
</body>
</html>

<?php 
mysqli_close($link);
?>