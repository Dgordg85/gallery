<?php
//определяем переодичность дроби
function cutNumber($string) {
	$arrSubString = array();
	if (strlen($string) == 14) {
		for ($j=1; $j <= 5; $j++) {  // длина подстроки 1-5
			for ($i=0; $i+$j <= 11; $i++) { // начало с 0 - 11
				$reserv = 0;
				for ($k=0; $k*$j+$j+$i <= 11; $k++) {    // смещение от нулевой позиции
					$subString = substr($string, $j * $k + $i, $j);
					$futureSubString = substr($string, $j * ($k + 1) + $i, $j);
					if ($subString == $futureSubString && $futureSubString != '') {
						if ($arrSubString[$subString] > 0) {
							$reserv +=1;
							if ($reserv > $arrSubString[$subString]) {
								$arrSubString[$subString] = $reserv;
							}
						} else {
							$arrSubString[$subString] = 2;
							$reserv = 2;
						}	
					} else {
						$reserv = 0;
					}
				}
			}
		}
	}

	$maxValue = -1;
	//выбираем самое длинное вхождение строки
	foreach ($arrSubString as $key => $value) {
		$key = (string)$key;
		$keyLength = strlen($key);
		$isPeriodTrue = $keyLength == 1 && $value > 6 || $keyLength == 2 && $value >= 5 || 
							$keyLength == 3 && $value >= 3 || $keyLength > 3 && $value >= 2;
		if ($isPeriodTrue) {
			if ($value == $maxValue) {
				if (strpos($string, $key) < strpos($string, $maxKey)) {
					$maxValue = $value;
					$maxKey = $key;
				}
			} elseif ($value > $maxValue) {
				$maxKey = $key;
				$maxValue = $value;
			}
		}
	}
	$result = substr($string, 0, 7);
	// ничего не делаем с переодическим нулем
	if (strlen($maxKey) > 0 && $maxKey != '0') {
		$result = substr($string, 0, strpos($string,$maxKey))."($maxKey)";
	}

return $result;
}


//функция выделяет дробную часть
function fraction($number) {
	if (floor($number) == 0) {
		$result = $number;
	} else {
		$result = $number - floor($number);
	}
return $result;
}

function changeDegreeFrom10ToAny($number, $system) {
	
	if ($system < 2 || $system > 36) {
		return "Error! Недопустимая система счисления";
	}

	$systemArr = array(0 => 0, 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 'A', 
				11 => 'B', 12 => 'C', 13 => 'D', 14 => 'E', 15 => 'F', 16 => 'G', 17 => 'H', 18 => 'I', 19 => 'J', 
				20 => 'K', 21 => 'L', 22 => 'M', 23 => 'N', 24 => 'O', 25 => 'P', 26 => 'Q', 27 => 'R', 28 => 'S', 
				29 => 'T', 30 => 'U', 31 => 'V', 32 => 'W', 33 => 'X', 34 => 'Y', 35 => 'Z', );

	// дробная часть
	$fraction = fraction($number); // величина дробной части
	if ($fraction != 0) { // определяем есть ли дробная часть						
		$fNumber = $fraction;						
		for ($i=1; $i < 15; $i++) {
			$fNumber = $fNumber * $system;
			$intPart = floor($fNumber);
			$fResult .= $systemArr[$intPart];
			$fNumber = $fNumber - $intPart;
		}
		//подрезаем строку по периодам
		$cutfResult = cutNumber($fResult);

		// подрезаем нули
		$i = 1;
		$strLength = strlen($cutfResult);
		while (substr($cutfResult,-1) == '0') {
			$cutfResult = substr($cutfResult ,0, $strLength - $i);
			$i++;
		}
		
		$fResult = ".".$cutfResult; // итоговая дробь
		$number = floor($number); // число для дальнейшей работы
	}
	// основание
	if ($number != 0) { // если целая часть вообще есть то её тоже считаем
		while ($number >= $system) {
			$module = $number % $system; 				//остаток от деления
			$number = floor($number / $system);		// целочисленное деление
			$nResult .= $systemArr[$module];
		}
		$nResult = strrev($nResult.$systemArr[$number]);
	} else {
		$nResult = "0";
	}

	// сложение дробной части с основной
	$result = $nResult.$fResult;

return $result;
}


$a = 1;
$b = 12;
$degree = 10;
$number = $a / $b;
echo changeDegreeFrom10ToAny($number, $degree);

?>