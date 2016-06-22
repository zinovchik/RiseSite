<?php include_once('class_and_config.php'); 
$admin = new risesite;
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>Application for restore website: step 2</title>
	<style type="text/css">

	</style>	
</head>
<body>
	<h3>Application for restore website: step 2<br /><?php echo $url= "http://web.archive.org/web/$_GET[date_file]/$_GET[name_file]"; ?></h3>
	<?php	
	 
		$name_page = explode('/',$_GET['name_file']);
		//Если не корневая директория, то создать директорию
		$directory_level='';
		if(count($name_page > 5)) 
		{ 
			$structure = $name_page;
			array_shift($structure);
		  array_shift($structure);
		  array_shift($structure);
		  array_pop($structure);
		  for($j=count($structure); $j>0;$j--) $directory_level.='../'; // нужно  если востанавливаем пути в файле который находится не в корне (используется в блоке удаления собственого домена для создания относительных путей)
		  $structure= implode('/',$structure);
		  if(!is_dir($admin->path.$structure)) 
		  {
		  	echo (mkdir($admin->path.$structure, 0777, true)) ? '<br><span style="color:green;"> * Директория создана и открыта для чтения</span>' : '<br><span style="color:red;"> * Ошибка! Директория не создана!</span>';		
			} 
		}
		
