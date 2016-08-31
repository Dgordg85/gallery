<?php 

function modString($string) {
	 if (check($string) !== true) {
	 	return check($string);
	 }

	$string = cString($string);
	$arrString = transformation($string);
	$arrString[0] = rpn($arrString[0]);
	$string = outRpn($arrString);
	$string = finalTransformation($string);
return $string;
} 

function check($string) {
	$countChars = count_chars($string, 1);
	if ($countChars[40] != $countChars[41]) {
		return "Ошибочное выражение - проверьте количество скобок";
	}
	
	if ($countChars[47] > 0) {
		return "Операция деления не допустима";
	}

	$i = 0;
	$operationArray = array('+','-','*');
	while ($i < strlen($string)) {
		$preChar = $string{$i-1};
		$char = $string{$i};
		
		if (in_array($char,$operationArray) && in_array($preChar,$operationArray)) {
			return "Ошибочное выражение - проверьте операции на дублирование";
		}
		$i++;
	}
	return true;
}

function cString($string) {
	$operationArray = array('+','-','*');
	$string .= '+0';
	$string = str_replace('{', '(', $string);
	$string = str_replace('[', '(', $string);
	$string = str_replace('}', ')', $string);
	$string = str_replace(']', ')', $string);
	$string = str_replace(' ', '', $string);
	$string = str_replace('=', '', $string);

	$stopArr = array('+','-','(',')','*');
	$digits = array('0','1','2','3','4','5','6','7','8','9','^');
	$i = 0;
	while ($i < strlen($string)) {
		$char = $string{$i};
		if (in_array($char, $stopArr) || $char == 'x' || in_array($char, $digits)) {
			$tempString .= $char;
		} else $tempString.='x';
		$i++;
	}
	if ($string{0} == '+') {
		$string = substr($string, 1);
	}
	
	// добавляем недостающие операции умножения
	// открывающаяся скобка
	$posOpenBracket = 0;
	$posOpenBracket = strpos($string, '(', $posOpenBracket);
	while ($posOpenBracket !== false) {
		$subString = '';
		for ($i=1; $i < strlen($string); $i++) {
			$readChar = $string{$posOpenBracket - $i};
			if (in_array($readChar, $stopArr) || $readChar == '') {
				switch ($readChar) {
					case '*':
					case '(':
					case '+':
					case '-':
					case '':
						if (strlen($subString) == 0) {
							break;
						}
					default:
						$subString .= '*';
						$string = substr_replace($string, $subString, $posOpenBracket - ($i - 1), strlen($substring)+ $i - 1);
						break;
				}
				break;
			} else {
				$subString = $readChar.$subString;
			}
		}
	$posOpenBracket = strpos($string, '(', $posOpenBracket + strlen($substring) + 1);
	}

	// закрывающаяся скобка
	$posCloseBracket = 0;
	$posCloseBracket = strpos($string, ')', $posCloseBracket);
	while ($posCloseBracket !== false) {
		$subString = '';
		for ($i = 1; $i < strlen($string); $i++) {
			$readChar = $string{$posCloseBracket + $i};
			if (in_array($readChar, $stopArr) || $readChar == '') {
				switch ($readChar) {
					case '*':
					case ')':
					case '+':
					case '-':
					case '':
						if (strlen($subString) == 0) {
							break;
						}
					default:
						$subString = '*'.$subString;
						$string = substr_replace($string, $subString, $posCloseBracket + 1, strlen($substring) + $i - 1);
						break;
				}
				break;
			} else {
				$subString .= $readChar;
			}	
		}
	$posCloseBracket = strpos($string, ')', $posCloseBracket + strlen($substring) + 1);
	}
return $string;
}

