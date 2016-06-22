<?php
include_once('class_and_config.php'); 
$admin = new risesite;
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>Application for restore website: step 1</title>
	<script src="jquery-1.11.3.min.js"></script>
	<script src="script.js"></script>
	<link rel='stylesheet'  href='style.css' type='text/css' />
</head>
<body>
	<h3>Application for restore website: step 1<br /><?php echo "http://web.archive.org/cdx/search/cdx?url=".$_GET['url']."&output=json&limit=".$_GET['limit']; ?></h3>
	<?php
	if(!$_GET['allpage']) { //Если галочка не поставлена
		$list_of_pages = file_get_contents("http://web.archive.org/cdx/search/cdx?url=".$_GET['url']."&output=json&limit=".$_GET['limit']);
		$list_of_pages=json_decode($list_of_pages,true); 
		if($list_of_pages) {
			?>
			<table cellspacing='0' style="margin: 0px auto;">
					<tr>
						<td><b>№</b></td>	
						<td><b>Дата</b></td>
						<td><b>Статус</b></td>
						<td colspan="2"><b>Действия</b></td>	
					</tr>
			
					<?php
					 $i=1;		
					 array_shift($list_of_pages);
					 rsort($list_of_pages);
					 
					 
					 foreach ($list_of_pages as $one_page) { ?> 
					<tr>
						<td><?php echo $i++; ?></td>
						<td><?php echo substr($one_page[1],6,2).'.'.substr($one_page[1],4,2).'.'.substr($one_page[1],0,4); ?></td>
						<td><?php echo $one_page[4]; ?></td>
						<td><a href="http://web.archive.org/web/<?php echo $one_page[1]; ?>/<?php echo $one_page[2]; ?>" target="_blank">Открыть</a></td>	
						<td><a href="step_2.php?name_file=<?php echo $one_page[2]; ?>&date_file=<?php echo $one_page[1]; ?>">Востановить</a></td>	
					</tr>	
					<?php } ?>
		</table>
		<?php } else { echo "<br><span style='color:red;'>В Веб Архиве нет данных о текущей странице!</span>";} 
	} 
	
	
	else { //Анализ страниц сайта
		
		$count_page = file_get_contents("http://web.archive.org/cdx/search/cdx?url=".$_GET['url']."/&matchType=prefix&showNumPages=true");
		echo "<h3>(Pages $count_page)</h3>";
//$count_page=1;//**************!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!		
		
		$list_of_pages = '';		
		do {
			$count_page=$count_page-1;
			$list_of_pages = file_get_contents("http://web.archive.org/cdx/search/cdx?url=".$_GET['url']."/&matchType=prefix&output=json&page=".$count_page);
			$list_of_pages=json_decode($list_of_pages,true); 
			array_shift($list_of_pages);
			rsort($list_of_pages);
			//print_r($list_of_pages);
			foreach ($list_of_pages as $one_page) {
				if($one_page[4]=='200') {
					
				//чистим урлы, убираем домен и 80 порт, тоесть убираются дубли страниц с 80 портом, www и http
				if (substr_count($one_page[2],':80')) $one_page[2] = str_replace(':80', '', $one_page[2]);
				
				//удаляем GET пераметры из адреса
				if (substr_count($one_page[2],'?')) $one_page[2] = substr($one_page[2], 0, strpos($one_page[2],'?'));				
				
				$tmp_url_id =  substr($one_page[2], strpos($one_page[2], $_GET['url'])+1+strlen($_GET['url']));
				if(!$tmp_url_id) {$tmp_url_id='/';}
				$data[$tmp_url_id] = $one_page[3];
				}
				//$data[$one_page[2]] = $one_page[3];
				//if (!substr_count($one_page[2],'search_type')) $data[$tmp_url_id] = $one_page[3]; исключения из списка файлов и страниц по куску имени
			}
			
			//print_r($data);
			
		} while($count_page > 0);
		 array_multisort($data);
		
		if($data) {
			?>
			<div id="panel">
				<b>Панель</b><br /><br />
				<a id="choise_html">.html (<span></span>)</a><br />
				<a id="choise_php">.php (<span></span>)</a><br />
				<a id="choise_css">.css (<span></span>)</a><br />
				<a id="choise_js">.js (<span></span>)</a><br />
				<a id="choise_jpg">.jpg (<span></span>)</a><br />
				<a id="choise_png">.png (<span></span>)</a><br />
				<a id="choise_gif">.gif (<span></span>)</a><br />
				<a id="choise_pdf">.pdf (<span></span>)</a><br />
				<a id="choise_else">Else (<span></span>)</a><br />
				<a id="choise_rise">Снять Rise (<span></span>)</a><br />
				<a id="choise_none">Снять все (<span></span>)</a><br /><hr />
				<a id="choise_start">START</a><br />
			</div>
			<div id="terminal">
				
				<div class="close">x</div>
				<h3>Терминал</h3>
				<div id="progress_line"><span>0 %</span><div></div></div>
				<div id="progress_status"></div>
				<div id="progress_list"></div>
			</div>
			<script type="text/javascript">
				
			 </script>	
			<table cellspacing='0' style="margin: 0px auto;">
					<tr>
						<td><b>№</b></td>	
						<td><b>Тип Файла</b></td>
						<td><b>Ссылка</b></td>
						<td><b>&nbsp;</b></td>
						<td><b>Востановить</b></td>
					</tr>
			
					<?php
					 $i=1;	
					 foreach ($data as $key => $value) { 
					//	 if($admin->is_set_file($key)	!="color: green") {
						
						//заменяем в адресе спецсимвол пробела
						//$key = str_replace("%20", " ", $key);
						 ?> 										
						<tr>
							<td><?php echo $i++; ?></td>
							<td><?php echo $value; ?></td>
							<td><a href="http://web.archive.org/web/*/<?php echo $_GET['url'].'/'.$key; ?>" target="_blank" style="<?php echo $admin->is_set_file($key)	; ?>"><?php echo $key; ?></a></td>	
							<td><input name="" class="checkbox <?php $admin->type_file($key); $admin->is_set_file2($key); ?>" value="<?php echo $_GET['url'].'/'.$key; ?>" type="checkbox" /></td>
							<td><a href="step_1.php?url=<?php echo $_GET['url'].'/'.$key; ?>&limit=<?php echo $_GET['limit'];?>&submit=Анализ+сайта" target="_blank">Выбрать версию</a></td>	
						</tr>	
						<?php
					//	}						
					 } ?>
		</table>
		<?php } else { echo "<br><span style='color:red;'>В Веб Архиве нет о текущем сайте!</span>";} 
	}	
	
	?>
</body>
</html>