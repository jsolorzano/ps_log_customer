<div class="panel container col-lg-12">
    <div class="panel-heading"><i class="icon-search"></i> {l s='Busca un producto' mod='uriajaxtest'}</div>
    <div class="row">
		{*{var_dump($language)}*}
		{*{var_dump($translations)}*}
        <input type="hidden" id="token" class="form-control" value="{$current_token}"/>
        <input type="text" id="search" class="form-control" placeholder="{l s='Search' mod='uritestmodule'}"/>
        <table class="table">
            <thead>
                <tr>
                    <th>{l s='ID Producto' mod='uritestmodule'}</th>
                    <th>{l s='Nombre' mod='uritestmodule'}</th>
                </tr>
            </thead>
            <tbody id="result"></tbody>
        </table>
    </div>
</div>

<script>
    const url_ajax = "{$url_ajax|escape:'html':'UTF-8'}";
    const current_token = "{$current_token}";
    const admin_dir = "{$admin_dir|escape:'html':'UTF-8'}";
    const products_url = "{$products_url|escape:'html':'UTF-8'}";
    const base_url = "{$base_url|escape:'html':'UTF-8'}";
</script>
