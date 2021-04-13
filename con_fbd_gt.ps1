# подключение к бд файрберд
$dbServerName = "localhost:c:\ProgramData\Vzljot\Vzljot Sp Db Firebird\VZLJOTSP.FDB"
$dbUser = "SYSDBA"
$dbPass = "masterkey"

################
#подключаем библиотеку MySql.Data.dll
Add-Type –Path ‘C:\Program Files (x86)\MySQL\MySQL Connector Net 8.0.23\Assemblies\v4.5.2\MySql.Data.dll'
# локальная бд
#$Connection = [MySql.Data.MySqlClient.MySqlConnection]@{ConnectionString='server=localhost;uid=host16973_rem;pwd=r7;database=host16973'}
# удаленная бд
$Connection = [MySql.Data.MySqlClient.MySqlConnection]@{ConnectionString='server=1.2.1.2;uid=host16973_rem;pwd=r7;database=host16973'}

$Connection.Open()
$sql = New-Object MySql.Data.MySqlClient.MySqlCommand

################# получаем максимальную дату сохраненных данных 
##### по тэм
$sqlad = New-Object MySql.Data.MySqlClient.MySqlDataAdapter
$sqlds = New-Object System.Data.DataSet
$sql.Connection = $Connection

$sql.CommandText = "select max(timestamp) as m_ts from present_day where name like 'ТЭМ%' "
$sqlad.SelectCommand=$sql
$numofds=$sqlad.fill($sqlds)
$rowsql = $sqlds[0].Tables[0].rows
$maxdat_tem =  $rowsql[0].m_ts
#echo 'tem: '  $maxdat_tem

##### по тсрв
$sqlad = New-Object MySql.Data.MySqlClient.MySqlDataAdapter
$sqlds = New-Object System.Data.DataSet
$sql.Connection = $Connection

$sql.CommandText = "select max(timestamp) as m_ts from present_day where name like 'ТСРВ%' "
$sqlad.SelectCommand=$sql
$numofds=$sqlad.fill($sqlds)
$rowsql = $sqlds[0].Tables[0].rows
$maxdat_vzl =  $rowsql[0].m_ts
#echo  'tsrv: '  $maxdat_vzl

##### проверка пустого ответа на запрос
if ($maxdat_vzl -like ""){$maxdat_vzl = 0}
if ($maxdat_tem -like ""){$maxdat_tem = 0}
#return $maxdat
#return $numofds
################ 
[string]$szConnect  = "Driver={Firebird/InterBase(r) driver};Database=$dbServerName;Pwd=$dbPass;UID=$dbUser" 

######## обработка таблицы тсрв
$cnDB = New-Object System.Data.Odbc.OdbcConnection($szConnect)
$dsDB = New-Object System.Data.DataSet
$str = "Select * From `"Table_TSRV_026M_Present_Day`" as pd,`"Equip`" as eq where `"TimeStamp`" > $maxdat_vzl and pd.`"EquipId`"=eq.`"Id`" "

try
{
    $cnDB.Open() 
    $adDB = New-Object System.Data.Odbc.OdbcDataAdapter 
    $adDB.SelectCommand = New-Object System.Data.Odbc.OdbcCommand($str, $cnDB)
    $adDB.Fill($dsDB)     
    $cnDB.Close() 
}
catch [System.Data.Odbc.OdbcException]
{
    $_.Exception
    $_.Exception.Message
    $_.Exception.ItemName
}

