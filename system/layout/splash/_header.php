<?php
	//Header
	$this->assets = new Assets();
?>	
<!DOCTYPE html>
<html lang="nl-BE">
<head>
	<title>Encode Framework!</title>
	<!-- SEO tags -->
	<link rel="author" href="https://plus.google.com/u/0/114866010643977884117/posts"/>
	<meta name="keywords" content="Encode RT, Riding Team, Ebro Productions, Koen Everaert, Jan Everaert, Framework, PHP, HTML, CSS" />
	<meta name="description" content="Encode RT is a ready-to-use, free for all framework that allows easy customization and quick setup." />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
	<!-- end of SEO tags -->

	<!-- Facebook tags -->
	<meta property="og:title" content="Encode RT framework" />
	<meta property="og:type" content="website" />
	<meta property="og:image" content="<?= $this->assets->get("css", "logo"); ?>" />
	<meta property="og:url" content="<? echo $_SERVER['REQUEST_URI']; ?>"/>
	<meta property="og:description" content="Encode RT is a ready-to-use, free for all framework that allows easy customization and quick setup." />
	<!-- end of Facebook tags -->

	<!-- Twitter tags -->
	<meta name="twitter:card" content="summary">
	<meta name="twitter:url" content="<? echo $_SERVER['REQUEST_URI']; ?>">
	<meta name="twitter:title" content="Encode RT framework">
	<meta name="twitter:description" content="Encode RT is a ready-to-use, free for all framework that allows easy customization and quick setup.">
	<meta name="twitter:image" content="<?= $this->assets->get("css", "logo"); ?>">
	<!-- end of Twitter tags -->

	<link rel="icon" href="<?= $this->assets->get("image", 'favicon'); ?>">

	<!--Basic stylesheets-->
	<link href="<?= $this->assets->get("_css", "uikit.almost-flat.min"); ?>" rel="stylesheet">

	<!--Basic javascripts-->
	<script type="text/javascript" src="<?= $this->assets->get("js", "jquery"); ?>"></script>

	<!-- Add fancyBox main JS and CSS files -->
	<script type="text/javascript" src="<?= $this->assets->get("_js", "jquery.fancybox") ?>"></script>
	<link rel="stylesheet" type="text/css" href="<?= $this->assets->get("_css", "jquery.fancybox") ?>" media="screen" />
</head>
<body>
<div class="uk-container uk-container-center uk-margin-top uk-margin-large-bottom">
