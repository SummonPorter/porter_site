

// note: no need to wait for domdocumentloaded here because
// we are using event delegation.
var urlBase = '/sys';
var addressUrl = '/internal/services/validate/location';
var discountUrl = '/internal/services/validate/discount';
var formatTime = 'H:i:s';
var formatDate = 'Y-m-d';
var dateFormat = formatDate + ' ' + formatTime;
var flatDiscount = 0;
var percentDiscount = 0;
var discountCode;

var minimum_dates = {
    "3": 2 * 24 * 60 * 60 * 1000, // Handyman
    "1": 0,                       // shopping and delivery
    "2": 2 * 24 * 60 * 60 * 1000, // Cleaning
    "4": 3 * 24 * 60 * 60 * 1000, // Moving
    "7": 1 * 24 * 60 * 60 * 1000, // corporate
    "day":   24 * 60 * 60 * 1000
};

var banned_dates = {
	'*' : { // year
		12: {  // month
			24: true, // *-12-24
			25: true, // *-12-25
			31: true, // *-12-31
		},
		1: {
			1: true // *-01-01
		}
	}
};

var isDateBanned = function(d) {
	var undefined;
    var year = d.getFullYear();
    var month = d.getMonth()+1;
    var date = d.getDate();
	
	if (banned_dates['*'][month] !== undefined) {
		return !!banned_dates['*'][month][date];
	}
	
	if (banned_dates[year] !== undefined) {
        if (banned_dates[year][month] !== undefined) {
			return !!banned_dates[year][month][date];
		}
    }
	
};


var addressCompleteCb = function () {
    $('#descriptionForm').fadeIn(function(){
        var d = new Date();
        d.setDate(d.getDate() + minimum_dates[$('#service-group-id').val()] / (minimum_dates.day));
        $('#datepicker').datepicker({
            changeMonth: true,
            changeYear: true,
            minDate: d,
			beforeShowDay: function(d) {
				// http://api.jqueryui.com/datepicker/#option-beforeShowDay
				return [!isDateBanned(d), "", ""];
			},
            dateFormat: 'yy-mm-dd'
        });
        $('#timepicker').timepicker({ 'scrollDefault': 'now', 'timeFormat': 'h:ia'  });
        scrollTo($('#descriptionForm').prev());

        try {
            ga('send', {
              hitType: 'pageview',
              page: location.pathname+'#describe-summon',
              location: location.pathname+'#describe-summon'
            });
        } catch (e) {}
    })
    $('#descriptionForm textarea[name="description"]').focus();

};
var addressCount = function () {
    return parseInt($('#addressCount').val(), 10);
};

var scrollTo = function (el) {
    var menu = 0;
    if ($('header:visible').length) {
        menu -= $('header').outerHeight();
    }
    $('html, body').animate({
        scrollTop: $(el).offset().top + menu
    }, 400);
};

