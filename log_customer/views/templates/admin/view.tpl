<div class="panel col-lg-4">
	<div class="panel-heading">
	  <i class="icon-credit-card"></i>
	  {*{l s='Order' mod='log_customer'}*}
	  {l s='Order' d='Modules.Logcustomer.View'}
	  <span class="badge">{$order->reference}</span>
	  <span class="badge">N°{$order->id}</span>
	</div>
</div>

{if $num_gifts > 0}
<!--{var_dump($link)}-->
<!--
{var_dump($urls.current_url)}
-->
<div class="panel container col-lg-12">
    <div class="panel-heading">
		<i class="icon-gift"></i> 
		{*{l s='Gifts' mod='log_customer'}*}
		{l s='Gifts' d='Modules.Logcustomer.View'}
		<span class="badge">{$num_gifts}</span>
	</div>
    <div class="row">
        <table class="table">
            <thead>
                <tr>
					{*
                    <th>{l s='Product Id' mod='log_customer'}</th>
                    <th>{l s='Image' mod='log_customer'}</th>
                    <th>{l s='Name' mod='log_customer'}</th>
                    <th>{l s='Reference' mod='log_customer'}</th>
                    *}
                    <th>{l s='Product Id' d='Modules.Logcustomer.View'}</th>
                    <th>{l s='Image' d='Modules.Logcustomer.View'}</th>
                    <th>{l s='Name' d='Modules.Logcustomer.View'}</th>
                    <th>{l s='Reference' d='Modules.Logcustomer.View'}</th>
                </tr>
            </thead>
            <tbody id="result">
				{foreach $gifts as $gift}
					<tr>
						<td>{$gift->id}</td>
						<td>
							<img style="height: 52px" class="replace-2x img-responsive" src="{$link->getImageLink($gift->link_rewrite, $gift->image.id_image, 'small_default')|escape:'html':'UTF-8'}" itemprop="image" />
						</td>
						<td>{$gift->name[1]}</td>
						<td>{$gift->ean13}</td>
					</tr>
				{/foreach}
            </tbody>
        </table>
    </div>
</div>
{/if}

<div class="panel container col-lg-12">
    <div class="panel-heading">
		<i class="icon-shopping-cart"></i> 
		{*{l s='Products' mod='log_customer'}*}
		{l s='Products' d='Modules.Logcustomer.View'}
		<span class="badge">{$num_products}</span>
	</div>
    <div class="row">
<!--
		{var_dump($gifts)}
-->
        <table class="table">
            <thead>
                <tr>
                    {*
                    <th>{l s='Product Id' mod='log_customer'}</th>
                    <th>{l s='Image' mod='log_customer'}</th>
                    <th>{l s='Name' mod='log_customer'}</th>
                    <th>
						<span class="title-box">{l s='Price' mod='log_customer'}</span><br>
						<small class="text-muted">Impuestos incluidos</small>
					</th>
                    <th>{l s='Quantity' mod='log_customer'}</th>
                    <th>{l s='Reference' mod='log_customer'}</th>
					*}
                    <th>{l s='Product Id' d='Modules.Logcustomer.View'}</th>
                    <th>{l s='Image' d='Modules.Logcustomer.View'}</th>
                    <th>{l s='Name' d='Modules.Logcustomer.View'}</th>
                    <th>
						<span class="title-box">{l s='Price' d='Modules.Logcustomer.View'}</span><br>
						<small class="text-muted">{l s='Taxes included' d='Modules.Logcustomer.View'}</small>
					</th>
                    <th>{l s='Quantity' d='Modules.Logcustomer.View'}</th>
                    <th>{l s='Reference' d='Modules.Logcustomer.View'}</th>
                </tr>
            </thead>
            <tbody id="result">
				{foreach $products as $product}
					<tr>
						<td>{$product['product_id']}</td>
						<td>
							{if isset($product['image']) && $product['image']->id}{$product['image_tag']}{/if}
						</td>
						<td>{$product['product_name']}</td>
						<td>{$product['unit_price_tax_incl']|number_format:2:",":"."}</td>
						<td>{$product['product_quantity']}</td>
						<td>{$product['product_reference']}</td>
					</tr>
				{/foreach}
            </tbody>
        </table>
    </div>
</div>
