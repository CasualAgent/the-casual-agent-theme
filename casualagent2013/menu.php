<?php
	/*global $CA_MENU_CATEGORIES;
	//update_menu_cfg();
	init_menu_categories();*/
	
/*	$categories = $CA_MENU_CATEGORIES;*/

	$TH = ThemeInstance();
	$categories = $TH->getMenu();
	
		
	$menuItems = array();	
	$menuItems[]='<li class="menu-item '.((is_home()||is_front_page())?"selected":"").'"><a class="menu-item-link " href="'.(home_url()).'" data-child="all">All</a></li>';
	
	foreach($categories as $cat ){
		$selected = ($cat['isCurrent'])?"selected":"";
		$hasSubMenu = (count($cat['children'])>0)?'has-sub-menu':'';
		
		$subHtml = "";
		if(count($cat['children'])>0 && $cat['cat_ID']>0){
			$subHtml = array("<ul class=\"sub-menu\" data-cat-id='".$cat['cat_ID']."'>");
			
			foreach($cat['children'] as $idx => $sc){
				$subHtml[] = '<li class="menu-item"><a class="menu-item-link " href="'.$sc['href'].'" data-child="'.$sc['slug'].'">'.$sc['name'].'</a></li>';
			}
			$subHtml[] = "</ul>";
			$subHtml = implode("\n", $subHtml);
		}
		$menuItems[]='<li class="menu-item '.$selected.' '.$hasSubMenu.'"><a class="menu-item-link " href="'.$cat['href'].'" data-child="'.$cat['slug'].'">'.$cat['name'].'</a>'.$subHtml.'</li>';	
	}
	$menuItems[] = '<li class="menu-item '.((is_page('My Blogs'))?"selected":"").'"><a class="menu-item-link" href="'.(home_url()."/blogs").'" >'.Blogs.'</a></li>';
	$menuItems = implode("\n", $menuItems);
?>		
<section id="header" class="container-outter">
	<nav class="nav container-inner">
		<a href="<?=home_url()?>" class="logo"><span id='casual'>Casual</span><span id='agent'> agent</span></a>
		<a class="menu-bttn">Menu</a>
		<ul class="menu brick-wall">
			<?=$menuItems?>
			<li class="menu-item menu-fb">
				<a class="menu-item-link ir" href="https://www.facebook.com/pages/The-Casual-Agent/375563579210984?ref=profile" target="_blank" alt='Facebook'></a>
			</li>
			<li class="menu-item menu-twttr">
				<a class="menu-item-link ir" href="http://twitter.com/TheCasualAgent" target="_blank" alt='Twitter'></a>
			</li>
			<!--<li class="menu-item search">
				<form class="search-box" action="/search" method="get">
					<input type="search" name="q" value="" data-search=" " required="" />
					<button type="submit">Search</button>
					<div class='clearfix'></div>
				</form>
				</li>-->
		</ul>
		<div class='clearfix'></div>
	</nav>
	<div class='clearfix'></div>
</section>

