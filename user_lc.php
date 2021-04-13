<?php
/**
 * Created by PhpStorm.
 * User: Constantin Krayushkin
 * Date: 10.02.21
 * Time: 14:01
 */
// Информация о лицевом счете пользователя

require_once 'mySecure.php';
require_once 'myClass.php';

// коннектимся к бд
$link = connect_to_db ();

// проверяем логин пароль
$chklp = new LogPass();

if ($chklp->lchet != '') {
	$lchet = htmlentities(mysqli_real_escape_string($link, $chklp->lchet));
	$pass = htmlentities(mysqli_real_escape_string($link, $chklp->pass));

	if ( $chklp->pass == '' ) {
		header('Location: index.php');
		exit;
	}
}

	/// проверяем не админ ли ломится
	$chklp->chkAdm();

	$sql = mysqli_query($link, "SELECT * FROM date_bd ");

	$row = mysqli_fetch_array($sql);
	$date_bd = $row['filedate'];
	// сохраним чтоб использовать в других вкладках
	$_SESSION['date_bd'] = $date_bd;
// выбираем все имеющиеся приборы
	    $sql = mysqli_query($link, "SELECT * FROM equip");

		if (mysqli_num_rows($sql) == 0) {
			echo  "<script>alert(\"Голяк!\");</script>";
			}

		while ($row = mysqli_fetch_array($sql))
			//while($row = mysqli_fetch_row($sql))
		{
			$equipes[] = $row;
		}

	//
	//    if (mysqli_num_rows($sql) == 0)
	//    {
	//        header('Location: index.php');
	//        exit;
	//    }

	//    записываем в сессию логин и пароль чтоб не вываливаться
	$chklp->writeSess();

	/////
//	}
//else{
//    header('Location: index.php');
//    exit;
//}

