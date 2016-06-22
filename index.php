<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>Application for restore website</title>
	<style type="text/css">
		label > span {
				display: block;
		    float: left;
		    width: 200px;
		}
		
		form {
		    border: 1px solid lightslategray;
		    border-radius: 10px;
		    box-shadow: 0 0 10px;
		    display: block;
		    line-height: 30px;
		    margin: 50px auto;
		    padding: 20px;
		    text-align: center;
		    width: 550px;
		}
	</style>
</head>
<body>
	<form action="step_1.php" method="GET">
		<legend> 
			<b>Bостановления сайта из веб архива	</b>
		</legend>
		<label>
		<span>Адрес сайта: http://</span>
			<input name="url" id="url" type="text" />	
		</label>
		<br />
		<label>
		<span>Лимит записей: (штк)</span>
			<input name="limit" id="limit" type="text" value="500" />	
		</label>
		<br />
		<label>
		Анализ доступных страниц сайта: <input name="allpage" id="allpage" type="checkbox" />
				&nbsp;
		</label>
		<br />
	  <label>
		<span>&nbsp;</span>
			<input name="submit" id="submit" type="submit" value="Анализ сайта" />	
		</label>	
	</form>
</body>
</html>