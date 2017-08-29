<?php
$pageClass = 'order';
$pageName = 'order1';
include '../snippets/header.php';
?>


<section class="banner order-banner">

    <div class="inner">
        <h3 class="nob">Task Summary</h3>

        <div class="whitebg-rounded" style="margin-top: 0.6rem">

            <div class="order icon">
                <i class="icon-grocery-bag"></i>
            </div>
            <div class="order description ">
                <h2 class="blue">Shopping &amp; Delivery  	<b class="down-hat"></b></h2>


                <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque ladantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto gbeatae vitae dicta sunt explicabo. Nemo enim epsam voluptatem.</p>
            </div>

            <i class="clear"></i>
        </div>
    </div>
</section>

<section class="breadcrumbs">
    <div class="inner">
        <div class="blue step">
            1. Enter Summon Details
        </div>
        <div class=" step">
            2. Enter Payment Details
        </div>
        <div class="step">
            3. Chose a Porter
        </div>

        <b class="clear"></b>
    </div>
</section>



<section class="order1 graybg" id="">
	<div class="inner">
		<div class="">
			<h1 class="blue">Enter Summon Details</h1>
			<div class="apply__form">
				<form method="post" action="order/index3.php">

                    <fieldset  class="inputGroup inputGroup--form">
                        <legend>Delivery Address</legend>
                        <div style="">
                            <i class="fa-map-marker " aria-hidden="true" style="margin-right:1rem;"></i>
                            <i class="fa fa-check black _2_1375" aria-hidden="true" style="float:right; "></i>
                            962 Jervis Street, BC V6B2E9, Canada, Unit: 403
                        </div>

                    </fieldset>


                    <fieldset  class="inputGroup inputGroup--form">
                        <legend>Pickup Address</legend>
                        <label for="pickupaddress">Street Address</label>
                        <input type="text" name="pickupaddress" id="streetaddress" placeholder="Enter Street Address" style="width: 70%" />

                        <label for="pickupaddress_apt">Unit or Apt #</label>
                        <input type="text" name="pickupaddress_apt" placeholder="Unit or Apt #" style="width: 29%; float:right" />

                        <br />
                        <br />
                        <div class="center">
                            <input class="btn btn--primary btn--large" type="submit" value="Continue" />
                        </div>
                    </fieldset>

                    <fieldset  class="inputGroup inputGroup--form">
                        <legend>Describe The Summon <span>(please be as specific as possible)</span></legend>
                    </fieldset>
                <br />
				</form>
			</div>
		</div>
	</div>
</section>

<script type="text/javascript">
$(function(){
    $('#streetaddress').focus();
    setInterval(function(){
        $('textarea').each(function(){
            $(this).css('height', $(this)[0].scrollHeight+'px');
        });
    }, 500);


	$('input[type="text"], input[type="tel"]').keyup(function(){
		$(this).prev().css('opacity', this.value.length == 0 ? 0 : 1);
	})
})


  var placeSearch, autocomplete;
  var componentForm = {
    street_number: 'short_name',
    route: 'long_name',
    locality: 'long_name',
    administrative_area_level_1: 'short_name',
    country: 'long_name',
    postal_code: 'short_name'
  };

  function initAutocomplete() {
    autocomplete = new google.maps.places.Autocomplete(
        document.getElementById('streetaddress'),
        {types: ['address']}
    );

    autocomplete.addListener('place_changed', fillInAddress);
  }

  function fillInAddress() {
    // Get the place details from the autocomplete object.
    var place = autocomplete.getPlace();
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
      });
    }
  }
</script>

<?php

$mapCallback = 'initAutocomplete';
include '../snippets/map-api.php';
include '../snippets/footer.php'; ?>
