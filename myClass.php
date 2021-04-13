<?php
/**
 * Created by PhpStorm.
 * User: Constantin Krayushkin
 * Date: 15.01.20
 * Time: 12:50
 */
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Font, Border, Alignment};

// проверка логина, пароля
class LogPass {
    public $lchet;
    public $pass;
    public $date_bd;
    function __construct (){
        $this->lchet = isset($_POST['login']) ? $_POST['login'] : '';
        $this->pass = isset($_POST['password']) ? $_POST['password'] : '';

        if ($this->lchet == '') {
            $this->lchet = isset($_SESSION['lchet']) ? $_SESSION['lchet'] : '';
            $this->pass = isset($_SESSION['user']) ? $_SESSION['user'] : '';
            $this->date_bd = isset($_SESSION['date_bd']) ? $_SESSION['date_bd'] : '';

        }
    }
    function chkAdm (){
        if ($this->pass == ADM_PASS and ($this->lchet == ADM_LOG1 or $this->lchet == ADM_LOG2 or $this->lchet == ADM_LOG3)){
            $_SESSION['adm_on']=1;
// пока делаю вход на юзерлс. потом посмотрим..
            //            header('Location: adm_zay.php');
            header('Location: user_lc.php');
            exit;
        }
        else{
            $_SESSION['adm_on']=0;
        }

    }
    function writeSess (){
        if (!isset($_SESSION['lchet'])) {
            $_SESSION['lchet'] = $this->lchet;
            $_SESSION['user'] = $this->pass;
        }

    }
    function writePayer($lchet, $fio, $adress, $sum, $date_now){
        if (!isset($_SESSION['payer'][0])) {
            $_SESSION['payer'][] = $lchet;
            $_SESSION['payer'][] = $fio;
            $_SESSION['payer'][] = $adress;
            $_SESSION['payer'][] = $sum;
            $_SESSION['payer'][] = $date_now;
        }
    }
}
// формирование файла эксел
function WrtXls($fl_name, $prov, $cons, $d_mes, $uz_str, $pr_nm, $pr_snum, $d_s, $d_po, $w_t1, $m_t1, $w_t, $m_t, $q_all, $m_all, $t_nar_t, $t_nar_n)
{
//    получаю месяц на русском
    $_monthsList = array(
        "1"=>"Январь","2"=>"Февраль","3"=>"Март",
        "4"=>"Апрель","5"=>"Май", "6"=>"Июнь",
        "7"=>"Июль","8"=>"Август","9"=>"Сентябрь",
        "10"=>"Октябрь","11"=>"Ноябрь","12"=>"Декабрь");

    $month = $_monthsList[date("n", $d_mes)];

//Создаем экземпляр класса электронной таблицы
    $spreadsheet = new Spreadsheet();

//Получаем текущий активный лист
    $sheet = $spreadsheet->getActiveSheet();

// устанвливаем умолчания (шрифт и размер, ширина столбца в данном случае)
    $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
    $spreadsheet->getDefaultStyle()->getFont()->setSize(10);
    $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(13);

    $locale = 'ru';
    $validLocale = \PhpOffice\PhpSpreadsheet\Settings::setLocale($locale);
    if (!$validLocale) {
        echo 'Unable to set locale to '.$locale." - reverting to en_us<br />\n";
    }
// нестандартная ширина столбцов
    $sheet->getColumnDimension('B')->setWidth(13);
    $sheet->getColumnDimension('h')->setWidth(14);

// Записываем данные
    $sheet->setCellValue('B2', 'СПРАВКА');
    $sheet->mergeCells('B2:g2');
    $sheet->setCellValue('h3', '=today()');
// форматируем дату
    $sheet->getStyle('h3')->getNumberFormat()->setFormatCode('DD.MM.YYYY');
    $sheet->setCellValue('B4', 'о теплопотреблении за');
//    $sheet->setCellValue('F4',  date('M, Y',$d_mes ));
    $sheet->setCellValue('F4', $month . ', ' . date('Y',$d_mes ));
    $sheet->getStyle('f4')->getNumberFormat()->setFormatCode('MMMM, YYYY');
    $sheet->mergeCells('B4:E4');
    $sheet->mergeCells('F4:G4');
    $sheet->setCellValue('B6', 'Договор №');
    $sheet->setCellValue('c6', '1154877');
    $sheet->setCellValue('d6', 'Адрес:');
    $sheet->setCellValue('e6', $uz_str);
    $sheet->mergeCells('e6:g6');
    $sheet->setCellValue('b7', 'Потребитель:');
    $sheet->setCellValue('c7', $cons);
    $sheet->mergeCells('c7:e7');
    $sheet->setCellValue('b8', 'Тип прибора:');
    $sheet->setCellValue('c8', $pr_nm);
    $sheet->setCellValue('d8', '№ пибора:');
    $sheet->setCellValue('e8', $pr_snum);
    $sheet->setCellValue('b9', 'Система ГВС:');
    $sheet->setCellValue('c9', 'открытая');
    $sheet->setCellValue('c10', 'Величина тепловой');
    $sheet->setCellValue('e10', 'Масса воды');
    $sheet->setCellValue('g10', 'Время работы');
    $sheet->mergeCells('c10:d10');
    $sheet->mergeCells('e10:f10');
    $sheet->mergeCells('g10:h10');
    $sheet->setCellValue('C11', 'энергии Е, Гкал');
    $sheet->setCellValue('e11', 'М, т');
    $sheet->setCellValue('g11', 'счётчика, час');
    $sheet->mergeCells('c11:d11');
    $sheet->mergeCells('e11:f11');
    $sheet->mergeCells('g11:h11');
    $sheet->setCellValue('b12', 'Дата');
    $sheet->setCellValue('C12', 'По');
    $sheet->setCellValue('d12', 'По');
    $sheet->setCellValue('e12', 'По');
    $sheet->setCellValue('f12', 'По');
    $sheet->setCellValue('g12', 'По');
    $sheet->setCellValue('h12', 'По');
    $sheet->setCellValue('C13', 'Подающему');
    $sheet->setCellValue('d13', 'Обратному');
    $sheet->setCellValue('e13', 'Подающему');
    $sheet->setCellValue('f13', 'Обратному');
    $sheet->setCellValue('g13', 'Подающему');
    $sheet->setCellValue('h13', 'Обратному');
    $sheet->setCellValue('C14', 'трубопроводу');
    $sheet->setCellValue('d14', 'трубопроводу');
    $sheet->setCellValue('e14', 'трубопроводу');
    $sheet->setCellValue('f14', 'трубопроводу');
    $sheet->setCellValue('g14', 'трубопроводу');
    $sheet->setCellValue('h14', 'трубопроводу');
// начало периода
    $sheet->setCellValue('b15', $d_s);
// конец периода
    $sheet->setCellValue('b16', $d_po);
    $sheet->setCellValue('b17', 'Итого:');
//тепло
    $sheet->setCellValue('C15', $w_t1);
    $sheet->setCellValue('C16', $w_t);
    $sheet->setCellValue('C17', $q_all);
//вода подающий
    $sheet->setCellValue('e15', '');
    $sheet->setCellValue('e16', '');
    $sheet->setCellValue('e17', $m_t);
// обратный
    $sheet->setCellValue('f15', '');
    $sheet->setCellValue('f16', '');
    $sheet->setCellValue('f17', $m_t1);
// наработка
    $sheet->setCellValue('g15', '');
    $sheet->setCellValue('g16', '');
    $sheet->setCellValue('g17', $t_nar_t);
    $sheet->setCellValue('b18', 'Прибор не работал');
    $sheet->mergeCells('b18:c18');
    $sheet->setCellValue('d18', $t_nar_n . ' часов');
    $sheet->setCellValue('e18', 'подача теплоносителя в это время');
    $sheet->mergeCells('e18:g18');
    $sheet->setCellValue('h18', 'осуществлялась');
    $sheet->setCellValue('e19', 'расход по трубопроводам');
    $sheet->setCellValue('g19', 'не фиксировался');
    $sheet->mergeCells('e19:f19');
    $sheet->mergeCells('g19:h19');
// итоги
    $sheet->setCellValue('d20', 'По счетчику');
    $sheet->mergeCells('d20:d21');
    $sheet->setCellValue('e20', 'Досчет недоработки');
    $sheet->mergeCells('e20:e21');
    $sheet->setCellValue('f20', 'Довыставлено за');
    $sheet->mergeCells('f20:f21');
    $sheet->setCellValue('g20', 'Досчет до конца месяца');
    $sheet->mergeCells('g20:g21');
    $sheet->setCellValue('h20', 'Итого');
    $sheet->mergeCells('h20:h21');
    $sheet->setCellValue('b22', 'Расход тепловой энергии ');
    $sheet->mergeCells('b22:c22');
    $sheet->setCellValue('d22', $q_all);
    $sheet->setCellValue('h22', $q_all);
    $sheet->setCellValue('b23', 'Расход горячей воды, т');
    $sheet->mergeCells('b23:c23');
    $sheet->setCellValue('d23', $m_all);
    $sheet->setCellValue('h23', $m_all);
//$sheet->getStyle('h23')->getNumberFormat()->setFormatCode('###0,00');
    $sheet->setCellValue('b24', 'Расход горячей воды, м3');
    $sheet->mergeCells('b24:c24');
    $sheet->setCellValue('f24', $m_all . ' / 0,98');
    $sheet->mergeCells('f24:g24');
//    расчет объема по массе
    $v_all=$m_all/0.98;
//    форматируем число
    $v_all= number_format($v_all,2,'.',' ');
    $sheet->setCellValue('h24', $v_all);
//$sheet->setCellValue('h24', '202,55');
    $sheet->setCellValue('b27', 'Представитель '.$prov);
    $sheet->getStyle('e27:g27')->getBorders()->getBottom()->setBorderStyle(border::BORDER_THIN);
    $sheet->setCellValue('b31', 'Представитель абонента');
    $sheet->getStyle('e31:g31')->getBorders()->getBottom()->setBorderStyle(border::BORDER_THIN);
    $sheet->getCell('f40')->setValue('=h24');

// рисуем обрамление
    $sheet->getStyle('c10:d14')->getBorders()->getOutline()->setBorderStyle(border::BORDER_THIN);
    $sheet->getStyle('g10:h14')->getBorders()->getOutline()->setBorderStyle(border::BORDER_THIN);
    $sheet->getStyle('c11:h11')->getBorders()->getBottom()->setBorderStyle(border::BORDER_THIN);
    $sheet->getStyle('d12:d14')->getBorders()->getLeft()->setBorderStyle(border::BORDER_THIN);
    $sheet->getStyle('f12:g14')->getBorders()->getOutline()->setBorderStyle(border::BORDER_THIN);
    $sheet->getStyle('b15:h17')->getBorders()->getAllBorders()->setBorderStyle(border::BORDER_THIN);

    $sheet->getStyle('B2')->applyFromArray([
        'font' => ['bold' => true],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
            'wrapText' => true,
        ]
    ]);
    $sheet->getStyle('b2:h2')->getBorders()->getOutline()->setBorderStyle(border::BORDER_THIN);

    $sheet->getStyle('B3:h9')->applyFromArray([
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
        ]
    ]);

    $sheet->getStyle('B10:h17')->applyFromArray([
        'font' => [
            'size' => '9'],
        'borders' => [
            'outline' => ['borderStyle' => Border::BORDER_THIN]
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
            'wrapText' => true,
        ]
    ]);

    $sheet->getStyle('B18:h19')->applyFromArray([
        'font' => [
            'size' => '9'],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
            'wrapText' => true,
        ]
    ]);

    $sheet->getStyle('d20:h24')->applyFromArray([
        'font' => [
            'size' => '9'],
        'borders' => [
            'outline' => ['borderStyle' => Border::BORDER_THIN]
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
            'wrapText' => true,
        ]
    ]);

    $sheet->getStyle('b22:h24')->applyFromArray([
        'font' => [
            'size' => '9'],
        'borders' => [
            'outline' => ['borderStyle' => Border::BORDER_THIN]
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
            'wrapText' => true,
        ]
    ]);

    $sheet->getStyle('d22:h23')->getBorders()->getAllBorders()->setBorderStyle(border::BORDER_THIN);
    $sheet->getStyle('h24')->getBorders()->getAllBorders()->setBorderStyle(border::BORDER_THIN);

    try {
        $writer = new Xlsx($spreadsheet);
//    $writer->save('insite/hello.xlsx');
        $writer->save($fl_name);

    } catch (PhpOffice\PhpSpreadsheet\Writer\Exception $e) {
        echo $e->getMessage();
    }

}
