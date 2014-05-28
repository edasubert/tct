<?php
	
	function source_template( $source, $control ) //ID, IDOut, text, langSource, langTarget, author, timestamp, hash
	{
		?>
		<div class="source">
			<a href="/phrase/<?php echo $control->urlClean( $source["text"] ); ?>/<?php echo $source["hash"]; ?>/">
			<span class="phrase">
				<?php echo $source["text"]; ?>
			</span>
			</a>
			
			<div class="properties">
				<span class="lang">
					<?php echo $control->registry->getObject( "lang" )->get( $control->lang , "From language:" ); ?>
					<em><?php echo $control->languageCode[strtoupper( $source["langSource"] )]; ?></em>
				</span>
				
				<span class="lang">
					<?php echo $control->registry->getObject( "lang" )->get( $control->lang , "To language:" ); ?>
					<em><?php echo $control->languageCode[strtoupper( $source["langTarget"] )]; ?></em>
				</span>
				
				<span class="time">
					<?php echo $control->registry->getObject( "lang" )->get( $control->lang , "Added:" ); ?>
					<em><?php echo $source["timestamp"]; ?></em>
				</span>
				
				<span class="author">
					<?php echo $control->registry->getObject( "lang" )->get( $control->lang , "By:" ); ?>
					<a href="http://twitter.com/<?php echo $source["author"]; ?>" target="_black"><em>@<?php echo $source["author"]; ?></em></a>
				</span>
				
				<a href="?flagS=<?php echo $source["ID"]; ?>" class="flag"><i class="icon-flag-filled"></i><?php echo $control->registry->getObject( "lang" )->get( $control->lang , "Flag" ); ?></a>
			</div>
		</div>
		<?php
	}

?>