foreach ($row in $dsDB[0].Tables[0].Rows)
{

#### решил проверять метку. при наличии нулл она была 2
	if ($row[2] -gt 0) { return	}

	# теперь делаем инсерт полученных пременных в удаленный мускул
	$id=$row[0]
	$ts=$row[1]
	$time=$row[6]
	$tnar=$row[7]
	$wts=$row[27]
	$w_t=$row[28]
	$mts=$row[29]
	$m_t=$row[30]
	$m1=$row[36]
	$v1=$row[37]
	$m2=$row[42]
	$v2=$row[43]
	$t1=$row[39]
	$t2=$row[45]
	$p1=$row[40]
	$p2=$row[46]
	$nm=$row[67]
	$sn=$row[68]
	##записываем показания приборов в табдицу БД

	$sql.CommandText = "INSERT INTO present_day (id,timestamp,time,wts,w_t,m1,mts,m2,m_t,t1,t2,p1,p2,v1,v2,tnar,name,snum) VALUES ('$id','$ts','$time','$wts',$w_t,'$m1','$mts','$m2',$m_t,'$t1','$t2','$p1','$p2',$v1,$v2,$tnar,'$nm','$sn') "
	$sql.ExecuteNonQuery()
}
########### то были взлеты. теперь тэмы
################ структура таблиц разная падла. 
$cnDB = New-Object System.Data.Odbc.OdbcConnection($szConnect)
$dsDB = New-Object System.Data.DataSet
$str = "Select * From `"Table_TEM104_Present_Day`" as pd,`"Equip`" as eq where `"TimeStamp`" > $maxdat_tem and pd.`"EquipId`"=eq.`"Id`" "

try
{
    $cnDB.Open() 
    $adDB = New-Object System.Data.Odbc.OdbcDataAdapter 
    $adDB.SelectCommand = New-Object System.Data.Odbc.OdbcCommand($str, $cnDB) 
    $adDB.Fill($dsDB)     
    $cnDB.Close() 
}
catch [System.Data.Odbc.OdbcException]
{
    $_.Exception
    $_.Exception.Message
    $_.Exception.ItemName
}

foreach ($row in $dsDB[0].Tables[0].Rows)
{
#### решил проверять метку. при наличии нулл она была 2
	if ($row[2] -gt 0) { return	}

# теперь делаем инсерт полученных пременных в удаленный мускул
	$id=$row[0]
	$ts=$row[1]
	$time=$row[6]
	$v1=$row[7]
	$v2=$row[9]
	$m1=$row[15]
	$m_t=$row[16]
	$m2=$row[17]
	$wts=$row[23]
	$w_t=$row[24]
	$tnar=$row[31]
	$t1=$row[81]
	$t2=$row[82]
	$p1=$row[93]
	$p2=$row[94]
#### разницу вычисляем		
	$mts=$m1-$m2
###
	$nm=$row[113]
	$sn=$row[114]
	##записываем показания приборов в табдицу БД
	
	$sql.CommandText = "INSERT INTO present_day (id,timestamp,time,wts,w_t,m1,mts,m2,m_t,t1,t2,p1,p2,v1,v2,tnar,name,snum) VALUES ('$id','$ts','$time','$wts',$w_t,'$m1','$mts','$m2',$m_t,'$t1','$t2','$p1','$p2',$v1,$v2,$tnar,'$nm','$sn') "
	$sql.ExecuteNonQuery()
}

################

# закрываем соединение с мускул
$Connection.Close()

