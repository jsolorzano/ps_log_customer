$(document).ready(function(){
    $('#search').on('keyup', function(){
        searchProduct();
    })
})

function searchProduct(){
    var res = '';
    var buscar = $('#search').val();
    if (buscar.length > 0) {
        $.ajax({
            type: "POST",
            url: url_ajax,
            data: {'search' : buscar},
            dataType: "json",
            success: function(response){
                $.each(response, function(i, v) {
                    res += '<tr><td>'+v.id_product+'</td><td><a href="'+base_url+admin_dir+products_url+v.id_product+'?_token='+current_token+'#tab-step1" target="_blank">'+v.name+'</a></td></tr>';
                })
                $('#result').html(res);
            },
        });
    } else {
        $('#result').html('');        
    }
}
