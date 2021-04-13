<?php
/**
 * Created by PhpStorm.
 * User: constantin
 * Date: 07.04.21
 * Time: 11:29
 */
require_once 'mySecure.php';
require_once 'myClass.php';

// коннектимся к бд
$link = connect_to_db ();

// доработать условия
if (!isset($_POST['uzel']) || !isset($_POST['prov']) || !isset($_POST['cons'])) return;

//if (!isset($_POST['uzel']) || ( (!isset($_POST['prov']) && !isset($_POST['prov_crt'])) || (!isset($_POST['cons']) && !isset($_POST['cons_crt'])) ) ) {
if ($_POST['uzel']=='' || ( $_POST['prov']=='' && $_POST['prov_crt']=='') || ($_POST['cons']=='' && $_POST['cons_crt']=='' ) ) {
//    if (!isset($_POST['uzel']) ) {
//    echo  "<script>alert(\"return!\");</script>";
    return;
}
//echo  "uz: ".$_POST['uzel']. " pr: " .$_POST['prov']." pr_c: ".$_POST['prov_crt']." con: ".$_POST['cons']." con_c: ".$_POST['cons_crt']." ";

$prov = $_POST['prov_crt']!='' ? $_POST['prov_crt'] : $_POST['prov'];
$cons = $_POST['cons_crt']!='' ? $_POST['cons_crt'] : $_POST['cons'];
$id = (int)$_POST['uzel'];

echo  "n prov: ".$prov." cons: ".$cons." ";

//echo  "<script>alert(\"uz: {$_POST['uzel']} pr: {$_POST['prov']} pr_c: {$_POST['prov_crt']} con: {$_POST['cons']} con_c: {$_POST['cons_crt']} \");</script>";

$sql = mysqli_query($link, "insert into partic (id, cons, prov) values ('{$id}', '{$cons}', '{$prov}') ON DUPLICATE KEY UPDATE cons='{$cons}', prov='{$prov}' ");

if (!$sql) echo  "<script>alert(\"Облом!\");</script>";

header('Location: serv.php');
exit;
