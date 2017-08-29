<?php
$pageClass = 'how';
$pageName = 'how-it-works';
include incl('snippets/header');
?>


<section class="banner">
	<div class="inner">
		<h1 class="verticalCenter heading--underlined heading--white heading--center">How Porter Works</h1>
	</div>
</section>

<section class="steps verticalSpacing--large">
	<div class="inner">
		<div class="stepsContainer">
			<div class="step">
				<div class="stepNumber">
					<span>1</span>
				</div>
				<div class="stepIcon">
					<i class="icon-bell"></i>
				</div>
				<div class="stepCopy">
					<h5>Summon a Porter</h5>
					<p>Summon a vetted, experienced and reliable Porter through the proprietary summon platform.</p>
				</div>
			</div>
			<div class="step">
				<div class="stepNumber">
					<span>2</span>
				</div>
				<div class="stepIcon">
					<i class="icon-shaking-hands"></i>
				</div>
				<div class="stepCopy">
					<h5>Get Matched to a Porter</h5>
					<p>A Porter will  instantly receive your summon and proceed with completing your request.</p>
				</div>
			</div>
			<div class="step">
				<div class="stepNumber">
					<span>3</span>
				</div>
				<div class="stepIcon">
					<i class="icon-to-do-list-done"></i>
				</div>
				<div class="stepCopy">
					<h5>Rest Easy</h5>
					<p>By summoning Porter, you can relax and rest easy knowing we’ll take care of your request.</p>
				</div>
			</div>
		</div>
		<a href="<?=makeUrl('/category/')?>" class="btn btn--primary">Try it now</a>
	</div>
</section>


<section class="how__safety">
	<img class="how__safetyImgMobile" src="<?=makeUrl('/images/how-it-works-safety-and-security.jpg')?>">
	<div class="how__safetyCopy">
		<div class="how__safetyCopyInner">
			<h2 class="heading--underlined heading--left heading--white">Trust and Safety</h2>
			<p>Trust and safety are our top priority. Every Porter has been thoroughly vetted and undergoes extensive background checks. Every summon is insured up to $1 million. And our talented Customer Support team is available to help every hour of the day.</p>
		</div>
	</div>
	<div class="how__safetyImgDesktop"></div>
</section>

<section class="how__wherever verticalSpacing--large">
	<div class="inner">
		<div class="how__whereverImg">
			<img class="how__whereverImg--mobile" src="<?=makeUrl('/images/how-it-works-devices-mockup-mobile.png')?>">
			<img class="how__whereverImg--desktop" src="<?=makeUrl('/images/how-it-works-devices-mockup-desktop.png')?>">
		</div>
		<div class="how__whereverCopy">
			<h2 class="heading--underlined heading--portgore heading--left">Wherever you are</h2>
			<p>Porter is wherever you are. You can access the Porter platform at any time through the web or your mobile device. Do you prefer talking to a human? You can also reach us at our toll free number 1-844-341-0041.</p>
			<div class="how__whereverDownload">
				<a href="https://itunes.apple.com/ca/app/pocketporter/id1126234883" target="_blank"><img src="./images/app-store.png"></a>
				<!--a href="#" target="_blank"><img src="./images/google-play.png"></a-->
			</div>
		</div>
	</div>
</section>


<?php include incl('snippets/how-payment-works'); ?>
<?php include incl('snippets/value-props'); ?>
<?php include incl('snippets/become-a-porter'); ?>
<?php include incl('snippets/footer'); ?>