function transformation($string) {
	$operationArray = array('+','-','*','/');
	$bracketsArr = array('(',')');
	$varArr[] = $string;
	$count = 1;
	$i = 0;
	$string .= '/';
	while ($i < strlen($string)) {
		$char = $string{$i};
		$charBeforeMinus = $string{$i-1};
		if (in_array($char, $operationArray)){
			if ($charBeforeMinus != '(' && $i != 0) {
				$varArr[] = $substring;
				$count++;
				$substring = '';
			}
			
		}
		switch ($char) {
			case '*':
			case '+':
			case '(':
			case ')':
				break;
			case '-':
				if ($charBeforeMinus != '(' && $i != 0) {
					break;		
				}
			default:
				$substring .= $char;
				break;
		}

		$i++;
	}

	$subString = "/".$varArr[0];
	$currentRead = 0;
	for ($i=1; $i < $count; $i++) {
		$posI = strpos($subString, $varArr[$i], $currentRead);
		$subString = substr_replace($subString, "$i/", $posI, strlen($varArr[$i]));

		$posLastSlash = strrpos($subString, '/');
		$currentChar = 1;
		$readNextChar = $subString{$posLastSlash + $currentChar};
		$countChars = 0;
		$currentChar = 1;
		if (in_array($readNextChar, $operationArray) || in_array($readNextChar, $bracketsArr)) {
			$countChars++;
			while (in_array($readNextChar, $operationArray) || in_array($readNextChar, $bracketsArr)) {
				$currentChar++;
				$readNextChar = $subString{$posLastSlash + $currentChar};
				$countChars++;
			}
		}
		$currentRead = $countChars + $posLastSlash;
	}
	$varArr[0] = $subString;
return $varArr;
}

function rpn($string) {
	$operation = array( '*' => 4,  '+' => 3,  '-' => 3, ')' => 2, '(' => 1);
	$stack = '';
	
	for ($i=0; $i < strlen($string); $i++) {
		$subchar = '';
		$char = substr($string, $i, 1);
		switch($operation[$char]) {
			case 1:
				$stack .= $char;
				break;
			case 2:
				while ($subchar != '(')  {
					$subchar = substr($stack, -1);
					$stack = substr($stack, 0, strlen($stack) - 1);
					if ($subchar != '(') {
						$result .= $subchar;
					}
				} 
				break;
			case 3:
			case 4:
				while ($operation[substr($stack, -1)] >= $operation[$char]) {
					$subchar = substr($stack, -1);
					$stack = substr($stack, 0, strlen($stack) - 1);
					$result .= $subchar;
				}
				$stack .= $char;
				break;
			default:
				$result .= $char;
		}
	}
	/*Если вся входная строка разобрана, но во временном стеке еще остаются знаки операций, 
	мы должны просто извлечь их в основной стек, начиная с последнего символа.*/
	while (strlen($stack) > 0) {
		$subchar = substr($stack, -1);
		$stack = substr($stack, 0, strlen($stack) - 1);
		$result .= $subchar;
	}

return $result;
}

function outRpn($array) {
	$arrOperation = array('+','-','*');

	$stack = '';
	$string = $array[0];

	while (strlen($string) > 0) {

		do {
			$char = substr($string, 0, 1);
			if (!in_array($char, $arrOperation)) {
				$stack .= $char;
			}
			$string = substr($string, 1);
		} while (!in_array($char, $arrOperation));

		$slashCount = 0;
		// определеяем переменные в стеке
	    while ($slashCount < 3) {
	    	$subChar = substr($stack, -1);
	    	$stack = substr($stack, 0, -1);
	    	if ($subChar != '/') {
	    		switch ($slashCount) {
	    		case '1':
	    			$varTwo .= $subChar;
	    			break;
	    		default:
	    			$varOne .= $subChar;
	    			break;
	    		}
	    	} else {
	    		$slashCount++;
	    	}
	    }

		$reservIndex = $varTwo;
	    $varOne = $array[$varOne];
		$varTwo = $array[$varTwo];
	
		switch ($char) {
			case '-':
			case '+':
				$stringOut.= additionNumbers($varOne, $varTwo, $char);
				break;
			default:

				$stringOut.= openBrackets($varOne, $varTwo);
				break;
		}
		$array[$reservIndex] = $stringOut;	
		$stack .= '/'.$reservIndex.'/';

		$stringOut = '';
		$varOne = '';
		$varTwo = '';
		$char = '';
	}
	$result = $array[$reservIndex];
return $result;
}

