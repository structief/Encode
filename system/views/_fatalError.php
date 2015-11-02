<!DOCTYPE html>
<html lang="nl-BE">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
	<link href="/Skeleton/application/assets/css/base.css" rel="stylesheet">
	<link href="/Skeleton/application/assets/css/layout.css" rel="stylesheet">
	<link href="/Skeleton/application/assets/css/skeleton.css" rel="stylesheet">
	<link href="/Skeleton/application/assets/css/ionicons.css" rel="stylesheet">

</head>
<body style="background-color:#EBEBEB">
<div style="width:40%;height:auto;margin-left:auto;margin-right:auto;margin-top:10%;border: 0px solid black;background-color:white;padding:10px">
	<b style="font-size:240px;position:relative;left:-200px;top:20px"><i class="ion-alert"></i></b>
	<p style="margin-top:-220px;color:black;font-size:20px;padding:10px">
	<i class="ion-flag" style="font-size:20pt;padding:10px"></i>
		<? echo $error['message']; ?>
	</p>
	<p>
		<pre style="background-color:#EBEBEB;border:1px solid black;white-space:pre-wrap;white-space:-moz-pre-wrap;white-space:-pre-wrap;white-space:-o-pre-wrap;word-wrap:break-word">
			<code>
<? echo $lines[0]; ?>
<span style="background-color:#f2aaaa;width:100%"><? echo $lines[1] ?></span>
<? echo $lines[2] ?>
			</code>
		</pre>
	</p>
	<p style="color:#ADADAD">
		<i class="ion-document-text"></i>   <i><? echo $error['file']; ?></i><br/>
		<i class="ion-drag"></i>   <i><? echo $error['line']; ?></i>
	</p>
</div>	
</body>