(()=>{function e(e,n){for(var o=0;o<n.length;o++){var r=n[o];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}var n=function(){function n(){!function(e,n){if(!(e instanceof n))throw new TypeError("Cannot call a class as a function")}(this,n)}var o,r;return o=n,(r=[{key:"init",value:function(){$("#plugin-list").on("click",".btn-trigger-change-status",(function(e){e.preventDefault();var n=$(e.currentTarget);n.addClass("button-loading"),$.ajax({url:route("plugins.change.status",{name:n.data("plugin")}),type:"PUT",success:function(e){e.error?Impiger.showError(e.message):(Impiger.showSuccess(e.message),$("#plugin-list #app-"+n.data("plugin")).load(window.location.href+" #plugin-list #app-"+n.data("plugin")+" > *"),window.location.reload()),n.removeClass("button-loading")},error:function(e){Impiger.handleError(e),n.removeClass("button-loading")}})})),$(document).on("click",".btn-trigger-remove-plugin",(function(e){e.preventDefault(),$("#confirm-remove-plugin-button").data("plugin",$(e.currentTarget).data("plugin")),$("#remove-plugin-modal").modal("show")})),$(document).on("click","#confirm-remove-plugin-button",(function(e){e.preventDefault();var n=$(e.currentTarget);n.addClass("button-loading"),$.ajax({url:route("plugins.remove",{plugin:n.data("plugin")}),type:"DELETE",success:function(e){e.error?Impiger.showError(e.message):(Impiger.showSuccess(e.message),window.location.reload()),n.removeClass("button-loading"),$("#remove-plugin-modal").modal("hide")},error:function(e){Impiger.handleError(e),n.removeClass("button-loading"),$("#remove-plugin-modal").modal("hide")}})}))}}])&&e(o.prototype,r),n}();$(document).ready((function(){(new n).init()}))})();
