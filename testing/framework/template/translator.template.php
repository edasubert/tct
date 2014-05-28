<?php
	
	function translator_template( $translator, $control ) 
	{
		?>
		<div class="translator">
			<span class="name"><?php echo $translator["name"]; ?></span>
			<span class="description"><?php echo $translator["description"]; ?></span>
			<div class="languages">
				<span class="from"><?php
				echo $control->registry->getObject( "lang" )->get( $control->lang , "From language:" )." "; 
				
				foreach ( $translator["from"] as $key => $lang )
				{
					$translator["from"][$key] = $control->languageCode[strtoupper( $lang )];
				}
				echo implode( ", ", $translator["from"] );
				
				?></span>
				<span class="to"><?php
				echo $control->registry->getObject( "lang" )->get( $control->lang , "To language:" )." "; 
				
				foreach ( $translator["to"] as $key => $lang )
				{
					$translator["to"][$key] = $control->languageCode[strtoupper( $lang )];
				}
				echo implode( ", ", $translator["to"] );
				
				?></span>
			</div>
		</div>
		
		<?php
	}

?>
