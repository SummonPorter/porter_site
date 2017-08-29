<?php
$pageClass = 'order';
$pageName = 'order1';

if (empty($_SERVER['HTTPS'])) {
    header("Location: https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");
    exit;
}

$page_data = null;
$service = slugToServiceId($_GET['page']);

foreach ($service_data as $type) {
    if ($type['id'] == $service) {
        $page_data = $type;
        $title = htmlspecialchars($type['name']);
        $meta_description = htmlspecialchars($type['description']);
        break;
    }
}

if ($page_data === null) {
    header("HTTP/1.1 404 Not Found");
    $page_data = [
        'name' => '404 Not Found'
    ];
}

require_once incl('snippets/header');
?>
<style>@import "<?=makeUrl('/js/jquery.timepicker.css')?>";</style>
<script type="text/javascript" src="<?=makeUrl('/js/jquery.timepicker.min.js')?>"></script>

<section class="banner order-banner" id="banner-bg" style="">

    <div class="inner">

        <div class="whitebg-rounded" style="margin-top: 0.6rem">

            <div class="order description ">
                <h2 class="blue"><?=$page_data['name']?> <rate id="titleRates"></rate></h2>

                <p><?=$page_data['description']?></p>

            </div>

            <i class="clear"></i>
        </div>
    </div>
</section>

<?php include incl('snippets/banner'); ?>


<input id="service-cat" value="<?=nameToSlug(isset($page_data['group']['name']) ? $page_data['group']['name'] : 'special')?>" type="hidden" />
<input id="service-name" value="<?=nameToSlug($page_data['name'])?>" type="hidden" />
<input id="service-id" value="<?=$page_data['id']?>" type="hidden" />
<input id="sku" value="<?=nameToSlug($page_data['name'])?>-<?=$page_data['id']?>" type="hidden" />
<input id="feeRate" value="<?=$page_data['feeRate']?>" type="hidden" />
<input id="fee" value="<?=$page_data['fee']?>" type="hidden" />
<input id="baseFee" value="<?=$page_data['baseFee']?>" type="hidden" />
<input id="trustFee" value="<?=round($page_data['trustFee'], 4)?>" type="hidden" />
<input id="defaultEstDuration" value="<?=$page_data['defaultEstDuration']?>" type="hidden" />
<input id="estTotalInput" value="50" type="hidden" />
<input id="addressCount" value="<?=$page_data['addressCount']?>" type="hidden" />
<input id="service-group-id" value="<?=$page_data['group']['id']?>" type="hidden" />
<style type="text/css">
#banner-bg {
    background-image:url('<?=$page_data['webHeader']?>');
    background-size: cover;
}
@media (max-width:48rem) {
    #banner-bg {
        background-image:url('<?=$page_data['webMobileHeader']?>');
    }
}
</style>
<style type="text/css" id="cssConsole">
</style>
<section class="breadcrumbs">
    <div class="inner">
        <div class="blue step">
            1. Enter Summon Details
        </div>
        <div class="step">
            2. Enter Payment Details
        </div>
        <div class="step">
            3. Summon Confirmation
        </div>

        <b class="clear"></b>
    </div>
</section>



<section class="order1 graybg" id="summon-details">
	<div class="inner">
		<div class="">
			<h1 class="blue">Summon Details</h1>
			<div class="apply__form">


                <?php if ($page_data['addressCount'] > 0): ?>
                <fieldset class="inputGroup inputGroup--form ">
                    <?php if ($page_data['addressCount'] > 1): ?>
                    <legend>Pickup Address</legend>
                    <?php else: ?>
                    <legend>Address</legend>
                    <?php endif; ?>
                    <form method="post" id="pickupAddress" onsubmit="return false;">
                        <label for="streetaddress">Street Address</label>
                        <input type="text" name="streetaddress" id="pickupstreetaddress" placeholder="Enter Street Address" style="width: 70%" required />

                        <label for="apt">Unit or Apt #</label>
                        <input type="text" name="apt" placeholder="Unit or Apt #" style="width: 29%; float:right" />

                        <div class="center">
                            <br />
                            <input class="btn btn--primary  btn--large" type="submit" value="Continue" />
                        </div>
                    </form>
                    <div style="display:none;" id="pickupAddressResult">
                        <input type="text" value="" class="validated-address" />
                        <a class="manatee edit-address-link">edit</a>
                    </div>
                </fieldset>
                <?php endif; ?>

                <?php if ($page_data['addressCount'] > 1): ?>
                    <fieldset class="inputGroup inputGroup--form">
                        <legend>Delivery Address</legend>
        				<form method="post" id="deliveryAddress" style="display:none" onsubmit="return false;">
    						<label for="streetaddress">Street Address</label>
                            <input type="text" name="streetaddress" id="streetaddress" placeholder="Enter Street Address" style="width: 70%" required />

    						<label for="apt">Unit or Apt #</label>
    						<input type="text" name="apt" placeholder="Unit or Apt #" style="width: 29%; float:right" />

                            <div class="center">
                                <br />
                                <input class="btn btn--primary  btn--large" type="submit" value="Continue" />
                            </div>
        				</form>
                        <div style="display:none;" id="deliveryAddressResult">
                            <input type="text" value="" class="validated-address" />
                            <a class="manatee edit-address-link">edit</a>
                        </div>
                    </fieldset>
                <?php endif; ?>

                <fieldset  class="inputGroup inputGroup--form ">
                    <legend>Describe The Summon <span>(please be as specific as possible)</span></legend>
                    <form id="descriptionForm" onsubmit="return false;" style="display:none;">
                        <textarea name="description"></textarea>
                        <br />
                        <br />
                        <div class="form2col">
                            <p>Date and time of summon</p>
                            <input id="datepicker" name="time" type="text" />
                            <input id="timepicker" name="time" type="text" />
                        </div>
                        <br />
                        <div class="center" id="descriptionSubmitDiv">
                            <input class="btn btn--primary btn--large" type="submit" value="Continue" />
                        </div>

                    </form>
                </fieldset>
                <br />
			</div>
		</div>
	</div>
