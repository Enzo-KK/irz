<?php
/**
 * Created by PhpStorm.
 * User: Constantin Krayushkin
 * Date: 07.04.21
 * Time: 10:01
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

	// выбираем все имеющиеся приборы, сопоставляем с поставщиками и потребителями
	    $sql = mysqli_query($link, "SELECT * FROM equip, partic where equip.id=partic.id");

		if (mysqli_num_rows($sql) == 0) {
//			echo  "<script>alert(\"Голяк!\");</script>";
            $equipes[]['id'] = 0;
            $equipes[]['name'] = '';
            $equipes[]['nomer'] = '';
            $equipes[]['adress'] = '';
			}

		while ($row = mysqli_fetch_array($sql))
			//while($row = mysqli_fetch_row($sql))
		{
			$equipes[] = $row;
		}
// выбираем всех имеющиеся поставщиков
        $sql = mysqli_query($link, "SELECT distinct prov FROM partic ");

//        if (mysqli_num_rows($sql) == 0)
        if (!$sql)
        {
//            echo  "<script>alert(\"Голяк!\");</script>";
            $prov[]['prov'] = '';
        }
        else {

            while ($row = mysqli_fetch_array($sql)) //while($row = mysqli_fetch_row($sql))
            {
                $prov[] = $row;
            }
        }
// выбираем всех имеющиеся потребителей
        $sql = mysqli_query($link, "SELECT distinct cons FROM partic ");

//        if (mysqli_num_rows($sql) == 0)
            if (!$sql)
        {
        //            echo  "<script>alert(\"Голяк!\");</script>";
            $cons[]['cons'] = '';
        }
        else {

            while ($row = mysqli_fetch_array($sql)) //while($row = mysqli_fetch_row($sql))
            {
                $cons[] = $row;
            }
        }

// selected по умолчанию сброшен
$prov_sel='';
$cons_sel='';
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
			<title>УК Правобережье. Настройки</title>
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
							<a class="nav-link active" href="#">Сервис</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="user_lc.php">Показания</a>
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
				<p>Редактор поставщиков и потребителй</p>
			<form id="save_partic_id" method="POST" action="save_partic.php" name="save_partic">
				    <table id="table" class="table table-striped">
				        <thead class="thead-dark">
				        <tr>
						<th scope="col" >Узел учета</th>
				        <th scope="col" >Поставщик</th>
						<th scope="col" >Потребитель</th>
						</tr>
				        </thead>
				        <tbody>
				        <tr>
						<th scope="row">
						<select size="1" id="uzel_id" name="uzel" required onchange= ChangeData(); ">
						
					<?php
						// вывод адресов учета
						for ($i = 0; $i < count($equipes); $i++) {
//						    получаю строку с наименованием потребителя и поставщика по каждому прибору
						    $prov_stuff=$equipes[$i]['prov'];
                            $cons_stuff=$equipes[$i]['cons'];
//                           пишу ее в ид по каждому прибору. в функции получаю значения потребителя и постващика
							print "<option id=\"" .$prov_stuff.",".$cons_stuff. "\" value=\"".$equipes[$i]['id']."\">".$equipes[$i]['adress']."</option>";
//							print "<option value=\"2\">Советская 176</option>";
						}
					?>							
						</select>
						</th>
						<td id="prov_td">
                            <select size="1" id="prov_id" name="prov" >

                            <?php
                            // вывод поставщика
                            for ($i = 0; $i < count($prov); $i++) {
//                               если есть в выборке по приборам, значит оно и есть
                                $prov_sel = array_search($prov[$i]['id'],$equipes) ? 'selected' : '';
//                                ($prov[$i]['id']==$equipes[array_search ()]['id'])
                                print "<option value=\"".$prov[$i]['prov']."\">".$prov[$i]['prov']." ".$prov_sel."</option>";
                            }
                            ?>
                                </select><input id="prov_inp" name="prov_crt" hidden >
                            <input type="checkbox" name="prov_nw" value="1" onchange= setNewData('1')>Новый
                        </td>
						<td >
                            <select size="1" id="cons_id" name="cons" >

                            <?php
                            // вывод потребителя
                            for ($i = 0; $i < count($cons); $i++) {
//                               если есть в выборке по приборам, значит оно и есть
                                $cons_sel = array_search($cons[$i]['id'],$equipes) ? 'selected' : '';
                                print "<option value=\"".$cons[$i]['cons']."\">".$cons[$i]['cons']." " .$cons_sel."</option>";
                            }
                            ?>
                            </select><input id="cons_inp" name="cons_crt" hidden >
                            <input type="checkbox" name="cons_nw" value="1" onchange="setNewData('2');">Новый
                        </td>
				        </tr>

				        </tbody>
				    </table>
				<!--        сюда выгрузим данные по приборам из функции js-->
				<!--<div id="cont" class="info"></div>-->
				<!---------------------------------------------------------------->

				<a class="btn btn-secondary"  href="index.php" role="button" title="Вернуться ко входу в личный кабинет">Вернуться</a>

				<!--<a class="btn btn-success" onclick="task(); return false;" href="#" role="button" title="Просмотреть показания">Смотреть</a>-->
				<!--<a class="btn btn-success" type="submit"  role="button" href="choi_period.php" title="Просмотреть показания">Смотреть</a>-->
				<input class="btn btn-success" type="submit"  role="button"  title="Сохратить введенные данные" value="Сохранить">
			</form>
				<p></p>
				<hr>
				<p> </p>
			</div>

	<script>
//        в случае отметки чека, показываем поле инпут
        function setNewData(who)
        {
            // alert('fack');
            // ----------- показываем - прячем инпут
            if (who=='1') {
                // alert(document.getElementById('prov_inp').hidden);
                // показываем
                if (document.getElementById('prov_inp').hidden) document.getElementById("prov_inp").hidden = false;
                // прячем инпут
                else document.getElementById("prov_inp").hidden =true;
            }
            else
                if (document.getElementById('cons_inp').hidden) document.getElementById("cons_inp").hidden = false;
                // прячем инпут
                else document.getElementById("cons_inp").hidden=true;
            // ---------------------
        }
        // в случае выбора другого адреса прибора
        function ChangeData() {
            // меняем активные элементы в списках потребителя и поставщика
            // var prov=document.querySelector('select').value;
            var sel = document.getElementById("uzel_id"); // Получаем наш список
            // получаем ид
            var stuff = sel.options[sel.selectedIndex].id;
            // разбиваем на подстроки
            var spl_ar = stuff.split(",");
            var prov = spl_ar[0];
            var cons = spl_ar[1];
            // alert(prov);
            // переставляем селектор на оптион по значению валю
              document.querySelector("#prov_id").value = prov;
             document.querySelector("#cons_id").value = cons;
        }

	function setNewData1(who)
    // не проканала
	{
	    // alert('fack');
			// ----------- создаем - убираем инпут
        if (who=='1') {
            // alert(who);
            // создаем
            if (document.getElementById('prov_sp').innerHTML==' ') prov_sp.innerHTML='<input name="prov_crt" required>';
            // убираем инпут
            else prov_sp.innerHTML=' ';
        }
        else
            if (document.getElementById('cons_sp').innerHTML==' ') cons_sp.innerHTML='<input name="cons_crt" required>';
            else cons_sp.innerHTML=' ';
			// ---------------------
	}
	</script>

		</body>
	</html>

	