// на вход строки с выражениями
function openBrackets($string1,$string2) {
	
	$arrNumbers1 = stringToArr($string1);
	$arrNumbers2 = stringToArr($string2);

	for ($i=0; $i < count($arrNumbers1); $i++) { 
		for ($j=0; $j < count($arrNumbers2); $j++) {
			$substring = multiplicationNumber($arrNumbers1[$i],$arrNumbers2[$j]);
			if ($substring{0} != '-' && strlen($string) != 0) {
				$substring = '+'.$substring;
			}
			$string .= $substring;
			$substring = '';
		}
	}

return $string;
}



function finalTransformation($string) {
	$operationArray = array('+','-');
	$string = str_replace('+x', '+1x', $string);
	$string = str_replace('-x', '-1x', $string);
	if ($string{0} == 'x') {
		$string = substr_replace($string, '1x', 0, 1);
	}
	$i = 0;
	while ($i < strlen($string)) {
		$char = $string{$i};
		if ((in_array($char, $operationArray) || ($i + 1 == strlen($string))) && $i != 0){
			if ($i + 1 == strlen($string)) { 
				$substring .= $char;
			}
			$posX = strpos($substring, 'x');
			$posDegree = strpos($substring, '^');
			if ($posX === false) {
				if ($posDegree !== false) {
					$num = substr($substring, 0, $posDegree);
					$deg = substr($substring, $posDegree + 1);
					$substring = pow($num,$deg);
				}
				$varArr[0] += $substring;

				
			} else if ($posX == true && $posDegree == false) {
				$varArr[1] += substr($substring, 0, $posX);	
			} else {
				$readDegree = substr($substring, $posDegree + 1);
				if ($readDegree == 0) {
					$varArr[0] += 1;
				} else {
					$varArr[$readDegree] += substr($substring, 0, $posX);
				}
				
			}
			$substring = '';
		}
		if ($char != '+') {
			$substring .= $char;
		}
		
		$i++;
	}
	$string = '';

	//находим максимальный элемент массива
	$max = 0;
	foreach ($varArr as $key => $value) {
		if ($key > $max) {
			$max = $key;
		}
	}

	// выводим массив в строку
	for ($i=$max; $i >= 0; $i--) {
		$elem = $varArr[$i];
		if ($elem) {
			if ($elem > 0 && strlen($string) != 0) {
				$string .='+';
			}
			if ($elem != 0) {
				switch ($i) {
				case '0':
					$string .= $elem;
					break;
				case '1':
				    if ($elem == 1) {
				       $string .='x'; 
				    } else {
				        $string .= $elem.'x';
				    }
					
					break;
				default:
				     if ($elem == 1) {
				       $string .='x^'.$i; 
				    } else {
				        $string .= $elem.'x^'.$i;
				    }
					break;
				}	
			}
			
		}
	}

	if (substr($string, -1) == '+') {
		$string = substr($string, 0, strlen($string)-1);
	}
    
    $string = str_replace('+', ' + ', $string);
     $string = str_replace('-', ' - ', $string);

return $string;
}

//строку в массив по элементно
function stringToArr($string) {
	$strLen = strlen($string);
	for ($i=0; $i < strlen($string); $i++) {
		$readChar = $string{$i};

		$elementFind = ($readChar == '+' || $readChar == '-' || ($i + 1) == $strLen);
		if ($elementFind == true ) {
			if (($i + 1) == $strLen) {
				$subString .= $readChar;
			}
			if (strlen($subString) > 0) {
				$arrNumbers[] = $subString;
				$subString = '';
			}
		}
		if ($readChar != '+') {
			$subString .= $readChar;
		}
		
	}
return $arrNumbers;
}

