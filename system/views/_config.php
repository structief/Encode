<?php
	//The set-your-environment-variables-on-first-load-page
	if($form){
?>
<div class="uk-grid">
	<div class="uk-width-1-1">
		<h2>Welcome!</h2>
		<p>You're about to dive into the world of <code><?php echo FW_TITLE; ?></code>.</p>
		<p>But first, we want you to fill in these settings, you need them to work properly. You can always change them later on in <code>application/config/all_config.inc.php</code>.</p>
	</div>
	<div class="uk-width-1-1">
		<form class="uk-form uk-form-stacked" method="post">
			<fieldset style="margin-top:20px">
			    <legend>General settings</legend>
			    <div class="uk-grid">
			    	<div class="uk-width-1-3">
					    <label for="productTitle" class="uk-form-label">Product Title</label>
						<input class="<? if(isset($errors->productTitle)){echo 'uk-form-danger';}else{echo '';} ?> uk-form-controls" id="productTitle" name="productTitle" type="text" placeholder="Product Title" value="<? echo $post->productTitle; ?>">
					</div>
					<div class="uk-width-1-3">
					    <label for="productVersion" class="uk-form-label">Product Version  <span class="uk-text-small">(optional)</span></label>
						<input class="<? if(isset($errors->productVersion)){echo 'uk-form-danger';}else{echo '';} ?> uk-form-controls" id="productVersion" name="productVersion" type="text" placeholder="Product Version" value="<? echo $post->productVersion; ?>">
					</div>
					<div class="uk-width-1-3">
					    <label for="baseUrl" class="uk-form-label">Base URL</label>
						<input class="<? if(isset($errors->baseUrl)){echo 'uk-form-danger';}else{echo '';} ?> uk-form-controls" id="baseUrl" name="baseUrl" type="text" placeholder="'/Encode/'" value="<? echo $post->baseUrl; ?>">
					</div>
				</div>
			</fieldset>
			<fieldset style="margin-top:20px">
				<legend>Development Settings</legend>
				<div class="uk-grid">
					<div class="uk-width-1-3">
						<label for="productStage" class="uk-form-label">Product Stage</label>
						<select id="productStage" name="productStage" class="uk-form-controls">
							<option value="dev">Development</option>
						  	<option value="test">Testing</option>
						  	<option value="deploy">Deployment</option>
						  </select>
					</div>
				</div>
			</fieldset>
			<fieldset style="margin-top:20px">
				<legend>Mail Settings</legend>
				<div class="uk-form-row">
					<label for="mandrillKey" class="uk-form-label">Mandrill Key</label>
					<input class="uk-form-controls" id="mandrillKey" name="mandrillKey" type="text" placeholder="Mandrill API Key" value="<? echo $post->mandrillKey; ?>"><br/><br/>
					<span class="uk-form-help-block"><span class="uk-badge">NOTE</span><span class="uk-text-small"><?php echo FRAMEWORK; ?> works with <a href="http://www.mandrillapp.com">Mandrillapp.com</a>. If you want this to work, generate an API key on their website, and fill it in here.</span></span>
				</div>
			</fieldset>
			<fieldset style="margin-top:20px">
				<legend>Database Settings</legend>
				<div class="uk-grid">
					<div class="uk-width-1-1">
						<span class="uk-form-help-block"><span class="uk-badge">NOTE</span><span class="uk-text-small">These are your deployment settings. The test settings are available to change in <code>application/config/db_config.inc.php</code></span></span>
					</div>
					<div class="uk-width-1-3" style="margin-top:10px">
						<label for="dbHost" class="uk-form-label">Host</label>
						<input class="uk-form-controls <? if(isset($errors->dbHost)){echo 'uk-form-danger';}else{echo '';} ?>" id="dbHost" name="dbHost" type="text" placeholder="host.com.mysql" value="<? echo $post->dbHost; ?>">
					</div>
					<div class="uk-width-1-3" style="margin-top:10px">
						<label for="dbUser" class="uk-form-label">Username</label>
						<input class="uk-form-controls <? if(isset($errors->dbUser)){echo 'uk-form-danger';}else{echo '';} ?>" id="dbUser" name="dbUser" type="text" placeholder="mysql username" value="<? echo $post->dbUser; ?>">
					</div>
					<div class="uk-width-1-3" style="margin-top:10px">
						<label for="dbPswd" class="uk-form-label">Password</label>
						<input class="uk-form-controls <? if(isset($errors->dbPswd)){echo 'uk-form-danger';}else{echo '';} ?>" id="dbPswd" name="dbPswd" type="password" placeholder="mysql password" value="<? echo $post->dbPswd; ?>">
					</div>
					<div class="uk-width-1-3" style="margin-top:10px">
						<label for="dbName" class="uk-form-label">Database Name</label>
						<input class="uk-form-controls <? if(isset($errors->dbName)){echo 'uk-form-danger';}else{echo '';} ?>" id="dbName" name="dbName" type="text" placeholder="database name" value="<? echo $post->dbName; ?>">
					</div>
					<div class="uk-width-1-3" style="margin-top:10px">
						<label for="dbPrefix" class="uk-form-label">Database Prefix  <span class="uk-text-small">(optional)</span></label>
						<input class="uk-form-controls <? if(isset($errors->dbPrefix)){echo 'uk-form-danger';}else{echo '';} ?>" id="dbPrefix" name="dbPrefix" type="text" placeholder="db_" value="<? echo $post->dbPrefix; ?>">
					</div>
				</div>
			</fieldset>
			<hr>
		    <button type="submit" class="uk-button uk-button-primary btn btn-primary">Save settings</button>
			<br/><br/><br/>
		</form>
	</div>
</div>
<? }else{ ?>
<div class="uk-grid">
	<div class="uk-width-1-1">
		<h2>Great!<small> all your settings were saved succesfully!</h2>
		<p>
			The best thing you can do now is going to your current <a href="/Index">landing page</a>, which is controlled here: <code>application/controllers/<? echo LANDING_CONTROLLER; ?></code>.<br/><br/>
			You can always change this controller in your settings (<code>application/config/all_config.inc.php</code>)
		</p><br/><hr>
	</div>
</div>
<? } ?>
