<?php echo functions::form_draw_form_begin('checkout_form', 'post', document::ilink('order_process'), false, 'autocomplete="off"'); ?>

<section id="box-checkout">
	<div class="cart wrapper"></div>
</section>

<?php echo functions::form_draw_form_end(); ?>


<script>
// Queue Handler

var updateQueue = [
{component: 'cart',     data: null, refresh: true},

{component: 'summary',  data: null, refresh: true}
];

function queueUpdateTask(component, data, refresh) {

	updateQueue = jQuery.grep(updateQueue, function(tasks) {
	  return (tasks.component == component) ? false : true;
	});

	updateQueue.push({
	  component: component,
	  data: data,
	  refresh: refresh
	});

	runQueue();
}

var queueRunLock = false;
function runQueue() {

	if (queueRunLock) return;
	if (!updateQueue.length) return;

	queueRunLock = true;

	task = updateQueue.shift();

	if (console) console.log('Processing ' + task.component);

	if (!$('body > .loader-wrapper').length) {
	  var loader = '<div class="loader-wrapper">'
				 + '  <div class="loader" style="width: 256px; height: 256px;"></div>'
				 + '</div>';
	  $('body').append(loader);
	}

	if (task.refresh) {
	  $('#box-checkout .'+ task.component +'.wrapper').fadeTo('fast', 0.15);
	}
	
	var url = '<?php echo document::ilink('ajax/checkout_overlay_cart'); ?>';

	if (task.component == 'summary') {
	  var comments = $(':input[name="comments"]').val();
	  var terms_agreed = $(':input[name="terms_agreed"]').prop('checked');
	}
	

	$.ajax({
	  type: task.data ? 'post' : 'get',
	  url: url,
	  data: task.data,
	  dataType: 'html',
	  beforeSend: function(jqXHR) {
		jqXHR.overrideMimeType('text/html;charset=<?php echo language::$selected['charset']; ?>');
	  },
	  error: function(jqXHR, textStatus, errorThrown) {
		$('#box-checkout .'+ task.component +'.wrapper').html('An unexpected error occurred, try reloading the page.');
	  },
	  success: function(html) {
		if (task.refresh) $('#box-checkout .'+ task.component +'.wrapper').html(html).fadeTo('fast', 1);
		if (task.component == 'summary') {
		  $(':input[name="comments"]').val(comments);
		  $(':input[name="terms_agreed"]').prop('checked', terms_agreed);
		}
		
	  },
	  complete: function(html) {
		if (!updateQueue.length) {
		  $('body > .loader-wrapper').fadeOut('fast', function(){
			$(this).remove();
		  });
		}
		queueRunLock = false;
		runQueue();
				
		var sum = 0;
		$('input[data-type="overlaycartnumber"]').each(function() {
			sum += Number($(this).val());
		});
		// hide proceed checkout button if no items
		if(sum == 0) {
			$('.btn-proceed-checkout').hide();
			$('.header-quantity').hide();
			//alert(sum);
		}
		else {
			$('.btn-proceed-checkout').show();
			//alert('not 0');
		}
		
	  }
	});
}

runQueue();

// Cart

$('#box-checkout .cart.wrapper').on('click', 'button[name="remove_cart_item"]', function(e){
	e.preventDefault();
	var data = $(this).closest('li').find(':input').serialize() + '&remove_cart_item=' + $(this).val();
	queueUpdateTask('cart', data, true);

	queueUpdateTask('summary', true, true);

	// update amount items in main header red quantity circle
	var sum = 0;
	$('input[data-type="overlaycartnumber"]').each(function() {
		sum += Number($(this).val());
	});

	var removeItems = $(this).closest('.row').find('input[data-type="overlaycartnumber"]').val();
	var itemsLeft = (sum - removeItems);
	// inject new quantity items
	$('.header-quantity').html(itemsLeft);
	
});

$('#box-checkout .cart.wrapper').on('click', 'button[name="update_cart_item"]', function(e){
	e.preventDefault();
	var data = $(this).closest('li').find(':input').serialize()
			 + '&update_cart_item=' + $(this).val();
	queueUpdateTask('cart', data, true);

	queueUpdateTask('summary', true, true);

	// update amount items in main header red circle
	var sum = 0;
	$('input[data-type="overlaycartnumber"]').each(function() {
		sum += Number($(this).val());
		
	});
	// update new sum in main header red quantity circle
	$('.header-quantity').html(sum);

});

// Customer Form: Toggles



// Customer Form: Get Address



// Customer Form: Fields

 


// Customer Form: Checksum



// Customer Form: Auto-Save

  

  

// Customer Form: Process Data



// Shipping Form: Process Data



// Payment Form: Process Data



// Summary Form: Process Data




</script>