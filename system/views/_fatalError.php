<!DOCTYPE html>
<html lang="nl-BE">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
	<link href="/system/assets/css/uikit.almost-flat.min.css" rel="stylesheet">

</head>
<body style="background-color:#EBEBEB">
<div style="width:40%;height:auto;margin-left:auto;margin-right:auto;margin-top:10%;border: 0px solid black;background-color:white;padding:20px;font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;font-weight: normal;line-height: 20px;color: #444444;">
	<b style="font-size: 240px;position: absolute;margin-left: -140px;margin-top: 100px;">!</b>
	<p style="margin-top:-20px;color:black;font-size:20px;padding:10px">
	<h2>
		<? echo $error['message']; ?>
	</h2>
	<p>
		<pre style="background-color:#EBEBEB;border:1px solid #ddd;white-space:pre-wrap;white-space:-moz-pre-wrap;white-space:-pre-wrap;white-space:-o-pre-wrap;word-wrap:break-word">
			<code>
<?php foreach($lines as $i => $line){
	if($i == 3){
		$style = "background-color:#F2AAAA;";
	}else{
		$style = "color:#999";
	}
	echo '<span style="' . $style . '";width:100%">' . $line . '</span>'; 
} ?>
			</code>
		</pre>
	</p>
	<div class="uk-grid">
		<span class="uk-width-small-1-10">
			File 
		</span>
		<span class="uk-width-small-9-10 text-truncate" style="color:#ADADAD;font-style:italic">
			<? echo $error['file']; ?>
		</span>
	</div>
	<div class="uk-grid" style="margin-top:0px">
		<span class="uk-width-small-1-10">
			Line 
		</span>
		<span class="uk-width-small-9-10 text-truncate" style="color:#ADADAD;font-style:italic">
			<? echo $error['line']; ?>
		</span>
	</div>
</div>
</body>
<html>