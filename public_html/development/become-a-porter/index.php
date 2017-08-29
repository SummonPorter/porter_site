<?php

if (empty($_SERVER['HTTPS'])) {
    header("Location: https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");
    exit;
}


$pageClass = 'apply';
$pageName = 'become-a-porter';
require_once incl('snippets/header');

?>

<section class="apply__hero banner">
	<div class="inner">
		<div class="apply__formWrapper">
			<h1>Become a Porter</h1>
			<div class="apply__form">
				<form mathod="post" id="becomePorter" onsubmit="return false">
					<div class="inputGroup inputGroup--form doubleInput">
						<input type="text" name="firstname" id="apply-firstname" placeholder="First Name">
						<input type="text" name="lastname" id="apply-lastname" placeholder="Last Name">
					</div>
					<div class="inputGroup inputGroup--form singleInput">
						<input type="email" name="email" id="apply-email" placeholder="Email Address">
					</div>
					<div class="inputGroup inputGroup--form singleInput">
						<input type="tel" name="tel" id="apply-tel" placeholder="Mobile Phone">
					</div>
					<div class="inputGroup inputGroup--form singleInput">
						<input type="text" name="city" id="apply-city" placeholder="City">
					</div>
					<h5>Which jobs do you want to do?</h5>
					<div class="apply__checkboxes">
						<div class="inputGroup inputGroup--checkbox">
							<input type="checkbox" name="jobs" id="apply-handyman" value="handyman">
							<label for="apply-handyman">Handyman</label>
						</div>
						<div class="inputGroup inputGroup--checkbox">
							<input type="checkbox" name="jobs" id="apply-driver" value="driver">
							<label for="apply-driver">Driver</label>
						</div>
						<div class="inputGroup inputGroup--checkbox">
							<input type="checkbox" name="jobs" id="apply-cleaning" value="cleaning">
							<label for="apply-cleaning">Cleaning</label>
						</div>
						<div class="inputGroup inputGroup--checkbox">
							<input type="checkbox" name="jobs" id="apply-moving" value="moving">
							<label for="apply-moving">Moving</label>
						</div>
						<div class="inputGroup inputGroup--checkbox">
							<input type="checkbox" name="jobs" id="apply-corporate" value="corporate">
							<label for="apply-corporate">Corporate</label>
						</div>
						<div class="inputGroup inputGroup--checkbox">
							<input type="checkbox" name="jobs" id="apply-shopping" value="shopping">
							<label for="apply-shopping">Shopping</label>
						</div>
					</div>
                    <div class="driver-fields" style="display:none;">
    					<div class="inputGroup inputGroup--form singleInput">
    						<input type="text" name="text" id="apply-vehicle-make" placeholder="Vehicle Make" />
    					</div>
    					<div class="inputGroup inputGroup--form singleInput">
    						<input type="text" name="text" id="apply-vehicle-model" placeholder="Vehicle Model" />
    					</div>
    					<div class="inputGroup inputGroup--form singleInput">
    						<input type="text" name="text" id="apply-vehicle-year" placeholder="Vehicle Year" />
    					</div>
                    </div>
					<input class="btn btn--primary btn--full btn--rect btn--large" type="submit" value="Submit Application">
				</form>
			</div>
		</div>
	</div>
</section>

<section class="steps verticalSpacing--large">
	<div class="inner">
		<div class="stepsContainer">
			<div class="step">
				<div class="stepIcon">
					<i class="icon-like"></i>
				</div>
				<div class="stepCopy">
					<h5>Jobs You Love</h5>
					<p>Choose from a variety of jobs and work the ones you enjoy.</p>
				</div>
			</div>
			<div class="step">
				<div class="stepIcon">
					<i class="icon-piggy-bank"></i>
				</div>
				<div class="stepCopy">
					<h5>Get Paid Well</h5>
					<p>Porters make between $15 to $30 an hour. You keep your tips.</p>
				</div>
			</div>
			<div class="step">
				<div class="stepIcon">
					<i class="icon-Calendar"></i>
				</div>
				<div class="stepCopy">
					<h5>Your Own Schedule</h5>
					<p>Be your own boss. Choose which days you want to work.</p>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="grey qualities verticalSpacing--large">
	<div class="inner">
        <div class="stepCopy">
            <p class="center">We believe it is our job to empower our Porters to continuously<br />
                improve their skills and feed their interests and motivatios.</p>
        </div>
		<div class="qualities">
			<div class="step">
				<div class="">
					<i class="line-icon profile"></i>
				</div>
				<div class="stepCopy">
					<h5 class="">Talent</h5>
					<p>We set high standards for our Porters.
                        We only hire the best in every city we operate in.</p>
				</div>
			</div>
			<div class="step">
				<div class="">
					<i class="line-icon gears"></i>
				</div>
				<div class="stepCopy">
					<h5 class="">Training</h5>
					<p>Each hire will be paired up with an experienced Porter and receive in-field training.</p>
				</div>
			</div>
			<div class="step">
				<div class="">
					<i class="line-icon shield"></i>
				</div>
				<div class="stepCopy">
					<h5 class="">Trust</h5>
					<p>All Porters undergo an extensive backgroun check to ensure that they meet Porter standards.</p>
				</div>
			</div>
		</div>

        <h1 class="center h2blue">Want to become a Porter today?<br /></h1>


        <div class="center">
            <br />
            <p>
                <a href="<?=makeUrl('/become-a-porter/#')?>" class="btn btn--primary">APPLY NOW</a>
            </p>
        </div>
	</div>
