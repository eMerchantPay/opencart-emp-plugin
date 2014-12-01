<form class="form-horizontal emerchantpay-direct">
    <fieldset id="payment">
        <legend><?php echo $text_credit_card; ?></legend>
        <div class="cc-container">
            <?php //if ($error_payments) { ?>
            <div class="payment-errors">
                <?php echo $error_payments;?>
            </div>
            <?php //} ?>

            <div class="row">
                <div class="col-xs-6">
                    <div class="card-wrapper pull-right"></div>
                </div>
                <div class="col-xs-6 ">
                    <div class="form-container form-group active pull-left" style="width:350px;">
                        <input placeholder="<?php echo $entry_cc_number;?>" class="form-control" type="text" name="emerchantpay_direct-cc-number">
                        <input placeholder="<?php echo $entry_cc_owner;?>" class="form-control" type="text" name="emerchantpay_direct-cc-holder">
                        <input placeholder="<?php echo $entry_cc_expire_date;?>" class="form-control" type="text" name="emerchantpay_direct-cc-expiration">
                        <input placeholder="<?php echo $entry_cc_cvv2;?>" class="form-control" type="text" name="emerchantpay_direct-cc-cvv">
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
</form>
<div class="buttons">
    <div class="pull-right">
        <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary" />
    </div>
