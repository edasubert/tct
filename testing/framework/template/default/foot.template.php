	<?php function foot_template( $control, $message, $templatePath )
	{?>

	</div>
	
	<div class="footer">
		<div class="container">
			
		<p class="pull-right"><a href="##myModal"> <i class="icon-mail"></i> CONTACT</a></p>
		</div>
	</div>
	
	
	<!-- Modal -->
	<?php 
	if ( !( $message === FALSE ) )
	{?>
	<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3 id="myModalLabel"><?php echo $message["title"]; ?></h3>
		</div>
		<div class="modal-body">
		<?php echo $message["body"]; ?>
		</div>
	</div>
	<?php } ?>
	
	<!-- Scripts -->
	<script src="<?php echo $templatePath; ?>/js/vertical-scroll.js"></script>
	<script src="<?php echo $templatePath; ?>/js/bootstrap.min.js"></script>
	<script>
		<?php 
		if ( !( $message === FALSE ) )
		{?>
				$('#myModal').modal('show')
		<?php } ?>
	</script>
	</body>
	</html>
	<?php } ?>
