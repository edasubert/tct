<?php
	
	function settings_template( $control, $hash ) 
	{
		?>
		
		<div class="settings-form">
			<form action="/" method="POST">
				<input type="hidden" name="settings" value="<?php echo $hash; ?>"/>
				<input type="text" name="name" placeholder="<?php echo $control->registry->getObject( "lang" )->get( $control->lang , "Your name" ); ?>"/>
				<textarea name="description" placeholder="<?php echo $control->registry->getObject( "lang" )->get( $control->lang , "Your description" ); ?>"></textarea>
				
				<div class="languages from">
					<span class="title"><?php echo $control->registry->getObject( "lang" )->get( $control->lang , "Languages you are able to translate FROM:" ); ?></span>
					
					<input type="checkbox" name="csFrom" id="csFrom" /><label for="csFrom"><?php echo $control->languageCode[strtoupper( "cs" )]; ?></label><br/>
					<input type="checkbox" name="enFrom" id="enFrom" /><label for="enFrom"><?php echo $control->languageCode[strtoupper( "en" )]; ?></label><br/>
					
				</div>
				
				<div class="languages to">
					<span class="title"><?php echo $control->registry->getObject( "lang" )->get( $control->lang , "Languages you are able to translate TO:" ); ?></span>
					<input type="checkbox" name="csTo" id="csTo" /><label for="csTo"><?php echo $control->languageCode[strtoupper( "cs" )]; ?></label><br/>
					<input type="checkbox" name="enTo" id="enTo" /><label for="enTo"><?php echo $control->languageCode[strtoupper( "en" )]; ?></label><br/>
				</div>
				
				<button type="submit" class="btn btn-large"><?php echo $control->registry->getObject( "lang" )->get( $control->lang , "SUBMIT" ); ?></button>
			</form>
		</div>
		
<?php } ?>
