</div>
<div style="width:100%;position:fixed;bottom:0px;left:0px;height:20px;background-color:rgba(200,200,200,0.4);color:black;padding-left:20px">
<a href="#errorHandler" class="encodeLogo" title="Errors, warnings and information"><img src="/Scribe/system/assets/images/encodeLogo.png" style="width:20px;height:20px;margin-left:-15px;margin-bottom:-4px;cursor:pointer" /></a>
<?php foreach($url as $name => $uri){ ?>
 > <a href="<?= $uri ?>" style="color:gray;text-decoration:none"><?= ucfirst($name) ?></a>
<? } ?>
<? Error::display_errors(); ?>
</div>
<?php
	//Footer
	echo '<!--' . session_id() . '-->';
?>
<!--Google Analytics-->
<script type="text/javascript">
/*
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-44097056-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
*/
</script>
</body>
</html>