# SIG # Begin signature block
# MIIFeQYJKoZIhvcNAQcCoIIFajCCBWYCAQExCzAJBgUrDgMCGgUAMGkGCisGAQQB
# gjcCAQSgWzBZMDQGCisGAQQBgjcCAR4wJgIDAQAABBAfzDtgWUsITrck0sYpfvNR
# AgEAAgEAAgEAAgEAAgEAMCEwCQYFKw4DAhoFAAQUkvaD/2A1E0WkBkyKDksk5c4a
# ac2gggMYMIIDFDCCAfygAwIBAgIQH2U9KTNaRbxN34i6tsnW7jANBgkqhkiG9w0B
# AQUFADAWMRQwEgYDVQQDDAtjZXJfY29kX3NpZzAeFw0yMTAyMDQwMzA2MDFaFw0y
# MjAyMDQwMzI2MDFaMBYxFDASBgNVBAMMC2Nlcl9jb2Rfc2lnMIIBIjANBgkqhkiG
# 9w0BAQEFAAOCAQ8AMIIBCgKCAQEAkaQcuOV1yC1YcEAxbU02l9AzZIR2T0F4Bdgb
# cilzs+UX2XgG1+VytGm7pF0BVKiwOYGqBurUcUDabIUsKxWooboOw1CWD2cpDkEz
# WdNQxfXz5hq8Q7b0o0vXoiD/Ed0v6hbeOsCl48G/1x1mQPe1IsS0omHe5NcDkki5
# zK+a0F4rdFxm3grSvkxyMHVnM8zio45xqbQssd264KKaB/ixn5WHTtYKypYH35bl
# br/3invo6ROEJ5D5VDQRI64PVtUHrUFi17l++ZyQJ7gbBl39w2OspWEmcPBS0sru
# QKi+GO2ZQ4f/GdCEaqqi9/S3v+r6RIMAMLZBW6IgYfzF99cbiQIDAQABo14wXDAO
# BgNVHQ8BAf8EBAMCB4AwEwYDVR0lBAwwCgYIKwYBBQUHAwMwFgYDVR0RBA8wDYIL
# Y2VyX2NvZF9zaWcwHQYDVR0OBBYEFAosBSMLg2Gls853GJEa39Qd8DrxMA0GCSqG
# SIb3DQEBBQUAA4IBAQBFmRD769/bp4zkqVCn01TG8F/zuB+AjO9NDrysGNpTEXQ9
# 9m0TqZPCXeHajsBSKa26N0EhNUIu8ezzZuLu/tqcsAyOYRde3pBg+QR6GmOPal8E
# UE4ViBvPWbQhXMk+x5t0A+QyYZaZ6LSlZ67Yv++OIdR86STYA5/LgTtVSZI+Du84
# bmcKRICASPn9XoLLHKOL3jmZSqvaLYDvwM8t9SYv6cPU7LTWAvN56ClPPlF1IScf
# jqnn+AheO2ZUcqBD9sFGbx+armmXYP4UNG5fCCU2TY+/7catVFO3mwlGayIxmL+j
# XLKG0A5c/xkeQVemF9dx3sPJozpndNlwGqfM8/2yMYIByzCCAccCAQEwKjAWMRQw
# EgYDVQQDDAtjZXJfY29kX3NpZwIQH2U9KTNaRbxN34i6tsnW7jAJBgUrDgMCGgUA
# oHgwGAYKKwYBBAGCNwIBDDEKMAigAoAAoQKAADAZBgkqhkiG9w0BCQMxDAYKKwYB
# BAGCNwIBBDAcBgorBgEEAYI3AgELMQ4wDAYKKwYBBAGCNwIBFTAjBgkqhkiG9w0B
# CQQxFgQUQDDCMW6LWhjGBNUJdE0lqziQlqMwDQYJKoZIhvcNAQEBBQAEggEAHJaB
# WS7RDsR7zUzpWtRbEeLtNu/H0B8pgIyBMkRhQ8Qyb8/gNhfklH/iR4vWoseDYITX
# pYynJ2YM9vjGUfAnc0AocTS6qfHgerJfaOxEYdVws+QdEgTXH/Pl+hrLByaYh42c
# xqNEcU7ObPUHlBUsd6S7c5sv4NnU/7UZBoIffQb06pG93cTaMlV7kF9gQ+qc+R4j
# hDEoFteTE6MBGBpeTOkAk6YyMcNk2+a6ZeMDuYe4d4LHvO2nsUwxhEgrYjhAUEee
# BrQKQW8sOLB5k1FfBng818jVMuSKPyQQy8zCl93aBf6TtDoYBz+fkrT0lyKxoLkM
# q6QiQYkz4U2DrGr/rQ==
# SIG # End signature block