// умножение двух чисел
function multiplicationNumber($numberOne, $numberTwo) {
	$posXOne = strpos($numberOne, 'x');
	$posXTwo = strpos($numberTwo, 'x');
	$posDegreeOne = strpos($numberOne, '^');
	$posDegreeTwo = strpos($numberTwo, '^');
	$preffixOne = substr($numberOne, 0, $posXOne);
	$preffixTwo = substr($numberTwo, 0, $posXTwo);

	if ($numberOne == '') {
		$numberOne = '1';
	}

	if ($numberTwo == '') {
		$numberTwo = '1';
	}

	if ($preffixOne === '0' || $preffixTwo === '0' || $numberOne === 0 || $numberTwo === 0) {
		return 0;
	}
	// определяем префикс первого числа
	if ($posXOne !== false) {
		$preffixOne = substr($numberOne, 0, $posXOne);
		
		if ($preffixOne == false) {
			$preffixOne = 1;
		}

		if ($preffixOne == '-') {
			$preffixOne = -1;
		}

		$char = 'x';

		if ($posDegreeOne === false) {
			$suffixOne = 1;
			$degree = '^';
		}

	} else { 
		if ($posDegreeOne !== false) {
			$minus = 0;
			if ($numberOne{0} == '-') {
				$minus = 1;
			}
			$num = substr($numberOne, 0 + $minus, $posDegreeOne);
			$deg = substr($numberOne, $posDegreeOne + 1, strlen($numberOne) - $posDegreeOne - 1);
			$preffixOne = pow($num,$deg);
			if ($minus == 1) {
				$preffixOne = '-'.$preffixOne;
				$minus = 0;
			}
			$posDegreeOne = false;
		} else { 
			$preffixOne = $numberOne;
		}	
	}

	// определяем префикс второго числа
	if ($posXTwo !== false) {
		
		if ($preffixTwo == false) {
			$preffixTwo = 1;
		} 

		if ($preffixTwo == '-') {
			$preffixTwo = -1;
		}

		$char = 'x';

		if ($posDegreeTwo === false) {
			$suffixTwo = 1;
			$degree = '^';
		}
	} else { 
		$minus = 0;
		if ($numberTwo{0} == '-') {
			$minus = 1;
		}
		if ($posDegreeTwo !== false) {
			$num = substr($numberTwo, 0 + $minus, $posDegreeTwo);
			$deg = substr($numberTwo, $posDegreeTwo + 1, strlen($numberTwo) - $posDegreeTwo - 1);
			$preffixTwo = pow($num,$deg);
			if ($minus == 1) {
				$preffixTwo = '-'.$preffixTwo;
				$minus = 0;
			}
			$posDegreeTwo = false;
		} else { 
			$preffixTwo = $numberTwo;
		}	
	}

	// определяем суффикс первого числа
	if ($posDegreeOne !== false) {
		$suffixOne = substr($numberOne, $posDegreeOne + 1, strlen($numberOne) - $posDegreeOne - 1);
		$degree = '^';
	} else {
		if ($posXOne) {
			$suffixOne = 1;
		}
	}

	// определяем суффикс второго числа
	if ($posDegreeTwo !== false) {
		$suffixTwo = substr($numberTwo, $posDegreeTwo + 1, strlen($numberTwo) - $posDegreeTwo - 1);
		$degree = '^';
	} else {
		if ($posXTwo) {
			$suffixTwo = 1;
		}
	}

	$preffix = $preffixOne * $preffixTwo;
	$takeMinus = subStr($preffix, 0, 1);
	$addPlus = ($operationTwo == true || $operationOne == true) && $takeMinus != '-';

	if ($addPlus) {
		$preffix = '+'.$preffix;
	}
	
	if ($preffix === 1) {
		$preffix = '';
	}


	$suffix = $suffixOne + $suffixTwo;

	if ($suffix == 0) {
		$suffix = '';
		$degree = '';
		$char = '';
	}

	if ($suffix == 1) {
		$suffix = '';
		$degree = '';
	}
	$result = $preffix.$char.$degree.$suffix;
return $result;
}

