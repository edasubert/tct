<?php
	
	function tran_template( $tran, $control ) //ID, IDSource, text, author, timestamp, score, deleted, flag
	{
		?>
		<div class="tran">
			<span class="phrase">
				<?php echo $tran["text"]; ?>
			</span>
			
			<div class="properties">
				<span class="time">
					<?php echo $control->registry->getObject( "lang" )->get( $control->lang , "Added:" ); ?>
					<em><?php echo $tran["timestamp"]; ?></em>
				</span>
				
				<span class="author">
					<?php echo $control->registry->getObject( "lang" )->get( $control->lang , "By:" ); ?>
					<a href="/translator/profile/<?php echo $tran["IDAuthor"]; ?>"><em><?php echo $tran["author"]; ?></em></a>
				</span>
				
				<span class="score">
					<?php echo $control->registry->getObject( "lang" )->get( $control->lang , "Score" ).": "; ?>
					<em><?php echo $tran["score"]; ?></em>
				</span>
				
				<a href="?flagT=<?php echo $tran["ID"]; ?>" class="flag"><i class="icon-flag-filled"></i><?php echo $control->registry->getObject( "lang" )->get( $control->lang , "Flag" ); ?></a>
				
			</div>
			
		</div>
		<?php
	}

?>
