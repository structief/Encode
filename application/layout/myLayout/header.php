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
	<!-- inject:css --> 
    <link rel="stylesheet" href="/application/assets/css/minified/Encode_stylesheets.css?v=5b27a8dd68d32e4c069fff3bdc0e5bae"> 
    <!-- endinject -->
</head>
<body>
<nav class="uk-navbar">
    <a href="" class="uk-navbar-brand">
    	<img src="<? echo $this->assets->get('images/icons', 'favicon'); ?>" style="height:auto;max-height: 80%;width:auto" class="uk-animation-fade" />
    </a>
    <ul class="uk-navbar-nav">
    	<li>
    		<a class="active" href="/Index">
    			<?= $title; ?>
    		</a>
    	</li>
    	<li>
    		<a href="http://www.ebro.me/Encode/Intro" target="_BLANK">
    			Getting started
    		</a>
    	</li>
    	<li>
    		<a href="http://www.ebro.me/Encode/Manual" target="_BLANK">
    			Documentation
    		</a>
    	</li>
    	<li>
    		<a href="http://www.ebro.me/Encode/FAQ" target="_BLANK">
    			FAQ
    		</a>
    	</li>
    	<li>
    		<a href="https://github.com/kliptonize/Encode" target="_BLANK">
    			Repository
    		</a>
    	</li>
    </ul>
    <div class="uk-navbar-flip">
        <i><?php echo date("F j, Y"); ?></i>
    </div>
</nav>
<div class="uk-container uk-container-center">