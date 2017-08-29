<footer>
	<div class="footer inner verticalSpacing--large">
		<div class="footer__links">
			<div class="footer__linksSection">
				<h6>Summon</h6>
				<ul>
					<?php
						foreach ($parent_categories as $category) {
							printf (
								'<li><a href="%s">%s</a></li>',
								makeUrl('/category/'.nameToSlug($category['name'])),
								$category['name']
							);
						}

						foreach ($wildcard_services as $service) {
							printf (
								'<li><a href="%s">%s</a></li>',
								makeUrl('/order/'.nameToSlug($service['name'])),
								$service['name']
							);
						}
						 ?>

				</ul>
			</div>
			<div class="footer__linksSection">
				<h6>Resources</h6>
				<ul>
					<li><a href="<?=makeUrl('/privacy-policy/')?>">Privacy Policy</a></li>
					<li><a href="<?=makeUrl('/terms-of-use/')?>">Terms of Use</a></li>
					<!--li><a href="/faq">FAQ</a></li-->
					<!--li><a href="/trust-and-safety">Trust &amp; Safety</a></li-->
					<li><a href="<?=makeUrl('/become-a-porter/')?>">Become A Porter</a></li>
				</ul>
			</div>
			<div class="footer__linksSection">
				<h6>Connect</h6>
				<ul>
					<li><a href="mailto:info@summonporter.ca">info@summonporter.ca</a></li>
					<li><a href="tel:+18443410041">1-844-341-0041</a></li>
					<li><a href="https://www.facebook.com/summonporter/">Facebook</a></li>
					<li><a href="https://www.instagram.com/summonporter/">Instagram</a></li>
					<li><a href="https://twitter.com/summonporter">Twitter</a></li>
				</ul>
			</div>
		</div>
		<div class="footer__logo">
			<i class="icon-logo"></i>
		</div>
	</div>
</footer>


<mobile-nav>
    <a class="close"><img src="<?=makeUrl('/images/x.png')?>" alt="Close" title="Close" /></a>

    <ul>
        <li><a href="how-it-works">How it Works</a></li>
        <li><a href="become-a-porter">Become a Porter</a></li>
        <li><a href="category">Summon Porter</a></li>
    </ul>
</mobile-nav>

<script src="<?=makeUrl('js/vendor.js?nc='.time())?>"></script>
<script src="<?=makeUrl('js/script.js?nc='.time())?>"></script>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');


  ga('create', 'UA-73145041-2', 'auto');
  ga('send', 'pageview');

  ga('require', 'ecommerce');
</script>
</body>
</html>
