<?php
	
	function score_template( $source, $tran1, $tran2, $control ) //ID, IDSource, text, author, timestamp, score, deleted, flag
	{
		?>
		<div class="score">
			
			<div class="source">
				<span class="title">
					<?php echo $control->registry->getObject( "lang" )->get( $control->lang , "Phrase to translate:" ); ?>
				</span>
				
				<span class="phrase">
					<?php echo $source["text"]; ?>
				</span>
				<div class="properties">
					<span class="lang">
						<?php echo $control->registry->getObject( "lang" )->get( $control->lang , "From language:" ); ?>
						<em><?php echo $control->languageCode[strtoupper( $source["langSource"] )]; ?></em>
					</span>
					
					<span class="lang">
						<?php echo $control->registry->getObject( "lang" )->get( $control->lang , "To language:" ); ?>
						<em><?php echo $control->languageCode[strtoupper( $source["langTarget"] )]; ?></em>
					</span>
				</div>
				
			</div>
			<span class="title">
				<?php echo $control->registry->getObject( "lang" )->get( $control->lang , "Please select better translation:" ); ?>
			</span>
			
			<a href="?win=<?php echo $tran1["ID"]; ?>&lost=<?php echo $tran2["ID"]; ?>">
			<div class="tran">
				<span class="phrase">
					<?php echo $tran1["text"]; ?>
				</span>
			</div>
			</a>
			
			<a href="?win=<?php echo $tran2["ID"]; ?>&lost=<?php echo $tran1["ID"]; ?>">
			<div class="tran">
				<span class="phrase">
					<?php echo $tran2["text"]; ?>
				</span>
			</div>
			</a>
			
		</div>
		<?php
	}

?>