// складывает два числа или вычитает вида -5x^7 8x^2
function additionNumbers($numberOne, $numberTwo, $operation) {

	if (count(stringToArr($numberOne) > 1 || count(stringToArr($numberTwo)) > 1)) {
		return $result = $numberOne.$operation.$numberTwo; 
	}

	$posDegreeOne = strpos($numberOne, '^');
	$posDegreeTwo = strpos($numberTwo, '^');
	$posXOne = strpos($numberOne, 'x');
	$posXTwo = strpos($numberTwo, 'x');

	if ($posDegreeOne !== false) {
		$suffixOne = substr($numberOne, $posDegreeOne + 1);
		$degreeSimbolOne = '^';
	}

	if ($posDegreeTwo !== false) {
		$suffixTwo = substr($numberTwo, $posDegreeTwo + 1);
		$degreeSimbolTwo = '^';
	}

	if ($posXOne !== false) {
		$preffixOne = substr($numberOne, 0, $posXOne);
		$xSimbolOne = 'x';
	} elseif ($posDegreeOne !== false) {
		$preffixOne = substr($numberOne, 0, $posDegreeOne);
	} else {
		$preffixOne = $numberOne;
	}

	if ($posXTwo !== false) {
		$preffixTwo = substr($numberTwo, 0, $posXTwo);
		$xSimbolTwo = 'x';
	} elseif ($posDegreeTwo !== false) {
		$preffixTwo = substr($numberTwo, 0, $posDegreeTwo);
	} else {
		$preffixTwo = $numberTwo;
	}

	if ($suffixOne == $suffixTwo && ($posXOne === false && $posXTwo === false || $posXOne > 0 && $posXTwo > 0)) {

		switch ($operation) {
			case '-':
				$preffix = $preffixOne - $preffixTwo;
				break;
			default:
				$preffix = $preffixOne + $preffixTwo;
				break;
		}
		$suffix = $suffixOne;
		$result = $preffix.$xSimbolOne.$degreeSimbolOne.$suffix;
	} else {
		$result = $preffixOne.$xSimbolOne.$degreeSimbolOne.$suffixOne.$operation.$preffixTwo.$xSimbolTwo.$degreeSimbolTwo.$suffixTwo;
	}
return $result;
}

$string = $_POST['textinput'];
if (strlen($string) == 0){
     $string = '(x - 5)(2x^3 + x(x^2 - 9))';
}

$stringOut = modString($string);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<style type="text/css">
		h1, h2, h5 {
			text-align: center;
		}
		.center {
			text-align: center;
		}
	</style>
	<meta charset="UTF-8">
	
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	
	<title>Раскрытие скобок</title>
</head>
<body>
	<div class="container">
		<h1>Раскрытие скобок</h1>
		<h5>Дано выражение, содержащее скобки, операции сложения, вычитания, умножения,<br>
		возведения в константную степень и одну переменную,<br>например: (x - 5)(2x^3 + x(x^2 - 9)).</h2>
		<hr>

		<form class="form-horizontal" method="post" action="index.php">

			<!-- Text input-->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="textinput"></label>  
			  <div class="col-md-4">
			  <input type="text" id="number_input" autofocus name="textinput" type="text" placeholder="(x - 5)(2x^3 + x(x^2 - 9)" value="<?=$string?>" class="form-control input-md">
			  <span class="help-block">введите полином</span>  
			  </div>
			</div>

			<!-- Text Output-->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="textarea"></label>
			  <div class="col-md-4">
			  <textarea disabled name="textoutput" rows="5" class="form-control input-md"><?=$stringOut?></textarea>
			  <span class="help-block">результат</span>
			  </div>
			</div>
	
			<!-- Button -->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="singlebutton"></label>
			  <div class="col-md-4">
			    <input id="singlebutton" type="submit" name="singlebutton" value="Получить результат" class="btn btn-primary">
			  </div>
			</div>
			</form>
	</div>
</body>
</html>