</section>

<section class="order1 graybg" id="customer-details" style="display: none;">
	<div class="inner">
		<div class="">
			<h1 class="blue">Your Details <button class="btn btn--small back-btn" style="" id="back-details">back</button></h1>
			<div class="apply__form">
				<form method="post" id="yourdetails" onsubmit="return false">

                    <fieldset  class="inputGroup inputGroup--form " style="margin-top: 1rem;">
                        <div class="form2col">
                            <label for="firstname">Firstname</label>
                            <input type="text" name="firstname" id="firstname" placeholder="First name" required />
                            <label for="lastname">Lastname</label>
                            <input type="text" name="lastname" id="lastname" placeholder="Last name" required />
                            <label for="mobile">Mobile</label>
                            <input type="tel" patten="\+?\d*[\- ]?\d{3}[\- ]?\d{3}[\- ]?\d{4}|\+?\d{10,}"
                              name="mobile" class="mobile-bg" id="mobile" placeholder="Mobile Number" required />

                            <label for="email">Email</label>
                            <input type="email" name="email" class="email-bg" id="email" placeholder="Email Address" required />
                            <i class="fa fa-email-after"></i>
                        </div>


                        <div class="center">
                            <input class="btn btn--primary btn--large" type="submit" value="Continue" />
                        </div>
                    </fieldset>

                <br />
				</form>
			</div>
		</div>
	</div>
</section>


