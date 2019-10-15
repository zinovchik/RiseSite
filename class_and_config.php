<?php

class risesite
{

    public $path = "/Users/user/Documents/WebServer/RiseSite/site/"; //путь куда будут востанавливаться файлы сайта

//**********************************************************************************************

    public function trimblock($text1, $text2, $delta1, $delta2, &$page, $message)
    {
        $start = strpos($page, $text1) - $delta1;
        $end = strpos($page, $text2) - $start + $delta2;

        if (($start + $delta1) != 0) {
            $tmp_string = '';
            for ($i = 0; $i <= $end; $i++) {
                $tmp_string .= $page[($start + $i)];
            }
            $page = str_replace($tmp_string, '', $page);
            echo "<br><span style='color:green;'> * $message удален</span>";
        } else {
            echo "<br><span style='color:red;'> * $message не удален</span>";
        }
    }

//**********************************************************************************************


    public function rise_links($search_text, $message, &$page)
    {
        $tmp = substr_count($page, $search_text);
        $page = str_replace($search_text, '', $page);
        echo "<br><span style='color:green;'> * $message (удалено $tmp: \"$search_text\")</span>";
    }

//**********************************************************************************************

    public function search_all_list_links($title, $regular, &$page, $domen)
    {
        echo "<hr><h3>$title:</h3>
				<table><tbody>
			  		<tr>
				  		<td>№</td>
				  		<td>URL</td>
			  		</tr>";
        preg_match_all($regular, $page, $matches);
        // Берём то место, где сама ссылка (благодаря группирующим скобкам в регулярном выражении)
        $urls = $matches[1];

        /* Выводим все линки */
        for ($i = 0; $i < count($urls); $i++) {

            //если ранее к домену добавляли ../ то убираем их
            $urls[$i] = str_replace('../', '', $urls[$i]);

            // если в адресе есть get параметры, то убираем их
            $tmp_url = strpos($urls[$i], '?') ? substr($urls[$i], 0, strpos($urls[$i], '?')) : $urls[$i];

            echo "<tr>
					  <td>$i</td>
					  <td><a style='color:" . (file_exists(($this->path . $tmp_url)) ? 'green' : 'red') . ";' href='step_1.php?url=$domen$urls[$i]&limit=500&submit=Анализ+сайта' target='_blank'>$urls[$i]</a></td>
				 </tr>";
        }
        echo "</tbody></table>";
    }

//**********************************************************************************************

    public function is_set_file($key, $type = '')
    {

        //удаляем GET пераметры из адреса
        if (substr_count($key, '?')) $key = substr($key, 0, strpos($key, '?'));

        if($type == 'text/html') {

            if (strpos($key, ".html")) {
                $key = str_replace(".html", ".php", $key);
            }
            if (strpos($key, ".htm")) {
                $key = str_replace(".htm", ".php", $key);
            }

            if(strpos($key, "%20")) {$key=str_replace("%20", " ", $key);}

            if (strpos($key, ".HTML")) {
                $key = str_replace(".HTML", ".php", $key);
            }
            if (strpos($key, ".") === FALSE) {
                if ($key[(strlen($key) - 1)] == '/') {
                    $key .= 'index.php';
                } else {
                    $key .= '/index.php';
                }
            }
        }


        return file_exists(($this->path . $key)) ? 'color: green' : 'color: red';
    }

    public function is_set_file2($key, $type)
    {

        //удаляем GET пераметры из адреса
        if (substr_count($key, '?')) $key = substr($key, 0, strpos($key, '?'));

        if($type == 'text/html') {
            if (strpos($key, ".html")) {
                $key = str_replace(".html", ".php", $key);
            } elseif (strpos($key, ".htm")) {
                $key = str_replace(".htm", ".php", $key);
            }
            if (strpos($key, ".HTML")) {
                $key = str_replace(".HTML", ".php", $key);
            }
            if (strpos($key, ".") === FALSE) {
                if ($key[(strlen($key) - 1)] == '/') {
                    $key .= 'index.php';
                } else {
                    $key .= '/index.php';
                }
            }
        }

        echo file_exists(($this->path . $key)) ? ' choice_rise' : '';
    }

    public function type_file($key)
    {
        if (strpos($key, ".html")) {
            echo "choice_html";
            return;
        } elseif (strpos($key, ".HTML")) {
            echo "choice_html";
            return;
        } elseif (strpos($key, ".htm")) {
            echo "choice_htm";
            return;
        } elseif (strpos($key, ".php")) {
            echo "choice_php";
            return;
        } elseif (strpos($key, ".css")) {
            echo "choice_css";
            return;
        } elseif (strpos($key, ".js")) {
            echo "choice_js";
            return;
        } elseif (strpos($key, ".jpg")) {
            echo "choice_jpg";
            return;
        } elseif (strpos($key, ".jpeg")) {
            echo "choice_jpg";
            return;
        } elseif (strpos($key, ".png")) {
            echo "choice_png";
            return;
        } elseif (strpos($key, ".gif")) {
            echo "choice_gif";
            return;
        } elseif (strpos($key, ".pdf")) {
            echo "choice_pdf";
            return;
        } else {
            echo "choice_else";
            return;
        }
    }


}