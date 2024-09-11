
        
(() => {
    function e(e, t) {
        for (var r = 0; r < t.length; r++) {
            var n = t[r];
            (n.enumerable = n.enumerable || !1), (n.configurable = !0), "value" in n && (n.writable = !0), Object.defineProperty(e, n.key, n);
        }
    }
    var t = (function () {
        function t() {
            !(function (e, t) {
                if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function");
            })(this, t);
        }
        var r, n, i;
        return (
            (r = t),
            (n = [
                {
                    key: "loadData",
                    value: function (e) {
                        let filterData = [];
                            $('.filter_list .form-filter').each(function() {
                                filterData.push({
                                    'column': $(this).find('.filter-column-key').val(),
                                    'operator': $(this).find('.filter-column-operator').val(),
                                    'value': $(this).find('.filter-column-value').val(),
                                });
                            })
                        $.ajax({
                            type: "GET",
                            url: $(".filter-data-url").val(),
                            data: { class: $(".filter-data-class").val(), key: e.val(), value: e.closest(".filter-item").find(".filter-column-value").val(),
                            filterData: filterData },
                            success: function (t) {
                                var r = $.map(t.data, function (e, t) {
                                    return { id: t, name: e };
                                });
                                e.closest(".filter-item").find(".filter-column-value-wrap").html(t.html);
                                var n = e.closest(".filter-item").find(".filter-column-value");
                                n.length && "text" === n.prop("type") && (n.typeahead({ source: r }), (n.data("typeahead").source = r)), Impiger.initResources();
                            },
                            error: function (e) {
                                Impiger.handleError(e);
                            },
                        });
                    },
                },
                {
                    key: "init",
                    value: function () {
                        var e = this;
                        $.each($(".filter-items-wrap .filter-column-key"), function (t, r) {
                            $(r).val() && e.loadData($(r));
                        }),
                            $(document).on("change", ".filter-column-key", function (t) {
                                $(t.currentTarget).closest(".filter-item").find(".filter-column-value").val("");
                                e.loadData($(t.currentTarget));
                            }),
                            $(document).on("click", ".btn-reset-filter-item", function (e) {
                                e.preventDefault();
                                var t = $(e.currentTarget);
                                t.closest(".filter-item").find(".filter-column-key").val("").trigger("change"),
                                    t.closest(".filter-item").find(".filter-column-operator").val("="),
                                    t.closest(".filter-item").find(".filter-column-value").val("");
                            }),
                            $(document).on("click", ".add-more-filter", function () {
                                var t = $(document).find(".sample-filter-item-wrap").html();
                                $(document).find(".filter-items-wrap").append(t.replace("<script>", "").replace("<\\/script>", "")), Impiger.initResources();
                                var r = $(document).find(".filter-items-wrap .filter-item:last-child").find(".filter-column-key");
                                $(r).val() && e.loadData(r);
                            }),
                            $(document).on("click", ".btn-remove-filter-item", function (e) {
                                e.preventDefault(), $(e.currentTarget).closest(".filter-item").remove();
                            });
                    }
                },
            ]) && e(r.prototype, n),
            i && e(r, i),
            t
        );
    })();
    $(document).ready(function () {
        new t().init();
    });
})();