<section class="order1 graybg" id="payment-details" style="display:none;">
	<div class="inner">
		<h1 class="blue">Payment Details <button class="btn btn--small back-btn" style="" id="back-payment">back</button></h1>
        <form method="post" id="payment" onsubmit="return false;">
    		<div class="flex">
                <flex-1 style="min-width:60%;flex-basis:26rem;">
        			<div class="apply__form">
                        <fieldset  class="inputGroup inputGroup--form form-group small" style="margin-top: 1rem;">
    			            <h6 class="blue">Credit Card Details</h6>
                            <br />
                            <div class="flex row validation">
                                <flex-1 style="min-width:50%; flex-basis: 14rem;">
                                    <input type="text" name="firstname" id="cardholder_first_name" class="cc-name" placeholder="Cardholder First Name" required />
                                </flex-1>
                                <flex-1 style="min-width:50%; flex-basis: 14rem; padding-left:1rem;">
                                    <input type="text" name="lastname" id="cardholder_last_name" class="cc-name" placeholder="Cardholder Last Name" required />
                                </flex>
                            </div>
                            <div class="flex row validation" style="">
                                <input type="tel" name="cardno" id="cardno" class="cc-number" placeholder="Card Number" required />
                            </div>
                            <div class="flex row ">
                                <flex-1 style="min-width:50%; flex-basis: 8rem;">
                                    <input type="tel" name="exp" id="cardno" class="cc-exp" autocomplete="cc-exp" placeholder="MM / YY" required />
                                </flex-1>
                                <flex-1 style="min-width:50%; padding-left:1rem; flex-basis: 6rem;">
                                    <input type="tel" name="cvc" id="cardno" class="cc-cvc"
                                      autocomplete="off" placeholder="CVC" />
                                </flex-1>
                            </div>

                            <h6 class="blue">Discount</h6>
                            <br />
                            <input type="text" name="discount_code" id="discount_code" class="discount_code"
                              autocomplete="discount_code" placeholder="Promo Code (Case Sensitive)" />
                            <div id="discountSpinner" class="spinner" style="display: none; float: right;">
                                <div class="bounce1"></div>
                                <div class="bounce2"></div>
                                <div class="bounce3"></div>
                            </div>

                        </fieldset>
                        <br />
        			</div>
                </flex-1>
                <flex-1 style="flex-basis:22rem;">
                    <div class="apply__form two-rem-left" style="">
                        <fieldset  class="inputGroup inputGroup--form form-group small" style="margin-top: 1rem;">
                            <h6 class="blue">Summon Summary</h6>
                            <br />
                            <div class="center ">

                                <table class="order-summary" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td>Order Subtotal:</td>
                                        <td id="subtotal"></td>
                                    </tr>
                                    <tr>
                                        <td>Trust and Safety Fee:</td>
                                        <td id="trustAndSafetyFee"></td>
                                    </tr>
                                    <tr>
                                        <td>Tax:</td>
                                        <td>Not included in estimate</td>
                                    </tr>
                                    <tr id="discountRow" style="display:none;">
                                        <td>Discount:</td>
                                        <td id="discountDisplay"></td>
                                    </tr>
                                    <tr id="baseFeeRow">
                                        <td>Fee:</td>
                                        <td id="baseFeeText"></td>
                                    </tr>
                                    <tr class="blue" id="estTotalRow">
                                        <td>Estimated Quote:</td>
                                        <td id="estTotal"></td>
                                    </tr>
                                </table>

                                <div id="submitbtnmessagebox">
                                  <input class="btn btn--primary" type="submit" value="Summon Porter" />
                                </div>
                                <br />
                                <div id="order-loader" class="sk-fading-circle" style="display:none;">
                                    <div class="sk-circle1 sk-circle"></div>
                                    <div class="sk-circle2 sk-circle"></div>
                                    <div class="sk-circle3 sk-circle"></div>
                                    <div class="sk-circle4 sk-circle"></div>
                                    <div class="sk-circle5 sk-circle"></div>
                                    <div class="sk-circle6 sk-circle"></div>
                                    <div class="sk-circle7 sk-circle"></div>
                                    <div class="sk-circle8 sk-circle"></div>
                                    <div class="sk-circle9 sk-circle"></div>
                                    <div class="sk-circle10 sk-circle"></div>
                                    <div class="sk-circle11 sk-circle"></div>
                                    <div class="sk-circle12 sk-circle"></div>
                                </div>

                                <br />
                                <p>
                                You are charged only after your summon is completed.
                                Summons have a one-hour minimum. a 5% Trust and Safety fee
                                 is added to the porter total rate. If you cancel your task
                                 within 24 hours of the scheduled start time, you will be
                                  charged a $50 cancellation fee.<br />
                                I agree to the terms of service &amp; privacy policy.
                                </p>
                            </div>
                        </fieldset>
                    </div>
                </flex-1>
    		</div>
        </form>
	</div>
</section>


<section class="order1 graybg" id="result-success" style="display:none;">
	<div class="inner">
		<div class="">
			<div class="center ">
    			<h1 class="blue">Success!</h1>


                <img src="<?=makeUrl('/images/success.svg')?>" style="width: 25%; margin: 2rem auto; display: block;" alt="Success!" />

                <p class="manatee">A Porter has been summoned. An email has been sent with follow up instructions.</p>
            </div>
		</div>
	</div>
</section>

<script type="text/javascript" src="<?=makeUrl('/js/order.js?nocache=')?><?=time()?>"></script>
<?php if (DEVELOPMENT): ?>
<script>
urlBase = '<?=makeUrl('/sys/app_dev.php/')?>';
</script>
<?php endif;?>
<script type="text/javascript" src="<?=makeUrl('/js/jquery.payment.min.js')?>"></script>
<script type="text/javascript" src="https://www.payfirma.com/media/payfirma.minified.js"></script>

<script type="text/javascript">
jQuery(function($) {
    $('[data-numeric]').payment('restrictNumeric');
    $('.cc-number').payment('formatCardNumber');
    $('.cc-exp').payment('formatCardExpiry');
    $('.cc-cvc').payment('formatCardCVC');
    $.fn.toggleInputError = function(erred) {
        this.parent('.form-group').toggleClass('has-error', erred);
        return this;
    };


    // $('#discount_code').css('background-image', 'url(./images/tag-with-text.php?t=5.99)')
});
</script>

<?php

$mapCallback = 'initAutocomplete';
include incl('snippets/map-api');

include incl('snippets/footer'); ?>