</div>
<style>
    .cc-container{/* width:100%;max-width:350px;margin:15px auto */}.cc-container .payment-errors{margin:16px auto;width:700px;padding:10px;border:1px solid #900;font-size:13px;background:#FCC;display:none}.cc-container form{margin:15px 0}.cc-container input{margin:10px auto;display:block}.cc-container .form-control{display:block;width:100%;height:34px;padding:6px 12px;font-size:14px;line-height:1.42857143;color:#555;background-color:#fff;background-image:none;border:1px solid #ccc;border-radius:4px;box-shadow:inset 0 1px 1px rgba(0,0,0,.075);-webkit-transition:border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;-o-transition:border-color ease-in-out .15s,box-shadow ease-in-out .15s;transition:border-color ease-in-out .15s,box-shadow ease-in-out .15s}.cc-container .form-control::-moz-placeholder{color:#999;opacity:1}.cc-container .form-control:-ms-input-placeholder{color:#999}.cc-container .form-control::-webkit-input-placeholder{color:#999}.cc-container .form-control[disabled],.form-control[readonly],fieldset[disabled] .form-control{cursor:not-allowed;background-color:#eee;opacity:1}.cc-container.amex .form-control:focus{border-color:#108168;outline:0}.cc-container.discover .form-control:focus{border-color:#86B8CF;outline:0}.cc-container.mastercard .form-control:focus{outline:0;border-color:#0061A8}.cc-container.visa .form-control:focus{border-color:#191278;outline:0}.card.safari.identified .back:before,.card.safari.identified .front:before{background-image:-webkit-repeating-linear-gradient(45deg,rgba(255,255,255,0) 1px,rgba(255,255,255,.03) 2px,rgba(255,255,255,.04) 3px,rgba(255,255,255,.05) 4px),-webkit-repeating-linear-gradient(315deg,rgba(255,255,255,.05) 1px,rgba(255,255,255,0) 2px,rgba(255,255,255,.04) 3px,rgba(255,255,255,.03) 4px),-webkit-repeating-linear-gradient(0deg,rgba(255,255,255,0) 1px,rgba(255,255,255,.03) 2px,rgba(255,255,255,.04) 3px,rgba(255,255,255,.05) 4px),-webkit-repeating-linear-gradient(240deg,rgba(255,255,255,0) 1px,rgba(255,255,255,.03) 2px,rgba(255,255,255,.04) 3px,rgba(255,255,255,.05) 4px),-webkit-linear-gradient(115deg,rgba(255,255,255,0) 50%,rgba(255,255,255,.2) 70%,rgba(255,255,255,0) 90%);background-image:repeating-linear-gradient(45deg,rgba(255,255,255,0) 1px,rgba(255,255,255,.03) 2px,rgba(255,255,255,.04) 3px,rgba(255,255,255,.05) 4px),repeating-linear-gradient(135deg,rgba(255,255,255,.05) 1px,rgba(255,255,255,0) 2px,rgba(255,255,255,.04) 3px,rgba(255,255,255,.03) 4px),repeating-linear-gradient(90deg,rgba(255,255,255,0) 1px,rgba(255,255,255,.03) 2px,rgba(255,255,255,.04) 3px,rgba(255,255,255,.05) 4px),repeating-linear-gradient(210deg,rgba(255,255,255,0) 1px,rgba(255,255,255,.03) 2px,rgba(255,255,255,.04) 3px,rgba(255,255,255,.05) 4px),linear-gradient(-25deg,rgba(255,255,255,0) 50%,rgba(255,255,255,.2) 70%,rgba(255,255,255,0) 90%)}.card.ie-10.flipped,.card.ie-11.flipped{-webkit-transform:0deg;-ms-transform:0deg;transform:0deg}.card.ie-10.flipped .back,.card.ie-10.flipped .front,.card.ie-11.flipped .back,.card.ie-11.flipped .front{-webkit-transform:rotateY(0deg);-ms-transform:rotateY(0deg);transform:rotateY(0deg)}.card.ie-10.flipped .back:after,.card.ie-11.flipped .back:after{left:18%}.card.ie-10.flipped .back .cvc,.card.ie-11.flipped .back .cvc{-webkit-transform:rotateY(180deg);-ms-transform:rotateY(180deg);transform:rotateY(180deg);left:5%}.card.ie-10.flipped .back .shiny,.card.ie-11.flipped .back .shiny{left:84%}.card.ie-10.flipped .back .shiny:after,.card.ie-11.flipped .back .shiny:after{left:-480%;-webkit-transform:rotateY(180deg);-ms-transform:rotateY(180deg);transform:rotateY(180deg)}.card.ie-10.amex .back,.card.ie-11.amex .back{display:none}.card-logo{height:36px;width:60px;font-style:italic}.card-logo,.card-logo:after,.card-logo:before{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}.card-logo.amex{text-transform:uppercase;font-size:4px;font-weight:700;color:#fff;background-image:-webkit-repeating-radial-gradient(center,circle,#FFF 1px,#999 2px);background-image:repeating-radial-gradient(circle at center,#FFF 1px,#999 2px);border:1px solid #EEE}.card-logo.amex:after,.card-logo.amex:before{width:28px;display:block;position:absolute;left:16px}.card-logo.amex:before{height:28px;content:"american";top:3px;text-align:left;padding-left:2px;padding-top:11px;background:#267AC3}.card-logo.amex:after{content:"express";bottom:11px;text-align:right;padding-right:2px}.card.amex.flipped{-webkit-transform:none;-ms-transform:none;transform:none}.card.amex.identified .back:before,.card.amex.identified .front:before{background-color:#108168}.card.amex.identified .front .card-logo.amex{opacity:1}.card.amex.identified .front .cvc{visibility:visible}.card.amex.identified .front:after{opacity:1}.card-logo.discover{background:#F60;color:#111;text-transform:uppercase;font-style:normal;font-weight:700;font-size:10px;text-align:center;overflow:hidden;z-index:1;padding-top:9px;letter-spacing:.03em;border:1px solid #EEE}.card-logo.discover:after,.card-logo.discover:before{content:" ";display:block;position:absolute}.card-logo.discover:before{background:#fff;width:200px;height:200px;border-radius:200px;bottom:-5%;right:-80%;z-index:-1}.card-logo.discover:after{width:8px;height:8px;border-radius:4px;top:10px;left:27px;background-color:#FFF;background-image:-webkit-radial-gradient(#FFF,#F60);background-image:radial-gradient(#FFF,#F60);content:"network";font-size:4px;line-height:24px;text-indent:-7px}.card .front .card-logo.discover{right:12%;top:18%}.card.discover.identified .back:before,.card.discover.identified .front:before{background-color:#86B8CF}.card.discover.identified .card-logo.discover{opacity:1}.card.discover.identified .front:after{-webkit-transition:400ms;transition:400ms;content:" ";display:block;background-color:#F60;background-image:-webkit-linear-gradient(#F60,#ffa166,#F60);background-image:-webkit-gradient(linear,left top,left bottom,from(#F60),color-stop(#ffa166),to(#F60));background-image:linear-gradient(#F60,#ffa166,#F60);height:50px;width:50px;border-radius:25px;position:absolute;left:100%;top:15%;margin-left:-25px;-webkit-box-shadow:inset 1px 1px 3px 1px rgba(0,0,0,.5);box-shadow:inset 1px 1px 3px 1px rgba(0,0,0,.5)}.card-logo.visa.visa{background:#fff;text-transform:uppercase;color:#16216A;text-align:center;font-weight:700;font-size:18px}.card-logo.visa.visa:after,.card-logo.visa.visa:before{content:" ";display:block;width:100%;height:25%}.card-logo.visa.visa:before{bottom:100%}.card-logo.visa.visa:after{top:100%}.card.visa.identified .back:before,.card.visa.identified .front:before{background-color:#191278}.card.visa.identified .card-logo.visa{opacity:1}.card-logo.mastercard{color:#fff;font-weight:700;text-align:center;font-size:9px;line-height:36px;z-index:1;text-shadow:1px 1px rgba(0,0,0,.6)}.card-logo.mastercard:after,.card-logo.mastercard:before{content:" ";display:block;width:36px;top:0;position:absolute;height:36px;border-radius:18px}.card-logo.mastercard:before{left:0;background:red;z-index:-1}.card-logo.mastercard:after{right:0;background:#FFAB00;z-index:-2}.card.mastercard.identified .back .card-logo.mastercard,.card.mastercard.identified .front .card-logo.mastercard{-webkit-box-shadow:none;box-shadow:none}.card.mastercard.identified .back:before,.card.mastercard.identified .front:before{background-color:#0061A8}.card.mastercard.identified .card-logo.mastercard{opacity:1}.card-container{-webkit-perspective:1000px;perspective:1000px;width:350px;max-width:100%;height:200px;margin:auto;z-index:1;position:relative}.card{font-family:"Helvetica Neue";line-height:1;position:relative;width:100%;height:100%;min-width:315px;border-radius:10px;-webkit-transform-style:preserve-3d;-ms-transform-style:preserve-3d;-o-transform-style:preserve-3d;transform-style:preserve-3d;-webkit-transition:all 400ms linear;transition:all 400ms linear}.card>*,.card>:after,.card>:before{-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;font-family:inherit}.card.flipped{-webkit-transform:rotateY(180deg);-ms-transform:rotateY(180deg);transform:rotateY(180deg)}.card .back,.card .front{-webkit-backface-visibility:hidden;backface-visibility:hidden;-webkit-transform-style:preserve-3d;-ms-transform-style:preserve-3d;-o-transform-style:preserve-3d;transform-style:preserve-3d;-webkit-transition:all 400ms linear;transition:all 400ms linear;width:100%;height:100%;position:absolute;top:0;left:0;overflow:hidden;border-radius:10px;background:#DDD}.card .back:before,.card .front:before{content:" ";display:block;position:absolute;width:100%;height:100%;top:0;left:0;opacity:0;border-radius:10px;-webkit-transition:all 400ms ease;transition:all 400ms ease}.card .back:after,.card .front:after{content:" ";display:block}.card .back .display,.card .front .display{color:#fff;font-weight:400;opacity:.5;-webkit-transition:opacity 400ms linear;transition:opacity 400ms linear}.card .back .display.focused,.card .front .display.focused{opacity:1;font-weight:700}.card .back .cvc,.card .front .cvc{font-family:"Bitstream Vera Sans Mono",Consolas,Courier,monospace;font-size:14px}.card .back .shiny,.card .front .shiny{width:50px;height:35px;border-radius:5px;background:#CCC;position:relative}.card .back .shiny:before,.card .front .shiny:before{content:" ";display:block;width:70%;height:60%;border-top-right-radius:5px;border-bottom-right-radius:5px;background:#d9d9d9;position:absolute;top:20%}.card .front .card-logo{position:absolute;opacity:0;right:5%;top:8%;-webkit-transition:400ms;transition:400ms}.card .front .lower{width:80%;position:absolute;left:10%;bottom:30px}@media only screen and (max-width:480px){.card .front .lower{width:90%;left:5%}}.card .front .lower .cvc{visibility:hidden;float:right;position:relative;bottom:5px}.card .front .lower .number{font-family:"Bitstream Vera Sans Mono",Consolas,Courier,monospace;font-size:24px;clear:both;margin-bottom:30px}.card .front .lower .expiry{font-family:"Bitstream Vera Sans Mono",Consolas,Courier,monospace;letter-spacing:0;position:relative;float:right;width:25%}.card .front .lower .expiry:after,.card .front .lower .expiry:before{font-family:"Helvetica Neue";font-weight:700;font-size:7px;white-space:pre;display:block;opacity:.5}.card .front .lower .expiry:before{content:attr(data-before);margin-bottom:2px;font-size:7px;text-transform:uppercase}.card .front .lower .expiry:after{position:absolute;content:attr(data-after);text-align:right;right:100%;margin-right:5px;margin-top:2px;bottom:0}.card .front .lower .name{text-transform:uppercase;font-family:"Bitstream Vera Sans Mono",Consolas,Courier,monospace;font-size:20px;max-height:45px;position:absolute;bottom:0;width:190px;-webkit-line-clamp:2;-webkit-box-orient:horizontal;overflow:hidden;text-overflow:ellipsis}.card .back{-webkit-transform:rotateY(180deg);-ms-transform:rotateY(180deg);transform:rotateY(180deg)}.card .back .bar{background-color:#444;background-image:-webkit-linear-gradient(#444,#333);background-image:-webkit-gradient(linear,left top,left bottom,from(#444),to(#333));background-image:linear-gradient(#444,#333);width:100%;height:20%;position:absolute;top:10%}.card .back:after{content:" ";display:block;background-color:#FFF;background-image:-webkit-linear-gradient(#FFF,#FFF);background-image:-webkit-gradient(linear,left top,left bottom,from(#FFF),to(#FFF));background-image:linear-gradient(#FFF,#FFF);width:80%;height:16%;position:absolute;top:40%;left:2%}.card .back .cvc{position:absolute;top:40%;left:85%;-webkit-transition-delay:600ms;transition-delay:600ms}.card .back .shiny{position:absolute;top:66%;left:2%}.card .back .shiny:after{content:"This card has been issued by Jesse Pollak and is licensed for anyone to use anywhere for free.\AIt comes with no warranty.\A For support issues, please visit: github.com/jessepollak/card.";position:absolute;left:120%;top:5%;color:#fff;font-size:7px;width:230px;opacity:.5}.card.identified{-webkit-box-shadow:0 0 20px rgba(0,0,0,.3);box-shadow:0 0 20px rgba(0,0,0,.3)}.card.identified .back,.card.identified .front{background-color:#000;background-color:rgba(0,0,0,.5)}.card.identified .back:before,.card.identified .front:before{-webkit-transition:all 400ms ease;transition:all 400ms ease;background-image:-webkit-repeating-linear-gradient(45deg,rgba(255,255,255,0) 1px,rgba(255,255,255,.03) 2px,rgba(255,255,255,.04) 3px,rgba(255,255,255,.05) 4px),-webkit-repeating-linear-gradient(315deg,rgba(255,255,255,.05) 1px,rgba(255,255,255,0) 2px,rgba(255,255,255,.04) 3px,rgba(255,255,255,.03) 4px),-webkit-repeating-linear-gradient(0deg,rgba(255,255,255,0) 1px,rgba(255,255,255,.03) 2px,rgba(255,255,255,.04) 3px,rgba(255,255,255,.05) 4px),-webkit-repeating-linear-gradient(240deg,rgba(255,255,255,0) 1px,rgba(255,255,255,.03) 2px,rgba(255,255,255,.04) 3px,rgba(255,255,255,.05) 4px),-webkit-repeating-radial-gradient(30% 30%,circle,rgba(255,255,255,0) 1px,rgba(255,255,255,.03) 2px,rgba(255,255,255,.04) 3px,rgba(255,255,255,.05) 4px),-webkit-repeating-radial-gradient(70% 70%,circle,rgba(255,255,255,0) 1px,rgba(255,255,255,.03) 2px,rgba(255,255,255,.04) 3px,rgba(255,255,255,.05) 4px),-webkit-repeating-radial-gradient(90% 20%,circle,rgba(255,255,255,0) 1px,rgba(255,255,255,.03) 2px,rgba(255,255,255,.04) 3px,rgba(255,255,255,.05) 4px),-webkit-repeating-radial-gradient(15% 80%,circle,rgba(255,255,255,0) 1px,rgba(255,255,255,.03) 2px,rgba(255,255,255,.04) 3px,rgba(255,255,255,.05) 4px),-webkit-linear-gradient(115deg,rgba(255,255,255,0) 50%,rgba(255,255,255,.2) 70%,rgba(255,255,255,0) 90%);background-image:repeating-linear-gradient(45deg,rgba(255,255,255,0) 1px,rgba(255,255,255,.03) 2px,rgba(255,255,255,.04) 3px,rgba(255,255,255,.05) 4px),repeating-linear-gradient(135deg,rgba(255,255,255,.05) 1px,rgba(255,255,255,0) 2px,rgba(255,255,255,.04) 3px,rgba(255,255,255,.03) 4px),repeating-linear-gradient(90deg,rgba(255,255,255,0) 1px,rgba(255,255,255,.03) 2px,rgba(255,255,255,.04) 3px,rgba(255,255,255,.05) 4px),repeating-linear-gradient(210deg,rgba(255,255,255,0) 1px,rgba(255,255,255,.03) 2px,rgba(255,255,255,.04) 3px,rgba(255,255,255,.05) 4px),repeating-radial-gradient(circle at 30% 30%,rgba(255,255,255,0) 1px,rgba(255,255,255,.03) 2px,rgba(255,255,255,.04) 3px,rgba(255,255,255,.05) 4px),repeating-radial-gradient(circle at 70% 70%,rgba(255,255,255,0) 1px,rgba(255,255,255,.03) 2px,rgba(255,255,255,.04) 3px,rgba(255,255,255,.05) 4px),repeating-radial-gradient(circle at 90% 20%,rgba(255,255,255,0) 1px,rgba(255,255,255,.03) 2px,rgba(255,255,255,.04) 3px,rgba(255,255,255,.05) 4px),repeating-radial-gradient(circle at 15% 80%,rgba(255,255,255,0) 1px,rgba(255,255,255,.03) 2px,rgba(255,255,255,.04) 3px,rgba(255,255,255,.05) 4px),linear-gradient(-25deg,rgba(255,255,255,0) 50%,rgba(255,255,255,.2) 70%,rgba(255,255,255,0) 90%);opacity:1}.card.identified .back .card-logo,.card.identified .front .card-logo{-webkit-box-shadow:0 0 0 2px rgba(255,255,255,.3);box-shadow:0 0 0 2px rgba(255,255,255,.3)}.card.identified.no-radial-gradient .back:before,.card.identified.no-radial-gradient .front:before{background-image:-webkit-repeating-linear-gradient(45deg,rgba(255,255,255,0) 1px,rgba(255,255,255,.03) 2px,rgba(255,255,255,.04) 3px,rgba(255,255,255,.05) 4px),-webkit-repeating-linear-gradient(315deg,rgba(255,255,255,.05) 1px,rgba(255,255,255,0) 2px,rgba(255,255,255,.04) 3px,rgba(255,255,255,.03) 4px),-webkit-repeating-linear-gradient(0deg,rgba(255,255,255,0) 1px,rgba(255,255,255,.03) 2px,rgba(255,255,255,.04) 3px,rgba(255,255,255,.05) 4px),-webkit-repeating-linear-gradient(240deg,rgba(255,255,255,0) 1px,rgba(255,255,255,.03) 2px,rgba(255,255,255,.04) 3px,rgba(255,255,255,.05) 4px),-webkit-linear-gradient(115deg,rgba(255,255,255,0) 50%,rgba(255,255,255,.2) 70%,rgba(255,255,255,0) 90%);background-image:repeating-linear-gradient(45deg,rgba(255,255,255,0) 1px,rgba(255,255,255,.03) 2px,rgba(255,255,255,.04) 3px,rgba(255,255,255,.05) 4px),repeating-linear-gradient(135deg,rgba(255,255,255,.05) 1px,rgba(255,255,255,0) 2px,rgba(255,255,255,.04) 3px,rgba(255,255,255,.03) 4px),repeating-linear-gradient(90deg,rgba(255,255,255,0) 1px,rgba(255,255,255,.03) 2px,rgba(255,255,255,.04) 3px,rgba(255,255,255,.05) 4px),repeating-linear-gradient(210deg,rgba(255,255,255,0) 1px,rgba(255,255,255,.03) 2px,rgba(255,255,255,.04) 3px,rgba(255,255,255,.05) 4px),linear-gradient(-25deg,rgba(255,255,255,0) 50%,rgba(255,255,255,.2) 70%,rgba(255,255,255,0) 90%)}
</style>
<script type="text/javascript">
    !function(e){if("object"==typeof exports)module.exports=e();else if("function"==typeof define&&define.amd)define(e);else{var t;"undefined"!=typeof window?t=window:"undefined"!=typeof global?t=global:"undefined"!=typeof self&&(t=self),t.card=e()}}(function(){var e,t,n;return function r(e,t,n){function i(o,u){if(!t[o]){if(!e[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(s)return s(o,!0);throw new Error("Cannot find module '"+o+"'")}var f=t[o]={exports:{}};e[o][0].call(f.exports,function(t){var n=e[o][1][t];return i(n?n:t)},f,f.exports,r,e,t,n)}return t[o].exports}var s=typeof require=="function"&&require;for(var o=0;o<n.length;o++)i(n[o]);return i}({1:[function(e,t,n){(function(){var e,t,n,r,i,s,o,u,a,f,l,c,h,p,d,v,m,g,y,b,w=[].slice,E=[].indexOf||function(e){for(var t=0,n=this.length;t<n;t++){if(t in this&&this[t]===e)return t}return-1};e=jQuery;e.payment={};e.payment.fn={};e.fn.payment=function(){var t,n;n=arguments[0],t=2<=arguments.length?w.call(arguments,1):[];return e.payment.fn[n].apply(this,t)};i=/(\d{1,4})/g;r=[{type:"visaelectron",pattern:/^4(026|17500|405|508|844|91[37])/,format:i,length:[16],cvcLength:[3],luhn:true},{type:"maestro",pattern:/^(5(018|0[23]|[68])|6(39|7))/,format:i,length:[12,13,14,15,16,17,18,19],cvcLength:[3],luhn:true},{type:"forbrugsforeningen",pattern:/^600/,format:i,length:[16],cvcLength:[3],luhn:true},{type:"dankort",pattern:/^5019/,format:i,length:[16],cvcLength:[3],luhn:true},{type:"visa",pattern:/^4/,format:i,length:[13,16],cvcLength:[3],luhn:true},{type:"mastercard",pattern:/^5[0-5]/,format:i,length:[16],cvcLength:[3],luhn:true},{type:"amex",pattern:/^3[47]/,format:/(\d{1,4})(\d{1,6})?(\d{1,5})?/,length:[15],cvcLength:[3,4],luhn:true},{type:"dinersclub",pattern:/^3[0689]/,format:i,length:[14],cvcLength:[3],luhn:true},{type:"discover",pattern:/^6([045]|22)/,format:i,length:[16],cvcLength:[3],luhn:true},{type:"unionpay",pattern:/^(62|88)/,format:i,length:[16,17,18,19],cvcLength:[3],luhn:false},{type:"jcb",pattern:/^35/,format:i,length:[16],cvcLength:[3],luhn:true}];t=function(e){var t,n,i;e=(e+"").replace(/\D/g,"");for(n=0,i=r.length;n<i;n++){t=r[n];if(t.pattern.test(e)){return t}}};n=function(e){var t,n,i;for(n=0,i=r.length;n<i;n++){t=r[n];if(t.type===e){return t}}};h=function(e){var t,n,r,i,s,o;r=true;i=0;n=(e+"").split("").reverse();for(s=0,o=n.length;s<o;s++){t=n[s];t=parseInt(t,10);if(r=!r){t*=2}if(t>9){t-=9}i+=t}return i%10===0};c=function(e){var t;if(e.prop("selectionStart")!=null&&e.prop("selectionStart")!==e.prop("selectionEnd")){return true}if(typeof document!=="undefined"&&document!==null?(t=document.selection)!=null?typeof t.createRange==="function"?t.createRange().text:void 0:void 0:void 0){return true}return false};p=function(t){return setTimeout(function(){var n,r;n=e(t.currentTarget);r=n.val();r=e.payment.formatCardNumber(r);return n.val(r)})};u=function(n){var r,i,s,o,u,a,f;s=String.fromCharCode(n.which);if(!/^\d+$/.test(s)){return}r=e(n.currentTarget);f=r.val();i=t(f+s);o=(f.replace(/\D/g,"")+s).length;a=16;if(i){a=i.length[i.length.length-1]}if(o>=a){return}if(r.prop("selectionStart")!=null&&r.prop("selectionStart")!==f.length){return}if(i&&i.type==="amex"){u=/^(\d{4}|\d{4}\s\d{6})$/}else{u=/(?:^|\s)(\d{4})$/}if(u.test(f)){n.preventDefault();return setTimeout(function(){return r.val(f+" "+s)})}else if(u.test(f+s)){n.preventDefault();return setTimeout(function(){return r.val(f+s+" ")})}};s=function(t){var n,r;n=e(t.currentTarget);r=n.val();if(t.which!==8){return}if(n.prop("selectionStart")!=null&&n.prop("selectionStart")!==r.length){return}if(/\d\s$/.test(r)){t.preventDefault();return setTimeout(function(){return n.val(r.replace(/\d\s$/,""))})}else if(/\s\d?$/.test(r)){t.preventDefault();return setTimeout(function(){return n.val(r.replace(/\s\d?$/,""))})}};d=function(t){return setTimeout(function(){var n,r;n=e(t.currentTarget);r=n.val();r=e.payment.formatExpiry(r);return n.val(r)})};a=function(t){var n,r,i;r=String.fromCharCode(t.which);if(!/^\d+$/.test(r)){return}n=e(t.currentTarget);i=n.val()+r;if(/^\d$/.test(i)&&i!=="0"&&i!=="1"){t.preventDefault();return setTimeout(function(){return n.val("0"+i+" / ")})}else if(/^\d\d$/.test(i)){t.preventDefault();return setTimeout(function(){return n.val(""+i+" / ")})}};f=function(t){var n,r,i;r=String.fromCharCode(t.which);if(!/^\d+$/.test(r)){return}n=e(t.currentTarget);i=n.val();if(/^\d\d$/.test(i)){return n.val(""+i+" / ")}};l=function(t){var n,r,i;i=String.fromCharCode(t.which);if(!(i==="/"||i===" ")){return}n=e(t.currentTarget);r=n.val();if(/^\d$/.test(r)&&r!=="0"){return n.val("0"+r+" / ")}};o=function(t){var n,r;n=e(t.currentTarget);r=n.val();if(t.which!==8){return}if(n.prop("selectionStart")!=null&&n.prop("selectionStart")!==r.length){return}if(/\s\/\s\d?$/.test(r)){t.preventDefault();return setTimeout(function(){return n.val(r.replace(/\s\/\s\d?$/,""))})}};y=function(e){var t;if(e.metaKey||e.ctrlKey){return true}if(e.which===32){return false}if(e.which===0){return true}if(e.which<33){return true}t=String.fromCharCode(e.which);return!!/[\d\s]/.test(t)};m=function(n){var r,i,s,o;r=e(n.currentTarget);s=String.fromCharCode(n.which);if(!/^\d+$/.test(s)){return}if(c(r)){return}o=(r.val()+s).replace(/\D/g,"");i=t(o);if(i){return o.length<=i.length[i.length.length-1]}else{return o.length<=16}};g=function(t){var n,r,i;n=e(t.currentTarget);r=String.fromCharCode(t.which);if(!/^\d+$/.test(r)){return}if(c(n)){return}i=n.val()+r;i=i.replace(/\D/g,"");if(i.length>6){return false}};v=function(t){var n,r,i;n=e(t.currentTarget);r=String.fromCharCode(t.which);if(!/^\d+$/.test(r)){return}if(c(n)){return}i=n.val()+r;return i.length<=4};b=function(t){var n,i,s,o,u;n=e(t.currentTarget);u=n.val();o=e.payment.cardType(u)||"unknown";if(!n.hasClass(o)){i=function(){var e,t,n;n=[];for(e=0,t=r.length;e<t;e++){s=r[e];n.push(s.type)}return n}();n.removeClass("unknown");n.removeClass(i.join(" "));n.addClass(o);n.toggleClass("identified",o!=="unknown");return n.trigger("payment.cardType",o)}};e.payment.fn.formatCardCVC=function(){this.payment("restrictNumeric");this.on("keypress",v);return this};e.payment.fn.formatCardExpiry=function(){this.payment("restrictNumeric");this.on("keypress",g);this.on("keypress",a);this.on("keypress",l);this.on("keypress",f);this.on("keydown",o);this.on("change",d);this.on("input",d);return this};e.payment.fn.formatCardNumber=function(){this.payment("restrictNumeric");this.on("keypress",m);this.on("keypress",u);this.on("keydown",s);this.on("keyup",b);this.on("paste",p);this.on("change",p);this.on("input",p);this.on("input",b);return this};e.payment.fn.restrictNumeric=function(){this.on("keypress",y);return this};e.payment.fn.cardExpiryVal=function(){return e.payment.cardExpiryVal(e(this).val())};e.payment.cardExpiryVal=function(e){var t,n,r,i;e=e.replace(/\s/g,"");i=e.split("/",2),t=i[0],r=i[1];if((r!=null?r.length:void 0)===2&&/^\d+$/.test(r)){n=(new Date).getFullYear();n=n.toString().slice(0,2);r=n+r}t=parseInt(t,10);r=parseInt(r,10);return{month:t,year:r}};e.payment.validateCardNumber=function(e){var n,r;e=(e+"").replace(/\s+|-/g,"");if(!/^\d+$/.test(e)){return false}n=t(e);if(!n){return false}return(r=e.length,E.call(n.length,r)>=0)&&(n.luhn===false||h(e))};e.payment.validateCardExpiry=function(t,n){var r,i,s;if(typeof t==="object"&&"month"in t){s=t,t=s.month,n=s.year}if(!(t&&n)){return false}t=e.trim(t);n=e.trim(n);if(!/^\d+$/.test(t)){return false}if(!/^\d+$/.test(n)){return false}if(!(1<=t&&t<=12)){return false}if(n.length===2){if(n<70){n="20"+n}else{n="19"+n}}if(n.length!==4){return false}i=new Date(n,t);r=new Date;i.setMonth(i.getMonth()-1);i.setMonth(i.getMonth()+1,1);return i>r};e.payment.validateCardCVC=function(t,r){var i,s;t=e.trim(t);if(!/^\d+$/.test(t)){return false}i=n(r);if(i!=null){return s=t.length,E.call(i.cvcLength,s)>=0}else{return t.length>=3&&t.length<=4}};e.payment.cardType=function(e){var n;if(!e){return null}return((n=t(e))!=null?n.type:void 0)||null};e.payment.formatCardNumber=function(n){var r,i,s,o;r=t(n);if(!r){return n}s=r.length[r.length.length-1];n=n.replace(/\D/g,"");n=n.slice(0,s);if(r.format.global){return(o=n.match(r.format))!=null?o.join(" "):void 0}else{i=r.format.exec(n);if(i==null){return}i.shift();i=e.grep(i,function(e){return e});return i.join(" ")}};e.payment.formatExpiry=function(e){var t,n,r,i;n=e.match(/^\D*(\d{1,2})(\D+)?(\d{1,4})?/);if(!n){return""}t=n[1]||"";r=n[2]||"";i=n[3]||"";if(i.length>0||r.length>0&&!/\ \/?\ ?/.test(r)){r=" / "}if(t.length===1&&t!=="0"&&t!=="1"){t="0"+t;r=" / "}return t+r+i}}).call(this)},{}],2:[function(e,t,n){var r,i,s=[].indexOf||function(e){for(var t=0,n=this.length;t<n;t++){if(t in this&&this[t]===e)return t}return-1},o=[].slice;e("jquery.payment");r=jQuery;r.card={};r.card.fn={};r.fn.card=function(e){return r.card.fn.construct.apply(this,e)};i=function(){function e(e,t){this.options=r.extend(true,{},this.defaults,t);r.extend(this.options.messages,r.card.messages);r.extend(this.options.values,r.card.values);this.$el=r(e);if(!this.options.container){console.log("Please provide a container");return}this.$container=r(this.options.container);if(this.options.updateContainer){this.$updateContainer=r(this.options.updateContainer)}this.render();this.attachHandlers();this.handleInitialValues()}e.prototype.cardTemplate='<div class="card-container">\n    <div class="card">\n        <div class="front">\n                <div class="card-logo visa">visa</div>\n                <div class="card-logo mastercard">MasterCard</div>\n                <div class="card-logo amex"></div>\n                <div class="card-logo discover">discover</div>\n            <div class="lower">\n                <div class="shiny"></div>\n                <div class="cvc display">{{cvc}}</div>\n                <div class="number display">{{number}}</div>\n                <div class="name display">{{name}}</div>\n                <div class="expiry display" data-before="{{monthYear}}" data-after="{{validDate}}">{{expiry}}</div>\n            </div>\n        </div>\n        <div class="back">\n            <div class="bar"></div>\n            <div class="cvc display">{{cvc}}</div>\n            <div class="shiny"></div>\n        </div>\n    </div>\n</div>';e.prototype.template=function(e,t){return e.replace(/\{\{(.*?)\}\}/g,function(e,n,r){return t[n]})};e.prototype.cardTypes=["maestro","dinersclub","laser","jcb","unionpay","discover","mastercard","amex","visa"];e.prototype.defaults={formatting:true,formSelectors:{numberInput:'input[name="number"]',expiryInput:'input[name="expiry"]',cvcInput:'input[name="cvc"]',nameInput:'input[name="name"]'},cardSelectors:{cardContainer:".card-container",card:".card",numberDisplay:".number",expiryDisplay:".expiry",cvcDisplay:".cvc",nameDisplay:".name"},messages:{validDate:"valid\nthru",monthYear:"month/year"},values:{number:"&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;",cvc:"&bull;&bull;&bull;",expiry:"&bull;&bull;/&bull;&bull;",name:"Full Name"},classes:{valid:"card-valid",invalid:"card-invalid"}};e.prototype.render=function(){var e,t;this.$container.append(this.template(this.cardTemplate,r.extend({},this.options.messages,this.options.values)));r.each(this.options.cardSelectors,function(e){return function(t,n){return e["$"+t]=e.$container.find(n)}}(this));r.each(this.options.formSelectors,function(e){return function(t,n){var i;if(e.options[t]){i=r(e.options[t])}else{i=e.$el.find(n)}if(!i.length){console.error("Card can't find a "+t+" in your form.")}return e["$"+t]=i}}(this));if(this.options.formatting){this.$numberInput.payment("formatCardNumber");this.$cvcInput.payment("formatCardCVC");if(this.$expiryInput.length===1){this.$expiryInput.payment("formatCardExpiry")}}if(this.options.width){e=parseInt(this.$cardContainer.css("width"));this.$cardContainer.css("transform","scale("+this.options.width/e+")")}if(typeof navigator!=="undefined"&&navigator!==null?navigator.userAgent:void 0){t=navigator.userAgent.toLowerCase();if(t.indexOf("safari")!==-1&&t.indexOf("chrome")===-1){this.$card.addClass("safari")}}if((new Function("/*@cc_on return @_jscript_version; @*/"))()){this.$card.addClass("ie-10")}if(/rv:11.0/i.test(navigator.userAgent)){return this.$card.addClass("ie-11")}};e.prototype.attachHandlers=function(){var e;this.$numberInput.bindVal(this.$numberDisplay,{fill:false,filters:this.validToggler("cardNumber")}).on("payment.cardType",this.handle("setCardType"));e=[function(e){return e.replace(/(\s+)/g,"")}];if(this.$expiryInput.length===1){e.push(this.validToggler("cardExpiry"))}this.$expiryInput.bindVal(this.$expiryDisplay,{join:function(e){if(e[0].length===2||e[1]){return"/"}else{return""}},filters:e});this.$cvcInput.bindVal(this.$cvcDisplay,{filters:this.validToggler("cardCVC")}).on("focus",this.handle("flipCard")).on("blur",this.handle("flipCard"));return this.$nameInput.bindVal(this.$nameDisplay,{fill:false,filters:this.validToggler("cardHolderName"),join:" "}).on("keydown",this.handle("captureName"))};e.prototype.handleInitialValues=function(){return r.each(this.options.formSelectors,function(e){return function(t,n){var r;r=e["$"+t];if(r.val()){r.trigger("paste");return setTimeout(function(){return r.trigger("keyup")})}}}(this))};e.prototype.handle=function(e){return function(t){return function(n){var i,s;i=r(n.currentTarget);s=Array.prototype.slice.call(arguments);s.unshift(i);return t.handlers[e].apply(t,s)}}(this)};e.prototype.validToggler=function(e){var t;if(e==="cardExpiry"){t=function(e){var t;t=r.payment.cardExpiryVal(e);return r.payment.validateCardExpiry(t.month,t.year)}}else if(e==="cardCVC"){t=function(e){return function(t){return r.payment.validateCardCVC(t,e.cardType)}}(this)}else if(e==="cardNumber"){t=function(e){return r.payment.validateCardNumber(e)}}else if(e==="cardHolderName"){t=function(e){return e!==""}}return function(e){return function(n,r,i){var s;s=t(n);e.toggleValidClass(r,s);e.toggleValidClass(i,s);return n}}(this)};e.prototype.toggleValidClass=function(e,t){e.toggleClass(this.options.classes.valid,t);return e.toggleClass(this.options.classes.invalid,!t)};e.prototype.handlers={setCardType:function(e,t,n){if(!this.$card.hasClass(n)){this.$card.removeClass("unknown");this.$card.removeClass(this.cardTypes.join(" "));this.$card.addClass(n);this.$card.toggleClass("identified",n!=="unknown");this.cardType=n}if(this.$updateContainer){if(!this.$updateContainer.hasClass(n)){this.$updateContainer.removeClass("unknown");this.$updateContainer.removeClass(this.cardTypes.join(" "));this.$updateContainer.addClass(n);return this.$updateContainer.toggleClass("identified",n!=="unknown")}}},flipCard:function(e,t){return this.$card.toggleClass("flipped")},captureName:function(e,t){var n,r,i;i=t.which||t.keyCode;r=[48,49,50,51,52,53,54,55,56,57,106,107,109,110,111,186,187,188,189,190,191,192,219,220,221,222];n=[189,109,190,110,222];if(r.indexOf(i)!==-1&&!(!t.shiftKey&&s.call(n,i)>=0)){return t.preventDefault()}}};r.fn.bindVal=function(e,t){var n,i,s,o,u;if(t==null){t={}}t.fill=t.fill||false;t.filters=t.filters||[];if(!(t.filters instanceof Array)){t.filters=[t.filters]}t.join=t.join||"";if(!(typeof t.join==="function")){s=t.join;t.join=function(){return s}}n=r(this);u=function(){var t,n,r;r=[];for(i=t=0,n=e.length;t<n;i=++t){o=e[i];r.push(e.eq(i).text())}return r}();n.on("focus",function(){return e.addClass("focused")});n.on("blur",function(){return e.removeClass("focused")});n.on("keyup change paste",function(s){var a,f,l,c,h,p,d,v,m,g;c=n.map(function(){return r(this).val()}).get();f=t.join(c);c=c.join(f);if(c===f){c=""}m=t.filters;for(h=0,d=m.length;h<d;h++){a=m[h];c=a(c,n,e)}g=[];for(i=p=0,v=e.length;p<v;i=++p){o=e[i];if(t.fill){l=c+u[i].substring(c.length)}else{l=c||u[i]}g.push(e.eq(i).text(l))}return g});return n};return e}();r.fn.extend({card:function(){var e,t;t=arguments[0],e=2<=arguments.length?o.call(arguments,1):[];return this.each(function(){var n,s;n=r(this);s=n.data("card");if(!s){n.data("card",s=new i(this,t))}if(typeof t==="string"){return s[t].apply(s,e)}})}})},{"jquery.payment":1}]},{},[2])(2)});
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('form.emerchantpay-direct').card({
            container       : $('.card-wrapper'),
            updateContainer : $('.cc-container'),
            numberInput     : 'input[name="emerchantpay_direct-cc-number"]',
            nameInput       : 'input[name="emerchantpay_direct-cc-holder"]',
            expiryInput     : 'input[name="emerchantpay_direct-cc-expiration"]',
            cvcInput        : 'input[name="emerchantpay_direct-cc-cvv"]'
        });
    });
    $('#button-confirm').bind('click', function() {
        $.ajax({
            url: 'index.php?route=payment/emerchantpay_direct/send',
            type: 'post',
            data: $('.emerchantpay-direct').serialize(),
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
</script>