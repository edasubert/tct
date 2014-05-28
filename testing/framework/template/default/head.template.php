	<?php function head_template( $control, $title, $templatePath, $lang )
	{?>

<!DOCTYPE html>
	<html lang="<?php echo $lang; ?>">
	<head>
	<meta charset="utf-8">
	<title><?php echo $title; ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Flat Design Mini Portfolio">
	<meta name="keywords" content="responsive, bootstrap, flat design, flat ui, portfolio">
	<meta name="author" content="Dzyngiri">
	<meta name="description" content="This is a responsive flat design mini portfolio for creative folks who want to showcase their work online.">
	<!-- styles -->
	<link href="<?php echo $templatePath; ?>/css/bootstrap.css" rel="stylesheet">
	<link href="<?php echo $templatePath; ?>/css/bootstrap-responsive.css" rel="stylesheet">
	<link href="<?php echo $templatePath; ?>/css/style-single-page.css" rel="stylesheet">
	<link href="<?php echo $templatePath; ?>/font/css/fontello.css" rel="stylesheet">
	<link href='http://fonts.googleapis.com/css?family=Droid+Sans:400,700' rel='stylesheet' type='text/css'>
	<!-- Add jQuery library -->
	<script type="text/javascript" src="<?php echo $templatePath; ?>/js/jquery-1.10.1.min.js"></script>
	<!-- Add fancyBox main JS and CSS files -->
	<script type="text/javascript" src="<?php echo $templatePath; ?>/js/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="/text/css" href="<?php echo $templatePath; ?>/css/jquery.fancybox.css?v=2.1.5" media="screen" />
	<link href="<?php echo $templatePath; ?>/css/tct.css" rel="stylesheet">
	<script>
				$(document).ready(function() {
			$(".fancybox-thumb").fancybox({
				helpers	: {
					title	: {
						type: 'inside'
					},
					overlay : {
								css : {
									'background' : 'rgba(1,1,1,0.65)'
								}
							}
				}
			});
		});
			</script>
	</head>
	<body>
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
		<div class="container"> <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </a> <a class="brand" href="/phrases">
				<img src="<?php echo $templatePath; ?>/img/logoBeta.svg"/>
			</a>
			
			<ul class="nav nav-collapse pull-right">
			
			<li><a href="/phrases"><i class="icon-doc-text"></i> <?php echo $control->registry->getObject( "lang" )->get( $control->lang , "Phrases" ); ?></a></li>
			<li><a href="/score"><i class="icon-star-half-alt"></i> <?php echo $control->registry->getObject( "lang" )->get( $control->lang , "Score" ); ?></a></li>
			<li><a href="/translator/0"><i class="icon-user"></i> <?php echo $control->registry->getObject( "lang" )->get( $control->lang , "Translators" ); ?></a></li>
			<li><a href="/about"><i class="icon-help-circled"></i> <?php echo $control->registry->getObject( "lang" )->get( $control->lang , "About" ); ?></a></li>
			</ul>
			<!-- Everything you want hidden at 940px or less, place within here -->
			<div class="nav-collapse collapse">
			<!-- .nav, .navbar-search, .navbar-form, etc -->
			</div>
		</div>
		</div>
	</div>
	<div class="clearfix"></div>
		
	<div id="profile" class="container">
		
	<?php } ?>