$('body').on('submit', 'form#pickupAddress', function(e){
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    cssConsole({});
    $.ajax({
        url: urlBase+addressUrl,
        data: {
            location: $('#pickupAddress input[name="streetaddress"]').val()
        },
        dataType: 'json',
        method: "POST"
    }).done(function(data) {
        if (data['inputDeclaredValid'] == true) {
            var unit = $('#pickupAddress input[name="apt"]').val();
            $('#pickupAddressResult input').val(
                $('#pickupAddress input[name="streetaddress"]').val() + (unit.length ? ' Unit: ' + unit : '')
            );

            $('#pickupAddress').fadeOut(400, function(){
                $('#pickupAddressResult').fadeIn(400, addressCount() > 1 ? (function(){
                    $('#deliveryAddressResult').hide();
                    $('#deliveryAddress').fadeIn(400);
                    scrollTo($('#deliveryAddress').prev());
                    $('#deliveryAddress input[name="streetaddress"]').focus();

                    try {
                        ga('send', {
                          hitType: 'pageview',
                          page: location.pathname+'#address-2',
                          location: location.pathname+'#address-2'
                        });
                    } catch (e) {}

                }) : addressCompleteCb);
            })
        } else {
            cssConsole({
                '#pickupAddress div:before': {
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
    });
    return false;
});


$('body').on('submit', 'form#deliveryAddress', function(e){
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    cssConsole({});
    $.ajax({
        url: urlBase+addressUrl,
        data: {
            location: $('#deliveryAddress input[name="streetaddress"]').val()
        },
        dataType: 'json',
        method: "POST"
    }).done(function(data) {
        if (data['inputDeclaredValid'] == true) {
            var unit = $('#deliveryAddress input[name="apt"]').val();
            $('#deliveryAddressResult input').val(
                $('#deliveryAddress input[name="streetaddress"]').val() + (unit.length ? ' Unit: ' + unit : '')
            );

            $('#deliveryAddress').fadeOut(400, function(){
                $('#deliveryAddressResult').fadeIn(400, addressCompleteCb)
            })
        } else {
            cssConsole({
                '#deliveryAddress div:before': {
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
    });;
    return false;
});

$(function(){
    if (addressCount() == 0) {
        addressCompleteCb();
    }
});

$('body').on('submit', 'form#descriptionForm', function(e) {
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    cssConsole({});

    if ($('.validated-address').not(':visible').length != 0) {
        cssConsole({
            '#descriptionSubmitDiv:before': {
                background: 'rgba(181, 151, 76, 1)',
                content: '"Please submit all addresses"',
                display: 'block',
                padding: '1rem',
                marginBottom: '1rem',
                color:'#1d1d51',
                clear: "both",
                animationName: 'shake-horizontal',
                animationDuration: '.8s',
                animationTimingFunction: 'ease-in-out'
            }
        });
        return false;
    }

    if (!$('#datepicker').val().match(/\d{4}-\d{2}-\d{2}/) || !$('#timepicker').val().match(/\d{2}:\d{2}\w{2}/)) {
        cssConsole({
            '#descriptionSubmitDiv:before': {
                background: 'rgba(181, 151, 76, 1)',
                content: '"Please enter a valid date and time (yyyy-mm-dd HH:MMAA)"',
                display: 'block',
                padding: '1rem',
                marginBottom: '1rem',
                color:'#1d1d51',
                clear: "both",
                animationName: 'shake-horizontal',
                animationDuration: '.8s',
                animationTimingFunction: 'ease-in-out'
            }
        });
        return false;
    }

    $('section#summon-details').slideUp(400, function(){
        $('section#customer-details').slideDown(400, function(){
            highlightTab(1);
            $('input#firstname').focus();
            try {
                ga('send', {
                  hitType: 'pageview',
                  page: location.pathname+'#your-details',
                  location: location.pathname+'#your-details'
                });
            } catch (e) {}
        });
        scrollTo('#customer-details');
    });

});

$('body').on('submit', 'form#yourdetails', function(e) {
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    cssConsole({});
    var fname = $('input#firstname').val();
    var lname = $('input#lastname').val();
    var mobile = $('input#mobile').val();
    var email = $('input#email').val();
    if (fname.length == 0 || lname.length == 0 || mobile.length == 0 || email.length == 0) {
       cssConsole({
           '#yourdetails div.center:before': {
               background: 'rgba(181, 151, 76, 1)',
               content: '"Please fill in all the fields."',
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
   } else if (!email.match(/(.*)@(.*)\.(.*)/)) {
      cssConsole({
          '#yourdetails div.center:before': {
              background: 'rgba(181, 151, 76, 1)',
              content: '"The e-mail appears incomplete."',
              display: 'block',
              padding: '1rem',
              marginTop: '1rem',
              marginBottom: '1rem',
              color:'#1d1d51',
              clear: "both",
              animationName: 'shake-horizontal',
              animationDuration: '.8s',
              animationTimingFunction: 'ease-in-out'
          }
      });
  } else {
      $('section#customer-details').slideUp(400, function(){
          $('section#payment-details').slideDown(400, function(){
              highlightTab(2);
          });
          scrollTo('#payment-details');

          try {
              ga('send', {
                hitType: 'pageview',
                page: location.pathname+'#payment',
                location: location.pathname+'#payment'
              });
          } catch (e) {}
      });
  }

  return false;
});

function displayDiscount ()
{
    $('#discountRow').hide();
    if (discountCode.length == 0) {
        return false;
    }
    var discountTmp = (flatDiscount > 0
        ? '$'+format_number(flatDiscount)
        : (percentDiscount+'%')
    );

    if (flatDiscount + percentDiscount > 0) {
        $('#discountRow').show();
        $('#discountDisplay').html('-' + discountTmp);
    }

    calculatePrices();
}

window.discountxhr = null;
window.discountTimeout = null;
$('body').on('keyup', '#discount_code', function(e){
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    var input = e.target;
    var codeEntered = input.value;
    discountCode = codeEntered;

    clearTimeout(window.discountTimeout);

    if (codeEntered.length == 0) {
        $('#discount_code').removeClass('fa-check').removeClass('fa-cross');
        displayDiscount();
        return false;
    }

    window.discountTimeout = setTimeout(function(){
        flatDiscount = 0;
        percentDiscount = 0;
        displayDiscount();
        if (window.discountxhr) {
            window.discountxhr.abort();
        }
        $('#discountSpinner').show();
        $('#discount_code').removeClass('fa-check').removeClass('fa-cross');
        window.discountxhr = $.ajax({
            url: urlBase+discountUrl,
            data: {
                'discount_code': codeEntered,
                'service_id': $('#service-id').val()
            },
            dataType: 'json',
            method: "POST"
        }).done(function(data) {
            /*if (codeEntered == 'test') {
                data = {
                    inputDeclaredValid: true,
                    amount: 0.01,
                    type: 'flat'
                }
            }*/
            if (data['inputDeclaredValid']) {
                $('#discount_code').addClass('fa-check').removeClass('fa-cross');
                if (data['type'] == 'flat') {
                    flatDiscount = data['amount'];
                    percentDiscount = 0;
                } else {
                    percentDiscount = data['amount'];
                    flatDiscount = 0;
                }
            } else {
                $('#discount_code').removeClass('fa-check').addClass('fa-cross');
                if (codeEntered.length == 0)
                    $('#discount_code').addClass('fa-cross');
            }
            displayDiscount();
        }).always(function(){
            window.discountxhr = null;
            $('#discountSpinner').hide();
        });
    }, 1000);


});

$('body').on('submit', 'form#payment', function(e) {
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    var cardType = $.payment.cardType($('.cc-number').val());
    cssConsole({});

    $('#order-loader').show();
    $('#submitbtnmessagebox input').attr('disabled','disabled').css('opacity', 0.2);

    console.log(cardType);

    var card_number = $('.cc-number').val();
    var card_expiry = $('.cc-exp').val().split('/');
    var card_expiry_month = parseInt(card_expiry[0], 10);
    var card_expiry_year = parseInt(card_expiry[1], 10);
    var cvv2 = $('.cc-cvc').val();

    var timeVal = (function(time){
        time = time.toLowerCase().match(/(\d{2}):(\d{2})(\w{2})/);
        time[1] = parseInt(time[1], 10);
        time[2] = parseInt(time[2], 10);
        if (time[3] == 'pm') {
            time[1] += 12;
        } else {
            if (time[1] == 12) time[1] = 0;
        }
        return (time[1] < 10 ? '0':'') + time[1].toString() + ':' + (time[2] < 10 ? '0':'') + time[2].toString() + ':00';
    })($('#timepicker').val());

    var key = '2d2d2d2d2d424547494e205055424c4943204b45592d2d2d2d2d4d494942496a414e42676b71686b6947397730424151454641414f43415138414d49494243674b434151454132354a532f77393066686b79506b726576356431476a3162766d686a314e6f2f3633355a47757636474c7639477939675a58354b527762706b6635676330524e4536316a7665456136537069624e52556a4e4a7267657433596b4a6f6e43545771727435796d36305a485769436c473439475834713264496d324856654c4957465538354154337273426f65587576646376687a685a51613061325a42463457524a376177596831646245724e667736393531593258594349396b6a484667337775553949464f5a70397548703264442b4d4e69515279694979514d4263594c4e6670304650727348523151715a504c5749474e6333636d70724a4e6f636650554d42656154766750656b73495a32567866694f6b5858756e636b71753050356b494a3743622f576c674d6c4c30347a703945744a6f39673559433151376e32426265486f4b75566557433858654b367673733976774944415141422d2d2d2d2d454e44205055424c4943204b45592d2d2d2d2d';
    var z = new Payfirma(key, {
        'card_number': card_number,
        'card_expiry_month': card_expiry_month,
        'card_expiry_year':  card_expiry_year,
        'cvv2': cvv2
    }, {
        'deliver_address': $('form#deliveryAddress input[name="streetaddress"]').val() || '',
        'deliver_address_apt': $('form#deliveryAddress input[name="apt"]').val() || '',
        'pickup_address': $('form#pickupAddress input[name="streetaddress"]').val() || '',
        'pickup_address_apt': $('form#pickupAddress input[name="apt"]').val() || '',
        'summon_description': $('form#descriptionForm textarea[name="description"]').val(),

        'first_name': $('input#firstname').val(),
        'last_name': $('input#lastname').val(),
        'mobile': $('input#mobile').val(),
        'email': $('input#email').val(),
        'card_holder_first_name': $('form#payment input[name="firstname"]').val(),
        'card_holder_last_name': $('form#payment input[name="lastname"]').val(),
        'discount_code': $('form#payment input[name="discount_code"]').val(),
        'service_id': $('#service-id').val(),
        'currency': "CA$",
        'est_total': $('#estTotalInput').val(),
        'datetime': $('#datepicker').val() + ' ' + timeVal
    }, urlBase+'/internal/services/submit/process', paymentCallback);


});

function saveForm (){
    sessionStorage.setItem(name, $('#inputName').val());
    sessionStorage.setItem(email, $('#inputEmail').val());
    sessionStorage.setItem(phone, $('#inputPhone').val());
    sessionStorage.setItem(subject, $('#inputSubject').val());
    sessionStorage.setItem(detail, $('#inputDetail').val());

    var name = sessionStorage.getItem(name);
    if (name !== null) $('#inputName').val(name);

}
window.onbeforeunload = function() {
}

function paymentCallback(response) {
    var data = JSON.parse(response);
    if (data.inputDeclaredValid) {
        try{
            ga('ecommerce:addTransaction', {
                'id': data['transactionId'],                     // Transaction ID. Required.
                'affiliation': 'summonporter.ca',   // Affiliation or store name.
                'revenue': $('#estTotalInput').val(),               // Grand Total.
                'shipping': '0',                  // Shipping.
                'tax': '0'                     // Tax.
            });
            ga('ecommerce:addItem', {
                'id': data['transactionId'],                     // Transaction ID. Required.
                'name': $('#service-name').val(),    // Product name. Required.
                'sku': $('#sku').val(),                 // SKU/code.
                'category': $('#service-cat').val(),         // Category or variation.
                'price': $('#estTotalInput').val(),                 // Unit price.
                'quantity': '1'                   // Quantity.
            });
            ga('ecommerce:send');
        } catch (e) {
            // GA might get blocked, so continue anyways
        }

      // show success page
      highlightTab(3);
      $('banner').slideUp();
      $('#payment-details').slideUp('600', function(){
          $('#result-success').hide().fadeIn('slow', function(){

            try {
                ga('send', {
                  hitType: 'pageview',
                  page: location.pathname+'#success',
                  location: location.pathname+'#success'
                });
            } catch (e) {}
          });
      });
    } else {
        $('#order-loader').hide();
        $('#submitbtnmessagebox input').removeAttr('disabled').css('opacity', 1);
         cssConsole({
             '#submitbtnmessagebox:before': {
                 background: 'rgba(181, 151, 76, 1)',
                 content: '"'+data.returnMessages.join("<br />")+'"',
                 display: 'block',
                 padding: '1rem',
                 marginTop: '1rem',
                 marginBottom: '1rem',
                 color:'#1d1d51',
                 clear: "both",
                 animationName: 'shake-horizontal',
                 animationDuration: '.8s',
                 animationTimingFunction: 'ease-in-out'
             }
         });
    }
};

function highlightTab (tab) {
    $($('.breadcrumbs .inner div').removeClass('blue')[tab-1]).addClass('blue');
}

function calculatePrices ()
{ // This is only an estimate, so clients who modify values won't be stealing anything

    var estDuration = parseFloat($('#defaultEstDuration').val());
    var baseFee = parseFloat($('#baseFee').val());
    var fee = parseFloat($('#fee').val());
    var feeRate = $('#feeRate').val();
    var trustFee = parseFloat($('#trustFee').val()) - 1;
    var total;
    var subtotal, safetyFee;
    $('#baseFeeText').text('$' + format_number(baseFee));

    var feeTitle = "";
    if (baseFee > 0 && estDuration == 0) {
        feeTitle += $('#baseFeeText').text() + ' base fee + $' + format_number(fee) + ' per ' + feeRate;
    } else if (baseFee > 0 && fee == 0) {
        feeTitle += $('#baseFeeText').text() + ' per summon';
    } else {
        feeTitle += '$' + format_number(fee) + ' per ' + feeRate;
    }

    if (feeTitle.length)
        $('#titleRates').text(feeTitle);


    if (estDuration == 0) {
        subtotal = fee;
        safetyFee = format_number(feeRate);
        $('#subtotal').text(
            '$' + format_number(fee) + ' per ' + feeRate
        );
        if (trustFee > 0) {
            var feeDisplay = Math.round(100*(trustFee));
            $('#trustAndSafetyFee').text((feeDisplay) + "%");
        }
        if (baseFee == 0) {
            $('#baseFeeRow').hide();
        }
        $('#estTotalRow').hide();
    } else {
        subtotal = fee * estDuration + baseFee;
        $('#baseFeeRow').hide();
        safetyFee = Math.floor(100*(trustFee * subtotal))/100;
        total = subtotal + safetyFee;
        $('#subtotal').text(
            '$' + format_number(subtotal)
        );
        $('#trustAndSafetyFee').text(
            '$' + format_number(safetyFee)
        );

        if (flatDiscount + percentDiscount > 0) {
            if (flatDiscount) {
                total -= flatDiscount;
            } else {
                total = total * (1 - (percentDiscount/100));
            }
        }

        $('#estTotal').text('$' + format_number(total) );
        $('#estTotalInput').val(total);


    }


    var id = $('#service-id').val();
    if (id == 18) {
        $('#subtotal').prepend('<span>Starting from </pan>');
    }
}

$(function(){
    highlightTab(1);
    $('#streetaddress').focus();
    setInterval(function(){
        $('textarea').each(function(){
            $(this).css('height', $(this)[0].scrollHeight+'px');
        });
    }, 500);

	$('input[type="text"], input[type="tel"]').keyup(function(){
		$(this).prev().css('opacity', this.value.length == 0 ? 0 : 1);
	});

    geolocate();

    calculatePrices();

    //

    setTimeout(function(){
        if (window.location.hash == '#pay') {
            $('#summon-details').hide()
            $('#payment-details').show();
        }
    }, 500)
})

function format_number (num) {
    var n = num,
    c = isNaN(c = Math.abs(c)) ? 2 : c,
    d = d == undefined ? "." : d,
    t = t == undefined ? "," : t,
    s = n < 0 ? "-" : "",
    i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
    j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}


var placeSearch, autocomplete, autocomplete2;
var componentForm = {
    street_number: 'short_name',
    route: 'long_name',
    locality: 'long_name',
    administrative_area_level_1: 'short_name',
    country: 'long_name',
    postal_code: 'short_name'
};

function initAutocomplete() {
    if (tmp = document.getElementById('streetaddress'))
        autocomplete = new google.maps.places.Autocomplete(
            tmp,
            {types: []}
        );
    // autocomplete.addListener('place_changed', void);

    if (tmp = document.getElementById('pickupstreetaddress'))
        autocomplete2 = new google.maps.places.Autocomplete(
            tmp,
            {types: []}
        );
    // autocomplete2.addListener('place_changed', void;
}

function fillInAddress() {
    // Get the place details from the autocomplete object.
    // var place = autocomplete.getPlace();
    // console.log(place);
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
            if (autocomplete) autocomplete.setBounds(circle.getBounds());
            if (autocomplete2) autocomplete2.setBounds(circle.getBounds());
        });
    }
}


$('body').on('focus', '.validated-address', function() {
    $(this).parent().hide().prev().slideDown(400, function(){
        $('input[name=streetaddress]', $(this)).focus();
    })
});
$('body').on('click', '.edit-address-link', function() {
    $(this).prev().focus();
});
$('body').on('click', '#back-details', function() {
    $('section#summon-details').slideDown(400, function(){
        $('section#customer-details').slideUp(400, function(){
            highlightTab(1);
        });
        scrollTo('#summon-details');
    });
});
$('body').on('click', '#back-payment', function() {
    $('section#customer-details').slideDown(400, function(){
        $('section#payment-details').slideUp(400, function(){
            highlightTab(1);
        });
        scrollTo('#customer-details');
    });
});
