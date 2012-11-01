<?php echo View::make('site.inc.meta')->render()?>
  	<? if($page): ?>
		<title><?php echo $page->title." &lt; ".$page->meta_title?></title>
		<meta name="description" content="<?php echo $page->meta_description?>" />
		<meta name="keywords" content="<?php echo $page->meta_keywords?>" />
	<? else: ?>
		<title><?php echo COMPANY_NAME?></title>
		<meta name="description" content="<?php echo COMPANY_NAME?>" />
		<meta name="keywords" content="<?php echo COMPANY_NAME?>" />
	<? endif; ?>
</head>
<body>
	<!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
	
	<footer>
		<?php echo View::make('site.inc.footer')->render()?>
	</footer>
	<?php echo View::make('site.inc.scripts')->render()?>
</body>
</html>