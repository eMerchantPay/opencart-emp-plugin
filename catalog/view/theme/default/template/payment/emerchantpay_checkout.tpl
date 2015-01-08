<div class="buttons">
    <div class="pull-right">
        <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary" />
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#button-confirm').bind('click', function() {
            $.ajax({
                url: 'index.php?route=payment/emerchantpay_checkout/send',
                type: 'get',
                dataType: 'json',
                cache: false,
                beforeSend: function() {
                    $('#button-confirm').button('loading');
                },
                complete: function() {
                    $('#button-confirm').button('reset');
                },
                success: function(json) {
                    if (json['error']) {
                        $('.payment-errors').text(json['error']).fadeIn();
                    }

                    if (json['redirect']) {
                        location = json['redirect'];
                    }
                }
            });
        });
    });
</script>