//********************************Создание файла********************************
		
		
		$domen=array_shift($name_page).'//';
		$domen.=array_shift($name_page);
		$domen.=array_shift($name_page).'/'; 
		
		// если файл находится в подкаталоге, но в адресе не указан сам файл index		
		if(count($name_page) > 1) {
			$name_page[count($name_page)-1] = $name_page[count($name_page)-1] == '' ? 'index.html' : $name_page[count($name_page)-1] ;
		} 
		else {
			$name_page[0] = $name_page[0]=='' ? 'index.html' : $name_page[0];
		} print_r($name_page);
		$file_type = array_pop(explode('.', $name_page[count($name_page)-1]));
		if(strpos($file_type, '?')) {$file_type = substr($file_type, 0, strpos($file_type, '?'));} // если в адресе есть get параметры, то убираем их
		$name_page= implode('/',$name_page);	
	   
	   if(strpos($name_page, '?')) {$name_page = substr($name_page, 0, strpos($name_page, '?'));} // если в адресе есть get параметры, то убираем их
		
		$name_page = str_replace(".html", ".php", $name_page);// меняем расширение файла из html на php
		
		$handle = fopen($admin->path . $name_page, "w");
		echo $handle ? '<br><span style="color:green;"> * файл создан и открыт для чтения ('.$admin->path . $name_page.')</span>' : '<br><span style="color:red;"> * Ошибка! файл не создан  ('.$admin->path . $name_page.') !</span>';
		
		//чтение страницы
		$page = file_get_contents($url);
		echo $page ? '<br><span style="color:green;"> * Код страницы извлечен с веб архива</span>' : '<br><span style="color:red;"> * Ошибка! Код страницы не извлечен с веб архива!</span>';		
		
		
		
		
		
		//Анализ содержимого страницы, если тип файла html, css, js  
		if(in_array($file_type, array('html', 'htm','css','js','HTML','HTM','php','aspx','asp'))) {
			
			echo $start =  strpos($page, '<div id="content" class="layout').'----';
		echo $length = strpos($page, '<div id="footer">') - $start-27;
		$page = substr($page, $start, $length);	
			
			$needle_files = array();
			
			//--поиск первого блока веб архива
			$admin->trimblock('<script type="text/javascript" src="/static/js/analytics.js"></script>','banner-styles.css"/>', 2, 21, $page, 'первый блок веб архива');
			
			//--поиск второго блока веб архива
			$admin->trimblock('<!-- BEGIN WAYBACK TOOLBAR INSERT -->','<!-- END WAYBACK TOOLBAR INSERT -->', 2, 35, $page, 'второй блок веб архива');
					
			//--поиск третьего блока веб архива
			$admin->trimblock('FILE ARCHIVED ON','SECTION 108(a)(3)).', 15, 22, $page, 'третий блок веб архива');
		
			//--поиск и исправление ссылок
			$admin->rise_links("/web/$_GET[date_file]/", "ссылки на странице исправлены", $page);			
			
			//--поиск и исправление адресов картинок
			$admin->rise_links("/web/$_GET[date_file]im_/", "адреса картинок исправлены", $page);			
			
			//--поиск и исправление адресов js файлов
			$admin->rise_links("/web/$_GET[date_file]js_/", "адреса js файлов исправлены", $page);			
			
			//--поиск и исправление адресов iframe тегов
			$admin->rise_links("/web/$_GET[date_file]if_/", "адреса iframe тегов исправлены", $page);
			
			//--поиск и исправление адресов frame тегов
			$admin->rise_links("/web/$_GET[date_file]fw_/", "адреса frame тегов исправлены", $page);
			
			//--поиск и исправление адресов css файлов
			$admin->rise_links("/web/$_GET[date_file]cs_/", "адреса css файлов исправлены", $page);
			
			//--поиск и исправление адресов тега object
			$admin->rise_links("/web/$_GET[date_file]oe_/", "адреса тега object исправлены", $page);
			
//********************************************************************************
			//--поиск и исправление ВРЕМЕННЫХ кусков кода
			$tmp_string="<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-20012027-3', 'teachertownhall.org');
  ga('send', 'pageview');

</script>";
			$tmp = substr_count($page,$tmp_string);
			$page = str_replace($tmp_string, '', $page	);
			echo "<br><span style='color:green;'> * временные куски кода исправлены (удалено $tmp)</span>";
			
			
//********************************************************************************			
			
			//--поиск и удаление собственного домена, для создания относительных путей
			
			echo $tmp_strrpos = strrpos($domen,':');

			$domen = $tmp_strrpos == 4 ? $domen : substr($domen, 0, $tmp_strrpos).'/';
			$tmp = substr_count($page,$domen);
			$page = str_replace($domen, $directory_level, $page	);
			echo "<br><span style='color:green;'> * созданы относительные путя (удалено $tmp: \"$domen\")</span>";
			

//********************************************************************************

			//--поиск и удаление ВРЕМЕННЫХ кусков кода (вставить нужный кусок кода в архив)			
			$array_tmp_cut_code = array('<!--		<h3>Resources</h3>
			<ul class="type1">
				<li>
					<a href="http://www.betweenmoms.com" TARGET="_blank">Between Moms - A Resource For Moms!</a>
				</li>
				<li>
					<a href="http://www.momtomomchat.com" TARGET="_blank">Mom To Mom Chat.com - A friendly place to meet for ALL Moms.</a>
				</li>

				<li>
					<a href="http://www.singlespouse.com" TARGET="_blank">SingleSpouse.com - A Community for Single Parents</a>
				</li>

				<li>
					<a href="http://www.mrdad.com" TARGET="_blank">MrDad.com - Parenting expertise and advice for fathers from best selling author Armin Brott.</a>
				</li>
				<li>
					<a href="http://www.brendanixon.com" TARGET="_blank">Brenda Nixon - Speaker/Writer/Educator - Powerful, high-content workshops to parents and professionals on child behavior and guidance.</a>
				</li>
				<li>
					<a href="http://www.naesp.org/client_files/what_parents_should.pdf" TARGET="_blank">What Parents Should Look For In Their Child\'s Elementary School - from the National Association of Elementary School Principals</a>
				</li>
			</ul>-->');

			foreach ($array_tmp_cut_code as $tmp_string){
				$tmp = substr_count($page,$tmp_string);
				$page = str_replace($tmp_string, '', $page);
				echo "<br><span style='color:green;'> * временные куски кода исправлены (удалено $tmp)</span>";
			}			

//********************************************************************************			
			
			//--поиск кода гугл аналитики
			$tmp_strrpos = strrpos($page, 'google-analytics');
			echo $tmp_strrpos ? "<br><span style='color:red;'> * в странице есть код гугл аналитики!!!</span>" : '';
			
			//--поиск кода веб архива
			$tmp_strrpos = strrpos($page, 'web/');
			echo $tmp_strrpos ? "<br><span style='color:red;'> * в странице есть код веб архива!!!</span>" : '';
			
			
			// Выводим список линков в файле
			$admin->search_all_list_links("Линки на странице", "/<link[\s]{1}[^>]*[Hh][Rr][Ee][Ff][^=]*=[ '\"\s]*([^ \"'>\s#]+)[^>]*>/", $page, $domen);		
			
			// Выводим список скриптов в файле
			$admin->search_all_list_links("Скрипты на странице", "/<script[\s]{1}[^>]*[Ss][Rr][Cc][^=]*=[ '\"\s]*([^ \"'>\s#]+)[^>]*>/", $page, $domen);	
			
			// Выводим список Object в файле
			$admin->search_all_list_links("Object на странице", "/<embed[\s]{1}[^>]*[Ss][Rr][Cc][^=]*=[ '\"\s]*([^ \"'>\s#]+)[^>]*>/", $page, $domen);
				
			// Выводим список картинок в файле
			$admin->search_all_list_links("Изображения на странице", "/<[Ii][Mm][Gg][\s]{1}[^>]*[Ss][Rr][Cc][^=]*=[ '\"\s]*([^ \"'>\s#]+)[^>]*>/", $page, $domen);
			  
			// Выводим список ссылок в файле
			$admin->search_all_list_links("Ссылки на странице", "/<[Aa][\s]{1}[^>]*[Hh][Rr][Ee][Ff][^=]*=[ '\"\s]*([^ \"'>\s#]+)[^>]*>/", $page, $domen);
		
$page = '<?php 
$title="Schools K-12 - Reports on Schools in California, Texas, Florida, Arizona and more. ";
$desctiption = "Get school information on public, private, and charter elementary, middle and high schools throughout the U.S. ";
$keywords = "School districts, high schools, private schools, public schools, elementary schools, charter, houston, Miami-dade, phoenix, orange county, los angeles, san francisco, san diego, california, arizona, texas, florida";
$page = "";
$level = "'.$directory_level.'";
require_once($level."header.php"); 
?>
'.$page.'<?php require_once($level."footer.php"); ?>';		
		}
		
		
		
		
		fwrite($handle, $page);
		fclose($handle);
		echo "<br><span style='color:green;'> * Обработаные данные записаны в файл (<a href = 'site/$name_page'>Открыть востановленый файл</a>)</span>";
?>
</body>
</html>