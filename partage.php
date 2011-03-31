<?php


function html_pre($title){ // Afficher le prefixe html

echo "
<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">

<html>
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\" >
<title>
$title
</title>
";

// Pour la feuille de style
// echo "<link rel=\"STYLESHEET\" type=\"text/css\" href=\"../../active.css\" >";

echo "</head>
<body>
<h2>$title</h2>
"
;



}

function html_post(){ // Afficher le postfixe html
echo "
</body>
</html>
"
;
}
?>