?>
	<!doctype html>
	<html lang="ru">
		<head>
			<meta charset="utf-8">
			<title>УК Правобережье. Показания ПУ</title>
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"> </script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"> </script>
			<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"> </script>
			<link rel="icon" href="my-ico.jpg">
			<!--    <script src="js/jquery-3.5.1.min.js"></script>-->
			<!--    <script src="js/jquery.js"></script>-->

		</head>
		<body>

			<nav class="navbar navbar-expand-md bg-dark navbar-dark fixed-top">
				<!--<nav class="navbar navbar-expand-md bg-dark navbar-dark ">-->
				<a class="navbar-brand" href="http://khozyain-doma.ru">ООО "УК Правобережье"</a>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="collapsibleNavbar">
					<ul class="navbar-nav">
						<li class="nav-item">
							<a class="nav-link active" href="#">Показания</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="serv.php">Сервис</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="#">Обработка заявок</a>
						</li>
						<!--            если есть активный опрос добавляем пункт -->
						<?php
	if (isset($_SESSION['isactopr'])  && $_SESSION['isactopr'] == 1)
		: ?>
						<li class="nav-item">
							<a class="nav-link" href="#">Опрос</a>
						</li>
						<?php endif; ?>
					</ul>
				</div>
			</nav>
			<br>
			<br>
			<br>

			<div class="container-fluid">
				<h3>Показания приборов учета тепла</h3>
				<p>Управляющая компания "Правобережье"</p>
			<form id="choi_period_id" method="POST" action="choi_period.php" target="_blank" name="choi_period">
				    <table id="table" class="table table-striped">
				        <thead class="thead-dark">
				        <tr>
						<th scope="col" >Узел учета</th>
				        <th scope="col" colspan="2">Период</th>
						</tr>
				        <th scope="col" rowspan="2"></th>
						<th scope="col" >С даты</th>
						<th scope="col" >По дату</th>
						</tr>
				        </thead>
				        <tbody>
				        <tr>
						<th scope="row">
						<select size="1" id="usel_id" name="uzel" required ">
						
					<?php
						// вывод адресов учета
						for ($i = 0; $i < count($equipes); $i++) {
							print "<option value=\"".$equipes[$i]['id']."\">".$equipes[$i]['adress']."</option>";
//							print "<option value=\"2\">Советская 176</option>";
						}
					?>							
						</select>
						</th>
						<td><input name="dat_s" type="datetime-local" required title="Ввод даты начала периода"></td>
						<td><input name="dat_po" type="datetime-local" required title="Ввод даты конца периода"></td>
				        </tr>

				        </tbody>
				    </table>
				<!--        сюда выгрузим данные по приборам из функции js-->
				<!--<div id="cont" class="info"></div>-->
				<!---------------------------------------------------------------->

				<a class="btn btn-secondary"  href="index.php" role="button" title="Вернуться ко входу в личный кабинет">Вернуться</a>

				<!--<a class="btn btn-success" onclick="task(); return false;" href="#" role="button" title="Просмотреть показания">Смотреть</a>-->
				<!--<a class="btn btn-success" type="submit"  role="button" href="choi_period.php" title="Просмотреть показания">Смотреть</a>-->
				<input class="btn btn-success" type="submit"  role="button"  title="Просмотреть показания за период" value="Смотреть">
			</form>
				<p></p>
				<hr>
				<p> </p>
			</div>

	<script>
	// да это я прикалывался :)) 
	function rotation(i)
	{
		// крутим цифры перед установкой реальных значений
		var entry = 12387655;
		//#block - найти элемент по индентификатору
		//.block - найти по имени класса
		setTimeout(function() {

			// Добавить задачи для выполнения
			entry=entry*i;
			$('.rot').html(entry);
		}, 100 * i);

		// $('.rot').html(entry);
	}
	function task()
	{
		// делает задержку выполнения фунции прокрутки цифр
		for (let i=1; i<10; i++) {

			rotation(i);
		}
		// запускаем обновление данных с задержкой, чтобы успела закончиться прокрутка
		setTimeout(setNewData, 100);
	}
	function setNewData()
	{
		// alert("функция");
		//         task();
		$.getJSON('chek_val.php', function(data) { // ajax-запрос, данные с сервера запишутся в переменную data

			// ----------- добавляем таблицу
			htmlstr = '<table class=\"table table-striped\"><thead class=\"thead-dark\"><tr><th scope="col" rowspan="2">Адрес установки</th><th scope="col" rowspan="2">На дату</th><th scope="col" rowspan="2">Тепло, Гкал</th><th scope="col" rowspan="2">Расход, т</th><th scope="col" colspan="2" align="center">Температура</th><th scope="col" colspan="2" align="center">Давление</th></tr><tr><th scope="col">Т1, С</th><th scope="col">Т2, С</th><th scope="col">Р1, МПа</th><th scope="col">Р2, МПа</th></tr></thead><tbody>';
			// массиб запроса трехмерный, поэтому два перебора - первый по записям, второй по столбцам
			$.each(data, function(ind, dat) {
				htmlstr += '<tr>';  // добавляем записи из запроса
				$.each(dat, function(key, val) {
					htmlstr += '<td class=\"rot\" >' +  [val] +    '</td>';      // добавляем столбцы
				});
				htmlstr += '</tr>';
			});
			htmlstr += '<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr></tbody></table>'; // завершаем тоблицу
			// $('div.info').html(htmlstr); // в div с классом info выводим получившуюся таблицу с данными
			$('#cont').html(htmlstr); // в div с ид cont выводим получившуюся таблицу с данными
			// ---------------------
		});
		//задержка перед выполнением функции
		// setTimeout(sayHi, 3000);
	}
	// прописать данные до нажатия на кнопку или не дожидаясь интервала обновления
	// $(function() {
	setNewData();
	// });
	// для для автоматического запуска функции обновления
	// setInterval(setNewData, 60000); // раз в минуту
	</script>

		</body>
	</html>

	