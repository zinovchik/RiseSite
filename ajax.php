<?php
include_once('class_and_config.php'); 
$admin = new risesite;
 
$_GET['url'] = str_replace('ampersandtoreplase', "&", $_GET['url']);
//$_GET['url'] = str_replace('ampersandtoreplase', "&", $_GET['url']);
//$_GET['url']  = decodeURIComponent($_GET['url']);
//  iconv('UTF-8','WINDOWS-1251',$data);
//echo str_replace(" ", "%20", "http://web.archive.org/cdx/search/cdx?url=".$_GET['url']."&output=json&limit=".$_GET['limit']);
$list_of_pages = file_get_contents(str_replace(" ", "%20", "http://web.archive.org/cdx/search/cdx?url=".$_GET['url']."&output=json&limit=".$_GET['limit']));
$list_of_pages=json_decode($list_of_pages,true); 

if($list_of_pages) {

	$i=1;		
	array_shift($list_of_pages);
	rsort($list_of_pages);
						 
						 
	foreach ($list_of_pages as $one_page) { 
		if($i && $one_page[4] == '200' && $one_page[1]  < 20071201000000) {
	//if($i && $one_page[4] == '200') {
			$file_200_name = $one_page[2];	
			$file_200_date = $one_page[1];
			$i=0;				 
		}
	}			 
 } else { echo "<a class='notmodified' href = '".$_GET['url']."' target='_blank'>".$_GET['url']."</a><br>"; exit;}




$status_page = '';
$name_page = explode('/',$file_200_name);
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
		mkdir($admin->path.$structure, 0777, true);		
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
} //print_r($name_page);
$file_type = array_pop(explode('.', $name_page[count($name_page)-1]));

// если в адресе есть get параметры, то убираем их
if(strpos($file_type, '?')) {$file_type = substr($file_type, 0, strpos($file_type, '?'));} 
$name_page= implode('/',$name_page);	

// если в адресе есть get параметры, то убираем их	   
if(strpos($name_page, '?')) {$name_page = substr($name_page, 0, strpos($name_page, '?'));}

// если в адресе есть # id параметры, то убираем их
if(strpos($name_page, '#')) {$name_page = substr($name_page, 0, strpos($name_page, '#'));}

// меняем расширение файла из html на php		
$name_page = str_replace(".htm", ".php", $name_page);
$name_page = str_replace("%20", " ", $name_page);

$handle = fopen($admin->path . $name_page, "w");

		
		

