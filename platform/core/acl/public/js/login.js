(()=>{function e(e,r){for(var o=0;o<r.length;o++){var n=r[o];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}var r=function(){function r(){!function(e,r){if(!(e instanceof r))throw new TypeError("Cannot call a class as a function")}(this,r)}var o,n;return o=r,(n=[{key:"handleLogin",value:function(){$(".login-form").validate({errorElement:"span",errorClass:"help-block",focusInvalid:!1,rules:{username:{required:!0},password:{required:!0},remember:{required:!1}},messages:{username:{required:"Email/Username is required."},password:{required:"Password is required."}},invalidHandler:function(){$(".alert-danger",$(".login-form")).show()},highlight:function(e){$(e).closest(".form-group").addClass("has-error")},success:function(e){e.closest(".form-group").removeClass("has-error"),e.remove()},errorPlacement:function(e,r){e.insertAfter(r.closest(".form-control"))},submitHandler:function(e){e.submit()}}),$(".login-form input").keypress((function(e){if(13===e.which)return $(".login-form").validate().form()&&$(".login-form").submit(),!1}))}},{key:"handleForgetPassword",value:function(){$(".forget-form").validate({errorElement:"span",errorClass:"help-block",focusInvalid:!1,ignore:"",rules:{email:{required:!0,email:!0}},messages:{email:{required:"Email is required."}},invalidHandler:function(){$(".alert-danger",$(".forget-form")).show()},highlight:function(e){$(e).closest(".form-group").addClass("has-error")},success:function(e){e.closest(".form-group").removeClass("has-error"),e.remove()},errorPlacement:function(e,r){e.insertAfter(r.closest(".form-control"))},submitHandler:function(e){e.submit()}}),$(".forget-form input").keypress((function(e){if(13===e.which)return $(".forget-form").validate().form()&&$(".forget-form").submit(),!1}))}},{key:"init",value:function(){this.handleLogin(),this.handleForgetPassword()}}])&&e(o.prototype,n),r}();$(document).ready((function(){(new r).init()}))})();
