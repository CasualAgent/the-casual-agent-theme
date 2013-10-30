<!DOCTYPE HTML>
<?php
global $more;
$more = 1;
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-status-bar-style" content="black" />
		<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<title><?php bloginfo( 'name' ); ?></title>
		<?
			/*<script type="text/javascript" src="<?=get_template_directory_uri()?>/js/jquery-2.0.3.min.map"></script>
				<script type="text/javascript" src="<?=get_template_directory_uri()?>/js/jquery.min.js"></script>
		
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
		<script type="text/javascript" src="<?=get_template_directory_uri()?>/js/jquery-ui.js"></script>
		<script type="text/javascript" src="<?=get_template_directory_uri()?>/js/masonry.min.js"></script> 		<script type="text/javascript" src="<?=get_template_directory_uri()?>/js/imagesLoaded.min.js"></script>
		<script type="text/javascript" src="<?=get_template_directory_uri()?>/js/page.js"></script>*/ 
		?>
		
		<?php wp_head(); ?>
	</head>
	<body>
		<?php get_template_part('menu'); ?>
		