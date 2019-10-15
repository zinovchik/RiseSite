<?php 
include_once('class_and_config.php');
$admin = new risesite;

$_GET['url'] = str_replace('ampersandtoreplase', "&", $_GET['url']);
$list_of_pages = file_get_contents(str_replace(" ", "%20", "http://web.archive.org/cdx/search/cdx?url=" . $_GET['url'] . "&output=json&limit=" . $_GET['limit']));
$list_of_pages = json_decode($list_of_pages, true);
$file_200_name = '';
$file_200_date = '';

if ($list_of_pages) {

    $i = 1;
    array_shift($list_of_pages);
    rsort($list_of_pages);


    foreach ($list_of_pages as $one_page) {
        if ($i && $one_page[4] == '200') {
//        if ($i && $one_page[4] == '200' && $one_page[1] < '20170330143653') {
            $file_200_name = $one_page[2];
            $file_200_date = $one_page[1];
            $i = 0;
        }
    }
} else {
    echo "<a class='not_modified' href = 'http://web.archive.org/web/*/{$_GET['url']}' target='_blank'>{$_GET['url']}</a><br>";
    exit;
}


$status_page = '';
$file_200_name = strtolower($file_200_name);
$name_page = explode('/', $file_200_name);

//Если не корневая директория, то создать директорию
$directory_level = '';
if (count($name_page) > 4) {
    $structure = $name_page;
    array_shift($structure);
    array_shift($structure);
    array_shift($structure);
    array_pop($structure);
    for ($j = count($structure); $j > 0; $j--) $directory_level .= '../'; // нужно  если востанавливаем пути в файле который находится не в корне (используется в блоке удаления собственого домена для создания относительных путей)
    $structure = implode('/', $structure);
    if (!is_dir($admin->path . $structure)) {
        $structure = str_replace("%20", " ", $structure);
        mkdir($admin->path . $structure, 0777, true);
    }
}

//********************************Создание файла********************************


$domain = array_shift($name_page) . '//';
$domain .= array_shift($name_page);
$domain .= array_shift($name_page) . '/';

// если файл находится в подкаталоге, но в адресе не указан сам файл index		
if (count($name_page) > 1) {
    $name_page[count($name_page) - 1] = $name_page[count($name_page) - 1] == '' ? 'index.html' : $name_page[count($name_page) - 1];
} else {
    $name_page[0] = $name_page[0] == '' ? 'index.html' : $name_page[0];
} //print_r($name_page);
$file_type = array_pop(explode('.', $name_page[count($name_page) - 1]));

//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//$name_page[count($name_page) - 1] = '_'.$name_page[count($name_page) - 1].'.php';

// если в адресе есть get параметры, то убираем их
if (strpos($file_type, '?')) {
   // $file_type = substr($file_type, 0, strpos($file_type, '?'));
}
$name_page = implode('/', $name_page);

// если в адресе есть get параметры, то убираем их	   
if (strpos($name_page, '?')) {
    $name_page = substr($name_page, 0, strpos($name_page, '?'));
}

// если в адресе есть # id параметры, то убираем их
if (strpos($name_page, '#')) {
    $name_page = substr($name_page, 0, strpos($name_page, '#'));
}



// меняем расширение файла из html на php		
$name_page = str_replace(".html", ".php", $name_page);
$name_page = str_replace(".htm", ".php", $name_page);
$name_page = str_replace("%20", " ", $name_page);

//удаляем GET пераметры из адреса
if (substr_count($name_page, '?')) $name_page = substr($name_page, 0, strpos($name_page, '?'));

$handle = fopen($admin->path . $name_page, "w");
//$handle = fopen($admin->path . '_'.$name_page.'.php', "w");

//!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//$file_type = 'php';

