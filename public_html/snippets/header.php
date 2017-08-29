<?php

if (!isset($pageClass)) $pageClass = '';

 ?><!DOCTYPE html>
<html lang="en">
<head>

<?php switch ($pageClass) {
    case 'home':
        ?>
        <title>Porter | Life Just Got Easier</title>
        <meta name="description" content="A personal concierge platform that helps busy consumers take care of their errands and chores. It's time you stop doing and start living.">
        <meta content='PocketPorter Technologies, Inc.' name='author'>
        <meta content='Porter, summon, domestic cleaning, commercial cleaning, local cleaning, errands, chores, local handyman, trustworthy, vetted, qualified, local delivery, ikea assembly, junk removal, grocery shopping, dry cleaning' name='keywords'>

        <?php
        break;
    case 'apply':
        ?>
        <title>Porter | Life Just Got Easier</title>
        <meta name="description" content="A personal concierge platform that helps busy consumers take care of their errands and chores. It’s time you stop doing and start living.">
        <meta content='PocketPorter Technologies, Inc.' name='author'>
        <meta content='Porter, summon, domestic cleaning, commercial cleaning, local cleaning, errands, chores, local handyman, trustworthy, vetted, qualified, local delivery, ikea assembly, junk removal, grocery shopping, dry cleaning' name='keywords'>

        <?php
        break;
    case 'how':
        ?>
        <title>Porter | Life Just Got Easier</title>
        <meta name="description" content="A personal concierge platform that helps busy consumers take care of their errands and chores. It’s time you stop doing and start living.">
        <meta content='PocketPorter Technologies, Inc.' name='author'>
        <meta content='Porter, summon, domestic cleaning, commercial cleaning, local cleaning, errands, chores, local handyman, trustworthy, vetted, qualified, local delivery, ikea assembly, junk removal, grocery shopping, dry cleaning' name='keywords'>

        <?php
        break;
    default:
        ?>
        <title>Porter<?=isset($title)?' | '.$title:''?> | Life Just Got Easier</title>
        <meta name="description" content="<?=isset($meta_description)?$meta_description:''?>">
        <meta content='PocketPorter Technologies, Inc.' name='author'>
        <meta content='Porter, summon, domestic cleaning, commercial cleaning, local cleaning, errands, chores, local handyman, trustworthy, vetted, qualified, local delivery, ikea assembly, junk removal, grocery shopping, dry cleaning' name='keywords'>

        <?php
        break;
}?>

	<base href="/<?=DEVELOPMENT?'development/':''?>">
	<meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' />
	<link rel="stylesheet" type="text/css" href="<?=makeUrl('css/vendor.css?'.time())?>">
	<link rel="stylesheet" type="text/css" href="<?=makeUrl('css/style.css?'.time())?>">
    <link rel="icon" type="image/png" href="<?=makeUrl('/images/porter-logo.png')?>" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.0/jquery-ui.min.js"></script>

    <script src="//load.sumome.com/" data-sumo-site-id="ddab481409d0f0e63a8e9f3317a05fb575ce46f1c75bfabcaa695b0644b07ab2" async="async"></script>

    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
    s1.async=true;
    s1.src='https://embed.tawk.to/56f823451c9c5b5e6f787940/default';
    s1.charset='UTF-8';
    s1.setAttribute('crossorigin','*');
    s0.parentNode.insertBefore(s1,s0);
    })();
    </script>
    <!--End of Tawk.to Script-->
</head>
<body class="<?php echo $pageClass; ?> animated fadeIn">

	<header>
		<div class="header inner">
			<a class="siteLogo" href="<?=makeUrl('')?>"><i class="icon-logotype"></i></a>
            <div class="mobile-nav active" style="height: 36px; line-height: 36px; width: 40px;">
				<i class="fa fa-bars"></i>
			</div>
			<nav class="desktopNav" id="nav">
				<ul>
					<li><a href="<?=makeUrl('how-it-works/')?>">How it Works</a></li>
					<li><a href="<?=makeUrl('become-a-porter/')?>">Become a Porter</a></li>
					<li class="last"><a class="btn btn--small" href="<?=makeUrl('category/')?>">Summon Porter</a></li>
				</ul>
			</nav>
		</div>
	</header>
