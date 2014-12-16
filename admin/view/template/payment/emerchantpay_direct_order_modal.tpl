<div class="modal-dialog">
    <div class="modal-content">
        <?php if ($type == 'capture'): ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel"><?php echo $text_modal_title_capture; ?></h3>
            </div>
            <div class="modal-body">
                <div class="notification"></div>
                <form class="form modal-form" action="<?php echo $url_action; ?>" method="post">
                    <input type="hidden" name="order_id" value="<?php echo $transaction['order_id']; ?>" />
                    <input type="hidden" name="reference_id" value="<?php echo $transaction['unique_id'];?>" />
                    <div class="input-group">
                        <div class="input-group-addon"><?php echo $transaction['currency']; ?></div>
                        <input type="text" class="form-control" name="amount" placeholder="Capture amount..." value="<?php echo $transaction['amount']; ?>" />
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo $text_button_close;?></button>
                <button class="btn btn-submit btn-info" value="partial"><?php echo $text_button_capture_partial;?></button>
                <!--<button class="btn btn-submit btn-primary" value="full"><?php echo $text_button_capture_full;?></button>-->
            </div>
        <?php endif;?>
        <?php if ($type == 'refund'): ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel"><?php echo $text_modal_title_refund; ?></h3>
            </div>
            <div class="modal-body">
                <div class="notification"></div>
                <form class="form modal-form" action="<?php echo $url_action; ?>" method="post">
                    <input type="hidden" name="order_id" value="<?php echo $transaction['order_id']; ?>" />
                    <input type="hidden" name="reference_id" value="<?php echo $transaction['unique_id'];?>" />
                    <div class="input-group">
                        <div class="input-group-addon"><?php echo $transaction['currency']; ?></div>
                        <input type="text" class="form-control" name="amount" placeholder="Refund amount..." value="<?php echo $transaction['amount']; ?>" />
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo $text_button_close;?></button>
                <button class="btn btn-submit btn-info"><?php echo $text_button_refund_partial;?></button>
                <!--<button class="btn btn-submit btn-primary"><?php echo $text_button_refund_full;?></button>-->
            </div>
        <?php endif;?>
        <?php if ($type == 'void'): ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel"><?php echo $text_modal_title_void; ?></h3>
            </div>
            <div class="modal-body">
                <div class="notification"></div>
                <form class="form modal-form" action="<?php echo $url_action; ?>" method="post">
                    <input type="hidden" name="order_id" value="<?php echo $transaction['order_id']; ?>" />
                    <input type="hidden" name="reference_id" value="<?php echo $transaction['unique_id'];?>" />
                </form>
                <p class="text-center">Are you sure you want to Cancel (Void) this transaction?</p>
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo $text_button_close;?></button>
                <button class="btn btn-submit btn-primary void-confirm"><?php echo $text_button_void;?></button>
            </div>
        <?php endif;?>
    </div>
</div>

<script type="text/javascript">
    $('.btn-submit').click(function() {
        tForm = $(this).parents().eq(2).find('.modal-form');

        //tForm.append('<input type="hidden" name="submit" value="' + $(this).attr('value') + '" />');

        tForm.submit();
    });

    $('form.modal-form').submit(function() {
        $theForm = $(this);

        // send xhr request
        $.ajax({
            type:   $theForm.attr('method'),
            url:    $theForm.attr('action'),
            data:   $theForm.serialize(),
            beforeSend: function () {
                $('#button-void').hide();
                $('#img_loading_void').show();
            },
            success: function (data) {

                $('.notification').removeClass('error').text(data.text);

                if (data.error) {
                    $('.notification').addClass('error');
                }
            }
        });

        // prevent submitting again
        return false;
    });
</script>

<style>
    .notification {
        color: #fff;
        text-align:center;
        padding: 8px;
        width:100%;
        background: lightblue;
        margin:0 0 16px 0;
    }
    .notification.error {
        background: red;
    }
</style>