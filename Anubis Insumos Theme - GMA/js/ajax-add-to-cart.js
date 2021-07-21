try {
    (function($){
        $(document).on('click', '.single_add_to_cart_button', function(e){
            e.preventDefault();
            
            var $thisbutton = $(this),
                $form = $thisbutton.closest('form.cart'),
                id = $thisbutton.val(),
                product_qty = $form.find('input[name=quantity]').val() || 1,
                product_id = $form.find('input[name=product_id]').val() || id,
                variation_id = $form.find('input[name=variation_id]').val() || 0;
            
            var data = {
                action: 'woocommerce_ajax_add_to_cart',
                product_id: product_id,
                product_sku: '',
                quantity: product_qty,
                variation_id: variation_id,
                atributeValues: {}
            };
            
            //Get <select> values.
            const selects = $form.find('select');
            const selectsCount = selects.length;
            for(let i = 0; i < selectsCount; i++){
                const name = selects[i].name;
                const value = selects[i].value;
                data.atributeValues[name] = value;
            }
            
            if($thisbutton.hasClass("disabled")){
                console.log("Debe seleccionar una opción.");
                return;
            }
            $(document.body).trigger('adding_to_cart', [$thisbutton, data]);
            
            $.ajax({
                type: 'post',
                url: wc_add_to_cart_params.ajax_url,
                data: data,
                beforeSend: function(response){
                    $thisbutton.removeClass('added').addClass('loading');
                },
                complete: function(response){
                    $thisbutton.addClass('added').removeClass('loading');
                },
                success: function(response){
                    if(response.error && response.product_url){
                        alert("Lo sentimos, ha ocurrido un error.");
                        console.log(response.product_url);
                        return;
                    }else{
                        $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $thisbutton]);
                    }
                },
            });
            return false;
        });
    })(jQuery);
}catch (error) {
    console.warn(error);
}