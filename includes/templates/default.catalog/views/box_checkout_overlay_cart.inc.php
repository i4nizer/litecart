<?php
$num_items = cart::$total['items']; 
?>

<section id="box-checkout-overlay-cart" class="overlay-card">

  <div class="card-header card-overlay-header">
    <div class="card-title card-overlay-title">
		<div id="cart-overlay" class="" href="#">
			<!--<img class="image" src="{snippet:template_path}images/<?php echo !empty($num_items) ? 'cart_filled.svg' : 'cart.svg'; ?>" alt="" />-->
			<span class="navbar-icon"><?php echo functions::draw_fonticon('fa-shopping-basket'); ?></span>			
			<span class="badge quantity overlay-quantity"><?php echo $num_items ? $num_items : ''; ?></span>
		</div>
    </div>
  </div>

  <div class="headings" style="margin-top: 15px;">
    <div class="row overlay-row">
      <div class="col-xs-3">
        <?php echo language::translate('title_item', 'Item'); ?>
      </div>
	  
	  <div class="col-xs-6">
        <?php echo language::translate('title_amount', 'Amount'); ?>
      </div>

      <div class="col-xs-3">
        <?php echo language::translate('title_sum', 'Sum'); ?>
      </div>
    </div>
  </div>

  <ul class="items list-unstyled">
    <?php foreach ($items as $key => $item) { ?>
    <li class="item" data-id="<?php echo $item['product_id']; ?>" data-sku="<?php echo $item['sku']; ?>" data-name="<?php echo functions::escape_html($item['name']); ?>" data-price="<?php echo currency::format_raw($item['price']); ?>" data-quantity="<?php echo currency::format_raw($item['quantity']); ?>">
            
		<div class="row overlay-row">
		  
			<div class="col-xs-3">
				<a href="<?php echo functions::escape_html($item['link']); ?>" class="thumbnail float-start" style="margin-inline-end: 1em;">
					<img style="width: 40px !important;" class="" src="<?php echo document::href_rlink(FS_DIR_STORAGE . $item['image']['thumbnail']); ?>" alt="" />
				</a>
		  
				<!-- name -->
				<div class="name"><a href="<?php echo functions::escape_html($item['link']); ?>" style="color: inherit;"><?php echo $item['name']; ?></a></div>

				<?php if (!empty($item['options'])) echo '<small class="options">'. implode('<br />', $item['options']) .'</small>'; ?>
				<?php if (!empty($item['error'])) echo '<div class="error">'. $item['error'] .'</div>'; ?>
		  
				<!-- end name -->		  
			</div>
		

			<div class="col-xs-6">
			  <div style="display: inline-flex;">
				<div class="input-group input-group-sm" style="max-width: 48px;">
				<?php if (!empty($item['quantity_unit']['name'])) { ?>
				  <?php echo !empty($item['quantity_unit']['decimals']) ? functions::form_draw_decimal_field('item['.$key.'][quantity]', $item['quantity'], $item['quantity_unit']['decimals'], $item['quantity_min'], $item['quantity_max'], $item['quantity_step'] ? 'step="'. (float)$item['quantity_step'] .'"' : '') : functions::form_draw_overlay_cart_number_field('item['.$key.'][quantity]', $item['quantity'], $item['quantity_min'], $item['quantity_max'], $item['quantity_step'] ? 'step="'. (float)$item['quantity_step'] .'"' : ''); ?>
				  
				<?php } else { ?>
				  <?php echo !empty($item['quantity_unit']['decimals']) ? functions::form_draw_decimal_field('item['.$key.'][quantity]', $item['quantity'], $item['quantity_unit']['decimals'], 'min="0"') : functions::form_draw_overlay_cart_number_field('item['.$key.'][quantity]', $item['quantity'], 'min="0" style="width: 125px;"'); ?>
				<?php } ?>
				</div>
				<div>
				<?php echo functions::form_draw_button('update_cart_item', [$key, functions::draw_fonticon('fa-refresh')], 'submit', 'class="btn btn-sm btn-default update-cart" title="'. functions::escape_html(language::translate('title_update', 'Update')) .'" formnovalidate style="margin-inline-start: 0.5em;"'); ?>
			
				</div>
				<div style="margin-inline-start: 0.5em;"><?php echo functions::form_draw_button('remove_cart_item', [$key, functions::draw_fonticon('fa-trash')], 'submit', 'class="btn btn-sm btn-danger" title="'. functions::escape_html(language::translate('title_remove', 'Remove')) .'" formnovalidate'); ?></div>
			  </div>
			</div>

			

			<div class="col-xs-3">
			  <div class="total-price">
				<?php echo currency::format($item['display_price'] * $item['quantity']); ?>
			  </div>
			</div>
				
        </div>        				     

    </li>
    <?php } ?>
  </ul>

  <div class="card-footer subtotal text-end">
    <?php echo language::translate('title_subtotal', 'Subtotal'); ?>: <strong class="formatted-value"><?php echo !empty(customer::$data['display_prices_including_tax']) ?  currency::format(cart::$total['value'] + cart::$total['tax']) : currency::format_html(cart::$total['value']); ?></strong>
  </div>
</section>
<script>


