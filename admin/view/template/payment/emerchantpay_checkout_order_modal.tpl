<div class="modal-dialog">
    <div class="modal-content">
        <?php if ($type == 'capture'): ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel"><?php echo $text_modal_title_capture; ?></h3>
            </div>
            <div class="modal-body">
                <div class="alert alert-notification alert-dismissible">
                    <i class="fa fa-info-circle"></i>
                    <span class="alert-text"></span>
                    <button type="button" class="close" data-hide="alert-notification">&times;</button>
                </div>
                <form class="form modal-form" action="<?php echo $url_action; ?>" method="post">
                    <input type="hidden" name="order_id" value="<?php echo $transaction['order_id']; ?>" />
                    <input type="hidden" name="reference_id" value="<?php echo $transaction['unique_id'];?>" />
                    <div class="form-group">
                        <label for="comment">Capture amount:</label>
                        <div class="input-group">
                            <div class="input-group-addon"><?php echo $transaction['currency']; ?></div>
                            <input type="text" class="form-control" name="amount" placeholder="Capture amount..." value="<?php echo $transaction['amount']; ?>" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="comment">Transaction Message (optional):</label>
                        <textarea class="form-control" rows="3" id="message" name="message"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                 <span class="form-loading">
                    <i class="fa fa-spinner fa-spin fa-lg"></i>
                </span>
                <span class="form-buttons">
                    <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo $text_button_close;?></button>
                    <button class="btn btn-submit btn-info btn-capture" value="partial"><?php echo $text_button_capture_partial;?></button>
                </span>
            </div>
        <?php endif;?>
        <?php if ($type == 'refund'): ?>
            <div class="modal-header">
                <button type="button" class="close" data-hide="alert-notification">×</button>
                <h3 id="myModalLabel"><?php echo $text_modal_title_refund; ?></h3>
            </div>
            <div class="modal-body">
                <div class="alert alert-notification alert-dismissible">
                    <i class="fa fa-info-circle"></i>
                    <span class="alert-text"></span>
                    <button type="button" class="close" data-hide="alert-notification">&times;</button>
                </div>
                <form class="form modal-form" action="<?php echo $url_action; ?>" method="post">
                    <input type="hidden" name="order_id" value="<?php echo $transaction['order_id']; ?>" />
                    <input type="hidden" name="reference_id" value="<?php echo $transaction['unique_id'];?>" />
                    <div class="form-group">
                        <label for="comment">Refund amount:</label>
                        <div class="input-group">
                            <div class="input-group-addon"><?php echo $transaction['currency']; ?></div>
                            <input type="text" class="form-control" name="amount" placeholder="Refund amount..." value="<?php echo $transaction['amount']; ?>" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="comment">Transaction Message (optional):</label>
                        <textarea class="form-control" rows="3" id="message" name="message"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                 <span class="form-loading">
                    <i class="fa fa-spinner fa-spin fa-lg"></i>
                </span>
                <span class="form-buttons">
                    <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo $text_button_close;?></button>
                    <button class="btn btn-submit btn-info btn-refund"><?php echo $text_button_refund_partial;?></button>
                </span>
            </div>
        <?php endif;?>
        <?php if ($type == 'void'): ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel"><?php echo $text_modal_title_void; ?></h3>
            </div>
            <div class="modal-body">
                <div class="alert alert-notification alert-dismissible">
                    <i class="fa fa-info-circle"></i>
                    <span class="alert-text"></span>
                    <button type="button" class="close" data-dismiss="alert-notification" aria-hidden="true">&times;</button>
                </div>
                <p class="text-center">
                    Are you sure you want to Void (cancel):
                    <br/>
                    <u><?php echo ucfirst($transaction['type']);?></u> transaction with UniqueId: <u><?php echo $transaction['unique_id']; ?></u>
                </p>
                <form class="form modal-form" action="<?php echo $url_action; ?>" method="post">
                    <input type="hidden" name="order_id" value="<?php echo $transaction['order_id']; ?>" />
                    <input type="hidden" name="reference_id" value="<?php echo $transaction['unique_id'];?>" />
                    <div class="form-group">
                        <label for="comment">Transaction Message (optional):</label>
                        <textarea class="form-control" rows="3" id="message" name="message"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <span class="form-loading">
                    <i class="fa fa-spinner fa-spin fa-lg"></i>
                </span>
                <span class="form-buttons">
                    <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo $text_button_close;?></button>
                    <button class="btn btn-submit btn-primary btn-void"><?php echo $text_button_void;?></button>
                </span>
            </div>
        <?php endif;?>
    </div>
</div>

<script type="text/javascript">
    $('.btn-submit').click(function() {
        modalForm = $(this).parents().eq(2).find('.modal-form');
        modalForm.submit();
    });

    $('form.modal-form').submit(function() {
        $modalForm = $(this);
        $modalDialog = $(this).parents().eq(4).find('.modal-dialog');

        $.ajax({
            type:   $modalForm.attr('method'),
            url:    $modalForm.attr('action'),
            data:   $modalForm.serialize(),
            beforeSend: function () {
                $modalDialog.find('.form-buttons').hide();
                $modalDialog.find('.form-loading').show();
            },
            complete: function() {
                $modalDialog.find('.form-loading').hide();
                $modalDialog.find('.form-buttons').show();
            },
            success: function (data) {
                if (data.text !== undefined && data.text.length > 0) {
                    $modalDialog.find('.alert-notification .alert-text').text(data.text);
                    $modalDialog.find('.alert-notification').removeClass('alert-danger').addClass('alert-info').slideDown();
                }

                setTimeout(function() {
                    $('.modal.fade').modal('hide');
                    location.reload();
                }, 2000);
            },
            error: function(xhr) {
                if (xhr.responseJSON !== undefined && xhr.responseJSON.text !== undefined && xhr.responseJSON.text.length > 0) {
                    error = xhr.responseJSON.text;
                }
                else {
                    error = xhr.statusText;
                }

                $modalDialog.find('.alert-notification .alert-text').text(error);
                $modalDialog.find('.alert-notification').removeClass('alert-info').addClass('alert-danger').slideDown();
            }
        });

        // prevent re-submitting
        return false;
    });

    $(function(){
        $("[data-hide]").on("click", function(){
            $("." + $(this).attr("data-hide")).slideUp();
        });
    });
</script>
<style>
    .alert-notification, .form-loading { display:none; }
</style>