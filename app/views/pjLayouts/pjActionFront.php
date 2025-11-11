<?php
require $content_tpl;
$content = ob_get_contents();
ob_end_clean();

$content = preg_replace('/\r\n|\n|\t/', '', $content);
$content = str_replace("'", "\"", $content);

$pattern = '|<script.*>(.*)</script>|';
if (preg_match($pattern, $content, $matches))
{
	$content = preg_replace($pattern, '', $content);
}
?>
var install_element = null;
var scripts = document.getElementsByTagName("script");
for (var i = 0; i < scripts.length; i++) 
{
	var src = scripts[i].src;
	if(src.indexOf("index.php?controller=pjFront&action=pjActionLoad") != -1)
	{
		install_element = scripts[i];
	}
}
var div = document.createElement('div');
div.innerHTML = '<?php echo $content;?>';
if(install_element != null)
{
	install_element.parentNode.insertBefore(div, install_element);
}else{
	document.body.appendChild(div);
}
<?php
if ($matches)
{
	?>
	var script = document.createElement('script');
	script.text = '<?php echo $matches[1];?>';
	if(install_element != null)
	{
		install_element.parentNode.insertBefore(script, install_element);
	}else{
		document.body.appendChild(script);
	}
	<?php
}
?>