</section>

<!-- FAQs -->
<section class="apply__faqs verticalSpacing--large">
	<div class="inner">
		<h2 class="heading--underlined heading--center heading--portgore">Frequently Asked Questions</h2>
		<div class="accordionGroup">
			<div class="accordion">
				<input class="accordion__checkbox" type="checkbox" name="ac-1" id="ac-1">
				<label class="accordion__label" for="ac-1"><span>How does the application process work?</span></label>
				<div class="accordion__content">
					<p>After submitting your application, you can start earning cash in as little as a week. We will contact everyone within a few days of applying. Candidates will go through an initial phone-screen. Upon successfully passing the phone screen, candidates will go through the Porter vetting process (background check and reference follow up). Only a select few will be hand-picked to come in for an orientation and be paired with an experienced Porter for training.</p>
				</div>
			</div>
			<div class="accordion">
				<input class="accordion__checkbox" type="checkbox" name="ac-2" id="ac-2">
				<label class="accordion__label" for="ac-2"><span>How do I get paid?</span></label>
				<div class="accordion__content">
					<p>You're paid direct deposit every 2 weeks.</p>
				</div>
			</div>
			<div class="accordion">
				<input class="accordion__checkbox" type="checkbox" name="ac-3" id="ac-3">
				<label class="accordion__label" for="ac-3"><span>What will my work schedule be like?</span></label>
				<div class="accordion__content">
					<p>That's 100% up to you. You give us your hours that you can work for the week and we'll give you a schedule that will work with those hours.</p>
				</div>
			</div>
			<div class="accordion">
				<input class="accordion__checkbox" type="checkbox" name="ac-4" id="ac-4">
				<label class="accordion__label" for="ac-4"><span>Do I need insurance?</span></label>
				<div class="accordion__content">
					<p>If you are driving a car or a motorcycle/scooter, you will need a drivers license. Porters using their bicycles do not need a license to operate.</p>
				</div>
			</div>
			<div class="accordion">
				<input class="accordion__checkbox" type="checkbox" name="ac-5" id="ac-5">
				<label class="accordion__label" for="ac-5"><span>What else do I need to know?</span></label>
				<div class="accordion__content">
					<p>In order to be a Porter, you must take pride in providing the best service in the city. Only a select few will get the opportunity to join the Porter family. You must be at least 19 years old and have reliable transportation (Car, truck, bike, moped or motorcycle). Additionally, you'll need an iPhone or Android phone to use our mobile app. As part of our approval process, you must pass phone screen, vetting process and in-person training.</p>
				</div>
			</div>
		</div>
	</div>
</section>

<style id="cssConsole"></style>

<script type="text/javascript">
var autocomplete;
function initAutocomplete() {
    autocomplete = new google.maps.places.Autocomplete(
        document.getElementById('apply-city'),
        {types: []}
    );
    // autocomplete.addListener('place_changed', void);
}

function fillInAddress() {
    // Get the place details from the autocomplete object.
    var place = autocomplete.getPlace();
    console.log(place);
}

function geolocate() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var geolocation = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            var circle = new google.maps.Circle({
                center: geolocation,
                radius: position.coords.accuracy
            });
            autocomplete.setBounds(circle.getBounds());
            autocomplete2.setBounds(circle.getBounds());
        });
    }
}
$('body').on('change', '#apply-driver', function(){
    if ($(this).is(':checked')) {
        $('.driver-fields').fadeIn();
    } else {
        $('.driver-fields').fadeOut()
    }
});
$(function(){
    $('#becomePorter').on('submit', function(e){
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        $.ajax({
            url: '<?=$url_prefixes?>/internal/services/porter/new',
            dataType: 'json',
            method: 'POST',
            data: {
                first_name: $('#apply-firstname').val(),
                last_name: $('#apply-lastname').val(),
                email: $('#apply-email').val(),
                phone: $('#apply-tel').val(),
                city : $('#apply-city').val(),
                handyman: $('#apply-handyman').is(':checked') ? 'yes' : 'no',
                driver: $('#apply-driver').is(':checked') ? 'yes' : 'no',
                cleaning: $('#apply-cleaning').is(':checked') ? 'yes' : 'no',
                moving: $('#apply-moving').is(':checked') ? 'yes' : 'no',
                corporate: $('#apply-corporate').is(':checked') ? 'yes' : 'no',
                shopping: $('#apply-shopping').is(':checked') ? 'yes' : 'no',
                vehicle_make: $('#apply-vehicle-make').val(),
                vehicle_model: $('#apply-vehicle-model').val(),
                vehicle_year: $('#apply-vehicle-year').val()
            }
        }).done(function(data) {
            if (data['inputDeclaredValid'] == true) {
                $('.apply__form form').css('opacity', 0.5);
                $('.apply__form input').attr('disabled', 'disabled');
                data.returnMessages = ["Your application was sent!"];
            }
            cssConsole({
                '.apply__form:after': {
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
        }).fail(function(xhr, status, error){
            console.log("Status: " + status + " Error: " + error);
            console.log(xhr);
        });;
    });
});


</script>

<?php

$mapCallback = 'initAutocomplete';
include incl('snippets/map-api');
include incl('snippets/footer'); ?>
