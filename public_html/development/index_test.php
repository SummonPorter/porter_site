<?php
$pageClass = 'home';
include 'snippets/header.php';

?>

<!-- HERO  -->
<section class="home__hero">
	<div class="home__heroIntro">
		<div class="inner">
			<div class="home__heroIntroInner">
				<h1>We take care of your errands and chores</h1>
				<p>So you can be where you want to be</p>
				<form class="inputGroup inputGroup--search">
					<input type="text" name="searchText"
						placeholder="What do you need help with?" id="tags" autocomplete="off">
					<div class="submit" id="searchSubmit"><i class="icon-search"></i></div>
				</form>
			</div>
		</div>
	</div>
	<a href="#tasks" class="home__heroChevron"><i class="icon-circle-chevron"></i></a>
</section>

<?php include 'snippets/banner.php'; ?>

<!-- TASKS -->
<section class="home__tasks verticalSpacing" id="tasks">
	<div class="inner">
		<h2 class="heading--center">Choose a category below</h2>
		<div class="home__taskItems">
			<?php

			$tpl = '
			<div class="home__taskItem" >
				<div style="background-image:url(%s)">
					<a href="%s" class="home__taskItemInner">
						<div class="home__taskItemCopy">
							<h4>%s</h4>
						</div>
					</a>
				</div>
			</div>';

				if ($category_display_type == 'show_parent') {
					foreach ($parent_categories as $category) {
						printf (
							$tpl,
							$category['webImage'],
							'/category/'.nameToSlug($category['name']),
							$category['name']
						);
					}
				}

				foreach ($wildcard_services as $service) {
					printf (
						$tpl,
						$service['webIcon'],
						'/order/'.nameToSlug($service['name']),
						$service['name']
					);
				}
			?>
		</div>
	</div>
</section>


<!-- VALUE PROPS -->
<?php include 'snippets/value-props.php' ?>

<!-- TESTIMONIALS -->
<section class="testimonials verticalSpacing">
	<div class="inner">
		<h2 class="heading--portgore heading--center">What our customers say:</h2>
		<div class="testimonialCarousel">
			<div class="testimonialCell">
				<div class="testimonialContent">
					<h4 class="heading--portgore">Virtually do everything</h4>
					<p>They virtually do everything which is absolutely incredible. One app for everything that I need taken care of.</p>
					<span>Mike</span>
				</div>
				<div class="testimonialDate">
					<h4 class="heading--portgore">May 21st</h4>
				</div>
			</div>
			<div class="testimonialCell">
				<div class="testimonialContent">
					<h4 class="heading--portgore">Pleasure to work with</h4>
					<p>My company has used Porter so many times and they've always been an absolute pleasure to work with.</p>
					<span>Robert</span>
				</div>
				<div class="testimonialDate">
					<h4 class="heading--portgore">June 3rd</h4>
				</div>
			</div>
			<div class="testimonialCell">
				<div class="testimonialContent">
					<h4 class="heading--portgore">There when I need them</h4>
					<p>They're always there when I need them, taking care of my errands so I can focus on the things that make me happy.</p>
					<span>Eva</span>
				</div>
				<div class="testimonialDate">
					<h4 class="heading--portgore">June 16th</h4>
				</div>
			</div>
			<div class="testimonialCell">
				<div class="testimonialContent">
					<h4 class="heading--portgore">Like having 5 clones</h4>
					<p>It's like having 5 clones of myself running around the city taking care of my errands while I focus on my business.</p>
					<span>Tony</span>
				</div>
				<div class="testimonialDate">
					<h4 class="heading--portgore">July 13th</h4>
				</div>
			</div>
			<div class="testimonialCell">
				<div class="testimonialContent">
					<h4 class="heading--portgore">They're so fast</h4>
					<p>They're so fast that it felt like I was in a time-warp. Will use them over and over and over again.</p>
					<span>Laura</span>
				</div>
				<div class="testimonialDate">
					<h4 class="heading--portgore">August 22nd</h4>
				</div>
			</div>
		</div>
	</div>
</section>

<script type="text/javascript">
$(function(){
	var emailSubmitting = false;
	$('#emailSubmit').click(function(){
		$('#email-form').submit();
	});

    $('#email-form').submit(function(e){
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
		if (emailSubmitting) return false;
		emailSubmitting = true;
		cssConsole({});
        $.ajax({
            url: '<?=$url_prefixes?>/internal/services/subscriber/new',
            dataType: 'json',
            method: 'POST',
            data: {
                email: $('#email-form input[name="email"]').val()
            }
        }).done(function(data) {
			if (!emailSubmitting) return;
            if (data['inputDeclaredValid'] == true) {
                $('.home__subInner > *').fadeOut(500);
				setTimeout(function(){
					$('<h2 class=" heading--white heading--center" style="display:none;">You were subscribed!</h2>')
						.fadeIn()
						.appendTo($('.home__subInner')[0]);
				}, 500)
            } else {
	            cssConsole({
	                '.home__subInner:after': {
	                    background: 'rgba(181, 151, 76, 1)',
	                    content: '"'+data.returnMessages.join("\n")+'"',
	                    display: 'block',
	                    padding: '1rem',
	                    marginTop: '1rem',
	                    color:'#1d1d51',
	                    clear: "both",
	                    animationName: 'shake-horizontal',
	                    animationDuration: '.8s',
	                    animationTimingFunction: 'ease-in-out'
	                }
	            });
			}

        }).fail(function(xhr, status, error){
            console.log("Status: " + status + " Error: " + error);
            console.log(xhr);
        }).always(function(){
			emailSubmitting = false;
		});
		return false;
    });
});

</script>
<style id="cssConsole"></style>


<!-- SUBSCRIBE -->
<section class="home__sub verticalSpacing">
	<div class="home__subOuter">
		<div class="home__subInner">
			<h2 class="heading--underlined heading--white heading--center">Your time is precious</h2>
			<p>Subscribe to keep up-to-date with all things Porter</p>
			<form class="inputGroup inputGroup--search" id="email-form" onsubmit="return false;">
				<input type="text" name="email" placeholder="Email Address" autocomplete="off">
				<div class="submit" id="emailSubmit"><i class="icon-check"></i></div>
			</form>
		</div>
	</div>
</section>


<!-- BECOME A PORTER -->
<?php include 'snippets/become-a-porter.php' ?>

<?php include 'snippets/autocomplete.php'; ?>

<?php include 'snippets/footer.php'; ?>
