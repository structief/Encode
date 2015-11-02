<?php
	$c = new Controller();
	$colors = array("white"=>"#FFFFFF", "very_light_gray"=>"#fbfbfb", "light_gray"=>"#EBEBEB", "dark_gray"=>"#52616D", "black"=>"#2C343B", "green"=>"#73C430");
?>
@font-face{
	font-family: HumanPJ;
	src: url(<? echo $c->assets->get('fonts', 'HumanPJ-Average'); ?>);
}
@font-face{
	font-family: Laser;
	src: url(<? echo $c->assets->get('fonts', 'Laser'); ?>);
}
@font-face{
	font-family: Romance;
	src: url(<? echo $c->assets->get('fonts', 'Mens Romance'); ?>);
}
body{
	background-image: url(<? echo $c->assets->get("images", "bg_pattern"); ?>);
	background-position: top center;
	color: <? echo $colors['black']; ?>;
}
h1, h2, h3, h4, h5{
	font-family: Laser;
	color: <? echo $colors['black']; ?>;
	font-weight: lighter;
}
a i{
	margin-right: 5px;
}
a{
	color: <? echo $colors['green']; ?>;
}
.margin-left{
	padding-left: 0px !important;
	margin-left: 0px !important;
	padding: 10px;
	margin-bottom: 20px !important;
}
#header-grid, #newsflash-grid, #body-grid{
	background: <? echo $colors['white']; ?>;
	margin: 20px auto 25px;
	-moz-box-shadow: inset 0 0 2px #FFF,0 0 5px #cacaca;
	-webkit-box-shadow: inset 0 0 2px white,0 0 5px #CACACA;
	box-shadow: inset 0 0 2px white,0 0 5px #CACACA;
}
#body-grid{
	padding-right: 25px;
}
.border-bottom-grid{
	border-bottom: 3px solid <? echo $colors['green']; ?>;
	border-top: 1px solid #ececec;
	background: <? echo $colors['white']; ?>;
	-moz-box-shadow: inset 0 0 2px #FFF,0 0 5px #cacaca;
	-webkit-box-shadow: inset 0 0 2px white,0 0 5px #CACACA;
	box-shadow: inset 0 0 2px white,0 0 5px #CACACA;
}
#breadcrumb-holder{
	background: <? echo $colors['very_light_gray']; ?>;
	border-top: 3px solid <? echo $colors['green']; ?>;
	border-bottom: 1px solid #ececec;
	height: 30px;
	padding-top: 2px;
}
#breadcrumb-holder .date{
	background-color: #EBEBEB;
	padding: 0px 5px 0px 5px;
	font-size:8pt;
	float: left;
}

#breadcrumb-holder .uk-breadcrumb{
	padding: 0px 5px 0px 5px;
	font-size:8pt;
	float: left;
	margin: 0px 0px 0px 10px;
}
#menu{
	background: <? echo $colors['very_light_gray']; ?>;
	border-bottom: 3px solid <? echo $colors['green']; ?>;
	border-top: 1px solid #ececec;
	height: 30px;
	padding-top: 2px;
}
#menu .uk-navbar-nav .uk-active a{
	background-color: <? echo $colors['black']; ?>;
	color: <? echo $colors['very_light_gray']; ?>;
}
#menu .uk-offcanvas-bar a{
	color: <? echo $colors['black']; ?>;
	background-color: <? echo $colors['white']; ?>;
}
#body-grid .title-bar h2{
	text-transform: uppercase;
}
#logo img{
	width:10%;
	height:auto;
	min-width:50px;
}
#logo #baseline{
	margin-top: -10px;
}
.uk-badge{
	padding: 4px;
	margin-right: 10px;
}
@media(min-width: 1220px){
	.uk-badge{
		margin-left: -35px;
	}
}
@media(max-width: 1219px){
	.uk-badge{
		margin-left: -25px;
	}
}
.uk-navbar-toggle{
	margin-top: -7px;
	margin-left: -35px;
}

.off-canvas-bar{
	background-color: <? echo $colors['white']; ?>;
}
.social{
	padding: 10px !important;
	border: 1px solid <? echo $colors['light_gray']; ?>;
}
.ion-social{
	font-size: 30pt;
	color: <? echo $colors['light_gray']; ?>;
	background-color: <? echo $colors['dark_gray']; ?>;
	opacity: 0.6;
	border-radius: 30px;
	padding: 10px;
	margin: 5px 5px 0px 5px;
	cursor: pointer;
}
.ion-social-small{
	color: <? echo $colors['light_gray']; ?>;
	background-color: <? echo $colors['dark_gray']; ?>;
	opacity: 0.6;
	border-radius: 30px;
	padding: 3px;
}
.ion-text{
	margin-top: 0px;
	color: <? echo $colors['black']; ?>;
	font-family: Laser;
}
.social:hover{
	cursor: pointer;
}
.social:hover .ion-social{
	color: <? echo $colors['light_gray']; ?>;
	background-color: <? echo $colors['green']; ?>;
}
.social:hover a{
	text-decoration: none;
}
.item{
	padding: 20px;
}
.icon-holder{
	background-color: <? echo $colors['light_gray']; ?>;
	width:100%;
	height:200px;
	display:block;
	text-align: center;
	padding-top: 10%;
	overflow: hidden;
}
.icon{
	background-color: <? echo $colors['green']; ?>;
	color: <? echo $colors['light_gray']; ?>;
	padding: 20px;
	width: 67px;
	margin-left: 23%;
	display: block;
	font-size: 50pt;
	border-radius: 100px;
}
.icon-text{
	margin-top: 20px;
	display: block;
	padding: 0px 5px 0px 5px;
}
.icon-text span{
	font-family: Laser;
	font-size: 10pt;
	margin-top: -20px;
	display: block;
}



.title-bar{
	overflow-x: hidden;
	display: block;
	white-space: nowrap;
	margin-top: 20px !important;
}
.title-bar h2{
	float: left;
	display: inline-block;
	margin-top: 0px;
	background-color: <? echo $colors['white']; ?>;
}
.title-bar-line-pre{
	width: 20px;
	height: 12px;
	margin-top:7px;
	margin-right: 5px;
	background-image: url(<? echo $c->assets->get("images", "title-bar-stripe"); ?>);
	display: inline-block;
	float: left;
}
.title-bar-line{
	width: 100%;
	height: 12px;
	margin-top:7px;
	margin-left:5px;
	background-image: url(<? echo $c->assets->get("images", "title-bar-stripe"); ?>);
	display: inline-block;
}
#sidebar div{
	padding-left: 0px;
	display: block;
}
pre{
	overflow: auto;
	white-space: pre;
	background-color: <? echo $colors['very_light_gray']; ?>;
	border: 1px solid <? echo $colors['light_gray']; ?>;
}
code .alone{
	color: <? echo $colors['dark_gray']; ?>;
	border-left: 3px solid <? echo $colors['green']; ?>;
	padding-left: 5px;
}
em{
	color: <? echo $colors['dark_gray']; ?>;
}

/* FAQ */
.uk-comment-header {
	margin-bottom: 15px;
	padding: 10px;
	border: 1px solid <? echo $colors['light_gray']; ?>;
	border-radius: 4px;
	background: <? echo $colors['very_light_gray']; ?>;
	cursor: pointer;
}
.uk-comment-avatar{
	width:50px;
	height: 50px;
}
.uk-text-info{
	padding: 10px 0px 10px 10px;
}
.no-hover:hover{
	text-decoration: none;
}