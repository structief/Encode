<?php
	$this->assets = new Assets();
?><!DOCTYPE html>
<html lang="nl-BE">
<head>
	<title><? echo TITLE; ?></title>

	<!-- Meta -->
	<meta id="view" name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
    <!-- SEO tags -->
	<link rel="author" href="https://plus.google.com/u/0/114866010643977884117/posts"/>
	<meta name="keywords" content="Encode, Framework, Ebro Productions, Code Hunters" />
	<meta name="description" content="Description" />
	<!-- end of SEO tags -->

	<!-- Facebook tags -->
	<meta property="og:title" content="<? echo TITLE; ?>" />
	<meta property="og:type" content="website" />
	<meta property="og:image" content="<? echo $this->assets->get('images', 'favicon'); ?>" />
	<meta property="og:url" content="http://www.ebro.me/Encode"/>
	<meta property="og:description" content="Description" />
	<!-- end of Facebook tags -->

	<!-- Twitter tags -->
	<meta name="twitter:card" content="summary">
	<meta name="twitter:url" content="http://www.ebro.me/Encode">
	<meta name="twitter:title" content="<? echo TITLE; ?>">
	<meta name="twitter:description" content="Description">
	<meta name="twitter:image" content="<? echo $this->assets->get('images', 'favicon'); ?>">
	<!-- end of Twitter tags -->

    <!-- Favicon -->
	<link rel="icon" href="<? echo $this->assets->get('images', 'favicon'); ?>">

	<!--Basic stylesheets-->
	<link href="<? echo $this->assets->get('css', 'uikit'); ?>" rel="stylesheet">
	<link href="<? echo $this->assets->get('css', 'ionicons'); ?>" rel="stylesheet">
	<link href="<? echo $this->assets->get('css', 'theme'); ?>" rel="stylesheet">

	<!--Basic javascripts-->
	<script type="text/javascript" src="<? echo $this->assets->get('js', 'jquery.min'); ?>"></script>
	<script type="text/javascript" src="<? echo $this->assets->get('js', 'uikit.min'); ?>"></script>
</head>
<body>
<div class="uk-container uk-container-center">
	<div class="uk-grid" id="header-grid">
		<div class="uk-width-1-1" id="breadcrumb-holder">
			<span class="uk-text-muted date"><? echo date("F j, Y"); ?></span>
			<ul class="uk-breadcrumb uk-hidden-small uk-text-muted">
			    <li><a href="<? echo BASE_URL; ?>">Home</a></li>
			    <? $i=1;foreach ($url as $name => $value) { if($name != "Home"){?>
			    <li>
			    <? if($i == count($url)){ ?>
			    <span><? echo ucfirst($name); ?></span>
			    <? }else{ ?>
			    <a href="<? echo $value; ?>"><? echo ucfirst($name); ?></a></li>
			    <? }$i++;}} ?>
			</ul>
		</div>
		<div class="uk-width-1-1" id="logo">
			<span><img src="<? echo $this->assets->get('images', 'EncodeLogo'); ?>" style="width:10%;height:auto" class="uk-animation-fade" /></span>
			<h2 id="baseline">Encode Framework</h2>
		</div>
	</div>
