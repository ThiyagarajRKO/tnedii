(()=>{!function(t){t.fn.dialogue=function(n){var e={title:"",content:t("<p />"),closeIcon:!1,id:"dynamicModal",open:function(){},buttons:[]},o=t.extend(!0,{},e,n),a=t("<div />").attr("id",o.id).attr("role","dialog").addClass("modal fade dynamicModal").append(t("<div />").addClass("modal-dialog").append(t("<div />").addClass("modal-content").append(t("<div />").addClass("modal-header bg-info").append(t("<h4 />").addClass("modal-title").text(o.title))).append(t("<div />").addClass("modal-body").append(o.content)).append(t("<div />").addClass("modal-footer"))));a.shown=!1,a.dismiss=function(){a.shown?(a.modal("hide"),a.prev().remove(),a.empty().remove(),t("body").removeClass("modal-open")):window.setTimeout((function(){a.dismiss()}),50)},o.closeIcon&&a.find(".modal-header").append(t("<button />").attr("type","button").addClass("close").html("&times;").click((function(){a.dismiss()})));for(var d=a.find(".modal-footer"),i=0;i<o.buttons.length;i++)!function(n){var e=n.class?n.class:"btn btn-default";d.prepend(t("<button />").addClass(e).attr("id",n.id).attr("type","button").text(n.text).click((function(){n.click(a)})))}(o.buttons[i]);return o.open(a),a.on("shown.bs.modal",(function(t){a.shown=!0})),a.modal("show"),a}}(jQuery);var t=null;idleSessionCheckConfig&&(t=setInterval((function(){$.ajax({url:"/session/idleTimeCheck",type:"post",_token:"{!! csrf_token() !!}",success:function(n){var e=n.data||{};$.isEmptyObject(e)||(e.stopIdleCheck?clearInterval(t):e.idleWarningDisplayed?function(t){var n=t.title;$.fn.dialogue({title:n,content:t.msg,closeIcon:!0,buttons:[{text:"Close",class:"float left btn btn-warning",id:"dynamicModal",click:function(t){t.dismiss()}}]})}({title:"Warning!",msg:n.message}):e.logoutWarningDisplayed&&(clearInterval(t),location.href="/admin/logout"))},error:function(t){}})}),1e3*parseFloat(idleSessionCheckConfig)*60))})();