//Анализ содержимого страницы, если тип файла html, css, js  
if(in_array($file_type, array('html', 'htm','HTML','HTM','php','aspx','asp'))) {		

	//чтение страницы
	include_once('simple_html_dom.php');
	//echo str_replace("web", "20", "http://web.archive.org/web/");
	$html = file_get_html("http://web.archive.org/web/{$file_200_date}/{$file_200_name}");
	$title =  $html->find("title" ,0)->innertext;		
	//$desctiption = $html->find("meta[name=description]" ,0)->content;
	//$keywords =	$html->find("meta[name=keywords]" ,0)->content;
		
//	foreach($html->find('body >div') as $rs_content_img) {
//			$rs_content_img->outertext = "";		
//	}	
	$content = $html->find("body" ,0);
/*	foreach($content->find('tr > td > table > tbody > tr  > td > table') as $rs_content_img) {
		$rs_content_img->outertext = "<?php require_once ('menu.php'); ?>";
	}
*/
	//если блока контент нет
	if($content) {	} else {
		$content = $html;
	}	

	// убираем из ссылки код веб архива
	foreach($content->find('a') as $rs_content_link) {
		$rs_tmp = $rs_content_link->href;
		$rs_tmp = str_replace(":80", '', $rs_tmp);
		$rs_tmp = str_replace("/web/{$file_200_date}/", '', $rs_tmp);
		$rs_tmp = str_replace(str_replace(":80", '', $domen), $directory_level, $rs_tmp);
		if(!$rs_tmp) {$rs_tmp =$directory_level;} //echo $domen;
		$rs_content_link->href = $rs_tmp;
	}
		
	//--поиск и удаление всех скриптов
	foreach($content->find('script') as $rs_content_img) {
		//$rs_tmp = $rs_content_img->src;
		//if(strpos($rs_tmp, "googlesyndication.com")) {
			$rs_content_img->outertext = "";		
		//}
	}

	//--поиск и удаление всех стилей
	foreach($content->find('style') as $rs_content_img) {
		//$rs_tmp = $rs_content_img->src;
		//if(strpos($rs_tmp, "googlesyndication.com")) {
		$rs_content_img->outertext = "";
		//}
	}


	//--поиск и удаление всех коментариев
	foreach($content->find('comment') as $rs_content_img) {
		//$rs_tmp = $rs_content_img->src;
		//if(strpos($rs_tmp, "googlesyndication.com")) {
			$rs_content_img->outertext = "";		
		//}
	}	
	
	//--поиск и удаление ненужного кода
	foreach($content->find('#wm-ipp') as $rs_content_img) {
		$rs_tmp = $rs_content_img->src;
		//if(strpos($rs_tmp, "googlesyndication.com")) {
			$rs_content_img->outertext = "";
		//}
	}
	
	//--поиск и php файлов
//	foreach($content->find('a') as $rs_content_img) {
//		$rs_tmp = $rs_content_img->href;
//		if(strpos($rs_tmp, ".php")) {
//			$rs_content_img->outertext = "";		
//		}
//	}


	
		//--поиск и php файлов
	foreach($content->find('img') as $rs_content_img) {
		$rs_tmp = $rs_content_img->src;
		if(strpos($rs_tmp, ".php")) {
			$rs_content_img->outertext = "";		
		}
	}
	
	//--поиск и удаление скрипта в ссылках
	foreach($content->find('a') as $rs_content_img) {
		$rs_tmp = $rs_content_img->href;
		if(strpos($rs_tmp, "avascript:")) {
			//echo $rs_content_img->href;
			$rs_content_img->href = "#";		
		}
	}	
	
	foreach($content->find('img') as $rs_content_img) {
		$rs_tmp = $rs_content_img->src;
		$rs_tmp = str_replace(":80", '', $rs_tmp);
		$rs_tmp = str_replace("/web/{$file_200_date}im_/", '', $rs_tmp);
		$rs_tmp = str_replace(str_replace(":80", '', $domen), $directory_level, $rs_tmp);
		$rs_content_img->src = $rs_tmp;
	}
//<<a href="javascript: popUpZCF();">
	foreach($content->find('#sidebarcontainer') as $rs_content_img) {
		$rs_content_img->outertext = '<?php require_once("sidebar.php"); ?>';
	}
		
	//$html = $content->outertext;
	$html = $content-> innertext;
		
	//--поиск и исправление адресов картинок
	rise_links("/web/{$file_200_date}im_/", $html);			
				
	//--поиск и исправление адресов js файлов
	rise_links("/web/{$file_200_date}js_/", $html);			
					
	//--поиск и исправление адресов iframe тегов
	rise_links("/web/{$file_200_date}if_/", $html);
					
	//--поиск и исправление адресов frame тегов
	rise_links("/web/{$file_200_date}fw_/", $html);
					
	//--поиск и исправление адресов css файлов
	rise_links("/web/{$file_200_date}cs_/", $html);
					
	//--поиск и исправление адресов тега object
	rise_links("/web/{$file_200_date}oe_/", $html);

	
//$html->find("#sidebarcontainer") = "55555555";
//	$html = str_replace("�", '&apos;', $html);
	
	$content = $html;
			
	$html = '<?php $title = "'.$title.'"; $path = "'.$directory_level.'"; include($path."header.php");?> <table width="859" height="454" border="2" cellpadding="15" cellspacing="0"><tr><td>'.$content.'</td></tr></table><?php include($path."footer.php");?>';
		
} else {
	$html = file_get_contents("http://web.archive.org/web/{$file_200_date}/{$file_200_name}");
}
		
fwrite($handle, $html);
fclose($handle);
//echo $_GET['i'].") <a href = 'site/".str_replace(".php", ".html", $name_page)."' target='_blank'>$name_page</a><br>";
echo $_GET['i'].") <a href = 'site/". $name_page."' target='_blank'>$name_page</a><br>";

// Функция востанавливает ссылки удаляя код вебархива
function rise_links($search_text, &$page) {
	$tmp = substr_count($page,$search_text);
	$page = str_replace($search_text, '', $page);
	return;
}
