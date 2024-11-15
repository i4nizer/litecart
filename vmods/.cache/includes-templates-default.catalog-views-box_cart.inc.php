<style>
#cart-overlay .badge {
    background: #c90000;
    border-radius: 2rem;
    padding: 0.25em 0em;
    display: inline-block;
    text-align: center;
    color: #fff;
    position: relative;
    top: -4px;
    right: -5px;
    width: 2em;
    animation: pulsating 1.5s linear infinite;
    font-size: 10px;
	border-bottom: 1px solid #333;
}
.card-header {
	padding-bottom: 10px;
	border-bottom: 1px solid #f6f6f6;
}
.shopping-cart-content .text-center {
    padding-top: 20px;
}
a#cart-overlay {
	text-decoration: none;
}
.card-footer.subtotal.text-end {
    margin-right: 10px;
	margin-bottom: 15px;
}

.btn-proceed-checkout {
	color: #fff !important;
	width: 100%;
        margin-bottom: 40px;
	position: relative;

}

.mask {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 100;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
}

.shopping-cart {
    position: fixed;
    z-index: 1000;
    background-color: #fff;
    overflow: hidden;
    -webkit-transition: all 0.3s;
    -moz-transition: all 0.3s;
    -ms-transition: all 0.3s;
    -o-transition: all 0.3s;
    transition: all 0.3s;
	padding: 15px 15px 15px 15px;
	/*overflow-y: auto;*/
	right: -600px;
    top: 0;
    width: 360px;
    height: 100%;
	/*padding-bottom: 50px;*/
    font-size: 14px;

}
.shopping-cart-content {
    margin-top: 36px;
    overflow-y: auto;
    height: 100%;

}

.featherlight-close{
    display: block;
	right: 15px;
	position: absolute;
	cursor: pointer;
	font-size: 25px;
}

body.sr-open .shopping-cart {
    right: 0;
}

@media only screen and (max-width: 600px) {
	.shopping-cart {
		width: 100%;
	}

}

@media (min-width: 992px)  {
    .shopping-cart:after {
        right: 10px;
    }
}
@media (max-width: 767px) {
    .shopping-cart:after {
        right: 55px;
    }

}

#box-checkout-overlay-cart .btn {
    padding-left: 1em;
    padding-right: 1em;
}
.input-group-sm>.form-control, .input-group-sm>.form-select, .input-group-sm>.input-group-text {
    padding: .25rem .5rem;
    font-size: .875rem;
    border-radius: .2rem;
}
.overlay-row {
  padding: 8px;
  background: var(--input-background-color);
  border-radius: 8px;
  grid-gap: 0px !important;

}

</style>

<a id="cart" class="text-center" href="#<?php //echo functions::escape_html($link); ?>">
  <!--<img class="image" src="/includes/templates/default.admin/images/<?php echo !empty($num_items) ? 'cart_filled.svg' : 'cart.svg'; ?>" alt="" />-->
  <div class="navbar-icon"><?php echo functions::draw_fonticon('fa-shopping-basket'); ?></div>
  <small class="hidden-xs"><?php echo language::translate('title_cart', 'Cart'); ?></small>
  <div class="badge quantity header-quantity"><?php echo $num_items ? $num_items : ''; ?></div>
</a>

<div class="shopping-cart">

	<div class="featherlight-close-icon featherlight-close" aria-label="Close">✖</div>

	<div class="shopping-cart-content"></div>

</div>

<script>
$('.shopping-cart').click(function(e) {
	e.stopPropagation();
});

shoppingcartOverlay();

function shoppingcartOverlay(){

    var body = $('body'),
        mask = $('<div class="mask"></div>'),
        active = '';

    $('body').append(mask);

    /* hide active shopping-cart if close shopping-cart button or body is clicked */
    $(".featherlight-close, body").on('click', function(){
        $('body').removeClass("sr-open");
        active = "";
        $('.mask').fadeOut();
    });

}

$('#cart').on('click', function(e) {
	e.stopPropagation();
	/* slide shopping-cart right */
	$('body').addClass("sr-open");
    $('.mask').fadeIn();
    active = "sr-open";

	// fetch data for overlay cart
	$.ajax({
		url: '<?php echo document::ilink('checkout_overlay'); ?>',
		type: "POST",
		cache: false,
		success: function (data) {
			$('.shopping-cart-content').html(data).hide().fadeIn('slow');
			// add proceed-checkout button
			$('.shopping-cart-content').append('<a href="<?php echo functions::escape_html($link); ?>" class="btn btn-success btn-proceed-checkout" type="button"><?php echo language::translate('title_proceed_checkout', 'Proceed Checkout'); ?></a>');
		},

	});

});
</script>

