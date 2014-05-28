<?php
	
	function register_template( $control ) 
	{
		?>
		
		<div class="register-form">
			<form action="/translator/0" method="POST">
				<input type="email" name="email" placeholder="Your Email"/>
				<button type="submit" class="btn btn-large"><?php echo $control->registry->getObject( "lang" )->get( $control->lang , "REGISTER" ); ?></button>
			</form>
		</div>
		
<?php } ?>