//Анализ содержимого страницы, если тип файла html, css, js  
//if (1) {
if (in_array($file_type, array('html', 'htm', 'HTML', 'HTM', 'php', 'aspx', 'asp'))) {
//
    //чтение страницы
    include_once('simple_html_dom.php');

    //echo str_replace("web", "20", "http://web.archive.org/web/");
    $html = file_get_html("http://web.archive.org/web/{$file_200_date}/{$file_200_name}");

    // убираем код веб архива
    foreach ($html->find('script, noscript, #wm-ipp, comment, style') as $elem) {
        $elem->outertext = "";
    }

    $title = $html->find("title", 0)->innertext;
    $description = $html->find("meta[name=Description],meta[name=description]" ,0)->content;
    $keywords =	$html->find("meta[name=Keywords],meta[name=keywords]" ,0)->content;
    //$h1 = $html->find("h1", 0)->innertext;
//    $style_add = $html->find("head style", 0)->innertext;

    $contentTMP = $html->find("#mainContent", 0)->outertext;
//    $contentTMP = $html->find("#AutoNumber18", 0)->outertext;
//    $contentTMP = $html->find("#ContentAreaNoSide", 0)->innertext;
    $content = str_get_html($contentTMP);

   // $content =  $content->find("table", 0);
    //$breadcrumb = $html->find("#breadcrumb", 0);
    /*	foreach($content->find('tr > td > table > tbody > tr  > td > table') as $rs_content_img) {
            $rs_content_img->outertext = "<?php require_once ('menu.php'); ?>";
        }
    */
    //если блока контент нет
    if ($content) {
    } else {
//       $content = $html->find("body", 0);
       $content = "";
        fclose($handle);
        if(unlink ( $admin->path . $name_page )){
            echo "rm";
        } else {
          echo "no";
        }
       exit;
    }

    // убираем код сайдбара и заменяем на php
//    foreach ($content->find('#sidebar') as $blog_sidebar) {
//        $blog_sidebar->innertext = '<?php include($path."sidebar.php"); ? >';
//    }

    //
//    foreach ($content->find('*[style=position]') as $element) {
//        $element->outertext = '';
//    }
//
//    foreach ($content->find('.blogCommentAuthor a.name, .blogCommentAuthor a.link') as $element) {
//        $element->href = '#';
//    }



    // убираем из ссылки код веб архива
    foreach ($content->find('a') as $rs_content_link) {
        $rs_tmp = strtolower($rs_content_link->href);
        $rs_tmp = str_replace(":80", '', $rs_tmp);
        $rs_tmp = str_replace("/web/{$file_200_date}/", '', $rs_tmp);
        $rs_tmp = str_replace(str_replace(":80", '', $domain), $directory_level, $rs_tmp);
//        $rs_tmp = str_replace(str_replace(":80", '', $domain), '', $rs_tmp);
        if (!$rs_tmp) {
            $rs_tmp = $directory_level;
        } //echo $domain;
        $rs_content_link->href = $rs_tmp;
    }


    // убираем из embed код веб архива
    foreach ($content->find('embed') as $rs_content_link) {
        $rs_tmp = $rs_content_link->src;
        $rs_tmp = str_replace(":80", '', $rs_tmp);
        $rs_tmp = str_replace("/web/{$file_200_date}oe_/", '', $rs_tmp);
        //$rs_tmp = str_replace(str_replace(":80", '', $domain), $directory_level, $rs_tmp);
//        if (!$rs_tmp) {
//            $rs_tmp = $directory_level;
//        } //echo $domain;
        $rs_content_link->src = $rs_tmp;
    }

    // убираем из object код веб архива
    foreach ($content->find('object') as $rs_content_link) {
        $rs_tmp = $rs_content_link->codebase;
        $rs_tmp = str_replace(":80", '', $rs_tmp);
        $rs_tmp = str_replace("/web/{$file_200_date}oe_/", '', $rs_tmp);
//        $rs_tmp = str_replace(str_replace(":80", '', $domain), $directory_level, $rs_tmp);
//        if (!$rs_tmp) {
//            $rs_tmp = $directory_level;
//        } //echo $domain;
        $rs_content_link->codebase = $rs_tmp;
    }

    // убираем из iframe код веб архива
    foreach ($content->find('iframe') as $rs_content_link) {
        $rs_tmp = $rs_content_link->src;
        $rs_tmp = str_replace(":80", '', $rs_tmp);
        $rs_tmp = str_replace("//web.archive.org/web/{$file_200_date}if_/", '', $rs_tmp);
        $rs_tmp = str_replace("http:http:", 'http:', $rs_tmp);
        $rs_tmp = str_replace("http://livestream.com", 'https://livestream.com', $rs_tmp);
//        $rs_tmp = str_replace("http://www.youtube.com/v/", 'http://www.youtube.com/embed/', $rs_tmp);
//        $rs_tmp = str_replace(str_replace(":80", '', $domain), $directory_level, $rs_tmp);
//        if (!$rs_tmp) {
//            $rs_tmp = $directory_level;
//        } //echo $domain;
        $rs_content_link->src = $rs_tmp;
    }

        // убираем из ссылки код веб архива breadcrumb
//            foreach ($breadcrumb->find('a') as $rs_content_link) {
//                $rs_tmp = $rs_content_link->href;
//                $rs_tmp = str_replace(":80", '', $rs_tmp);
//                $rs_tmp = str_replace("/web/{$file_200_date}/", '', $rs_tmp);
//                $rs_tmp = str_replace(str_replace(":80", '', $domain), $directory_level, $rs_tmp);
//                if (!$rs_tmp) {
//                    $rs_tmp = $directory_level;
//                } //echo $domain;
//                $rs_content_link->href = $rs_tmp;
//            }

    //--поиск и удаление всех скриптов
    foreach ($content->find('script') as $rs_content_img) {
        //$rs_tmp = $rs_content_img->src;
        //if(strpos($rs_tmp, "googlesyndication.com")) {
        $rs_content_img->outertext = "";
        //}
    }

    //--поиск и удаление всех стилей
    foreach ($content->find('style') as $rs_content_img) {
        //$rs_tmp = $rs_content_img->src;
        //if(strpos($rs_tmp, "googlesyndication.com")) {
        $rs_content_img->outertext = "";
        //}
    }


    //--поиск и удаление всех коментариев
    foreach ($content->find('comment') as $rs_content_img) {
        //$rs_tmp = $rs_content_img->src;
        //if(strpos($rs_tmp, "googlesyndication.com")) {
        $rs_content_img->outertext = "";
        //}
    }


    //--поиск и удаление всех noscript
    foreach ($content->find('noscript') as $rs_content_img) {
        //$rs_tmp = $rs_content_img->src;
        //if(strpos($rs_tmp, "googlesyndication.com")) {
        $rs_content_img->outertext = "";
        //}
    }

    //--поиск и удаление ненужного кода
    foreach ($content->find('#wm-ipp-base') as $rs_content_img) {
//        $rs_tmp = $rs_content_img->src;
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
    foreach ($content->find('img') as $rs_content_img) {
        $rs_tmp = strtolower($rs_content_img->src);
        if (strpos($rs_tmp, ".php")) {
            $rs_content_img->outertext = "";
        }
        if (strpos($rs_tmp, "action.naacp.org")) {
            $rs_content_img->outertext = "";
        }
    }
//$adsf=0;
    //--поиск и удаление скрипта в ссылках
    foreach ($content->find('a') as $rs_content_img) {
        $rs_tmp = $rs_content_img->href;

//        // Проверка существование внешней ссылки (URL)
//        if (strpos($rs_tmp, "mailto:") === 0) {
//        } else {
//            $url_c = parse_url($rs_tmp);
//            if (!empty($url_c['host'])) {
//                // Ответ сервера
//                if ($answer = @get_headers($rs_tmp)) {
//                    if (!(in_array(substr($answer[0], 9, 3), array('200', '301','302')))){
//                        //удалить ссылку
//                        $rs_content_img->outertext = $rs_content_img->plaintext;$adsf++;
//                    }
//                }
//            }
//        }

        if (strpos($rs_tmp, "avascript:")) {
            //echo $rs_content_img->href;
            $rs_content_img->href = "#";
        }



    }

    //echo $adsf;
    foreach ($content->find('img') as $rs_content_img) {
        $rs_tmp = strtolower($rs_content_img->src);
        $rs_tmp = str_replace(":80", '', $rs_tmp);
        $rs_tmp = str_replace("/web/{$file_200_date}im_/", '', $rs_tmp);
//        $rs_tmp = str_replace(str_replace(":80", '', $domain), $directory_level, $rs_tmp);
        $rs_tmp = str_replace(str_replace(":80", '', ''), $directory_level, $rs_tmp);
        $rs_content_img->src = $rs_tmp;
    }
//<<a href="javascript: popUpZCF();">
/*    foreach ($content->find('#sidebarcontainer') as $rs_content_img) {
        $rs_content_img->outertext = '<?php require_once($path."sidebar.php"); ?>';
    }
*/


    $html = $content->outertext;
    //$html = $content->innertext;




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


//$html->find("#AutoNumber14")->outertext = "55555555";
//	$html = str_replace("�", '&apos;', $html);

    //$content = $breadcrumb . $html;
    $content = str_replace('http://web.archive.org', '', $content);
    $content = str_replace('http://worldsbiggestwriting.com', '', $content);
    $content = str_replace('http://www.worldsbiggestwriting.com', '', $content);

    $html = '<?php $title = "' . $title . '"; $description = "'.$description.'"; $keywords = "'.$keywords.'"; $path = "'.$directory_level.'"; include($path."template/header.php");?> ' . $content . ' <?php include($path."template/footer.php");?>';
/*    $html = '<?php $title = "' . $title . '"; $description = "'.$description.'"; $path = "' . $directory_level . '"; include($path."header.php");?> ' . $content . ' <?php include($path."footer.php");?>';  */

} elseif (in_array($file_type, array('css', 'js'))) {
    //http://web.archive.org/web/20171015154108if_/http://q2learning.com/css/screen_layout_large.css
    $html = file_get_contents("http://web.archive.org/web/{$file_200_date}if_/{$file_200_name}");

    //foreach($html->find('comment') as $element) $element->outertext = '';
} else {
    $html = file_get_contents("http://web.archive.org/web/{$file_200_date}if_/{$file_200_name}");
}

fwrite($handle, $html);
fclose($handle);
//!!!!!!!!!!!!!!!!!!!!!!!
//$htaccess = $name_page;
//$htaccess = str_replace('_','',$htaccess);
//$htaccess = str_replace('.php','',$htaccess);

echo $_GET['i'].") <a href = 'http://rs-site.max/".str_replace(".php", ".htm", $name_page)."' target='_blank'>$name_page</a><br>";
//echo  " <a href = 'http://rs-site.max/" . $htaccess . "' target='_blank'>RewriteRule ^$htaccess $name_page</a><br>"; //^content/main$ content/_main.php

// Функция востанавливает ссылки удаляя код вебархива
function rise_links($search_text, &$page)
{
    $tmp = substr_count($page, $search_text);
    $page = str_replace($search_text, '', $page);
    return;
}

