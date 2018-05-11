<?php
/*
 * Copyright (C) 2018 emerchantpay Ltd.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @author      emerchantpay
 * @copyright   2018 emerchantpay Ltd.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2 (GPL-2.0)
 */
?>

<div class="alert alert-warning alert-checkout" hidden="hidden">
    <i class="fa fa-exclamation-circle"></i>
    <span class="alert-text"></span>
    <button type="button" class="close" data-hide="alert-checkout">&times;</button>
</div>

<div class="buttons">
    <div class="pull-right">
        <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary" />
    </div>
</div>

<script type="text/javascript">
    $(function(){
        $("[data-hide]").on("click", function(){
            $(this).closest("." + $(this).attr("data-hide")).fadeOut();
        });
    });

    $(document).ready(function() {
        $('#button-confirm').bind('click', function() {
            $.ajax({
                url: '<?php echo $button_target;?>',
                type: 'get',
                cache: false,
                dataType: 'json',
                beforeSend: function() {
                    $('#button-confirm').button('loading').prop('disabled', true);
                },
                success: function(json) {
                    if (json['error']) {
                        $('.alert-checkout .alert-text').text(json['error']);

                        $('.alert-checkout').fadeIn();

                        $('#button-confirm').button('reset').prop('disabled', false);
                    }

                    if (json['redirect']) {
                        location = json['redirect'];
                    }
                }
            });
        });
    });
</script>