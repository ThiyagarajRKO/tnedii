(function ($) {

    $.fn.dialogue = function (options) {
        var defaults = {
            title: "", content: $("<p />"),
            closeIcon: false, id: 'dynamicModal', open: function () { }, buttons: []
        };
        var settings = $.extend(true, {}, defaults, options);

        // create the DOM structure
        var $modal = $("<div />").attr("id", settings.id).attr("role", "dialog").addClass("modal fade dynamicModal")
            .append($("<div />").addClass("modal-dialog")
                .append($("<div />").addClass("modal-content")
                    .append($("<div />").addClass("modal-header bg-info")
                        .append($("<h4 />").addClass("modal-title").text(settings.title)))
                    .append($("<div />").addClass("modal-body")
                        .append(settings.content))
                    .append($("<div />").addClass("modal-footer")
                    )
                )
            );
        $modal.shown = false;
        $modal.dismiss = function () {
            // loop until its shown
            // this is only because you can do $.fn.alert("utils.js makes this so easy!").dismiss(); in which case it will try to remove it before its finished rendering
            if (!$modal.shown) {
                window.setTimeout(function () {
                    $modal.dismiss();
                }, 50);
                return;
            }

            // hide the dialogue
            $modal.modal("hide");
            // remove the blanking
            $modal.prev().remove();
            // remove the dialogue
            $modal.empty().remove();

            $("body").removeClass("modal-open");
        }

        if (settings.closeIcon)
            $modal.find(".modal-header").append($("<button />").attr("type", "button").addClass("close").html("&times;").click(function () { $modal.dismiss() }));

        // add the buttons
        var $footer = $modal.find(".modal-footer");
        for (var i = 0; i < settings.buttons.length; i++) {
            (function (btn) {
                let btnClass = (btn.class) ? btn.class : "btn btn-default";
                $footer.prepend($("<button />").addClass(btnClass)
                    .attr("id", btn.id)
                    .attr("type", "button")
                    .text(btn.text)
                    .click(function () {
                        btn.click($modal)
                    }))
            })(settings.buttons[i]);
        }

        settings.open($modal);

        $modal.on('shown.bs.modal', function (e) {
            $modal.shown = true;
        });
        // show the dialogue
        $modal.modal("show");

        return $modal;
    };
})(jQuery);

function doModal(data) {
    let title = data.title;

    return $.fn.dialogue({
        title: title,
        content: data.msg,
        closeIcon: true,
        buttons: [
            {
                text: "Close", class: "float left btn btn-warning", id: 'dynamicModal', click: function ($modal) {
                    $modal.dismiss();
                },
            }
        ]
    });
}

var idleTimeChecker = null;
function checkSession() {
    $.ajax({
        url: '/session/idleTimeCheck',
        type: 'post',
        '_token': '{!! csrf_token() !!}',
        success: response => {
            let data = response.data || {};
            if (!$.isEmptyObject(data)) {
                if (data.stopIdleCheck) {
                    clearInterval(idleTimeChecker);
                } else if (data.idleWarningDisplayed) {
                    let modalConfig = {
                        title: "Warning!",
                        msg: response.message
                    }
                    doModal(modalConfig);
                } else if (data.logoutWarningDisplayed) {
                    clearInterval(idleTimeChecker);
                    location.href = '/admin/logout';
                }
            }
        },
        error: data => {
        }
    });
}
if(idleSessionCheckConfig) {
    idleTimeChecker = setInterval(checkSession, (parseFloat(idleSessionCheckConfig) * 1000 *60));
}

