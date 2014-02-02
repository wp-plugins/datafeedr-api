jQuery(function($) {

    $.fn.sort = function(opts) {

        return $(this).each(function() {
            var box = $(this), ls = [];

            box.children().each(function() {
                var p = opts.by ? $(this).find(opts.by) : this;
                var t = $(p).text();
                if(!opts.case)
                    t = t.toUpperCase();
                ls.push([t, this]);
            });
            ls.sort(function(a, b) {
                var n = a[0] > b[0] ? 1 : a[0] < b[0] ? -1 : 0;
                return opts.reverse ? -n : n;
            });
            $.each(ls, function() {
                box.append(this[1]);
            });
            return this;
        });

    };

    // Networks Settings Page
    $("#dfrapi_networks .group").click(function (e) {
        $(".networks", this).toggle(500);
        e.preventDefault();
    });

    $("#dfrapi_networks .group .networks").click(function(e) {
        e.stopPropagation();
    });

    $("#dfrapi_networks .group .networks .network .check_network").change(function(e) {
        var parent = $(this).parent('td').parent('tr');
        var id  = parent.attr('id');
        var nid = parent.attr('nid');
        var key = parent.attr('key');
        var aid = parent.attr('aid');
        var tid = parent.attr('tid');
        if ($(this).prop('checked')) {
            $("#" + id + " .aid_input").html('<input type="text" name="'+key+'[ids]['+nid+'][aid]" value="'+aid+'" class="aid_input_field" />');
            $("#" + id + " .tid_input").html('<input type="text" name="'+key+'[ids]['+nid+'][tid]" value="'+tid+'" class="tid_input_field" />');
        } else {
            $("#" + id + " .aid_input").html('');
            $("#" + id + " .tid_input").html('');
        }
    }).change();

    // Merchants Settings Page
    var refreshSearch = function(e) {
        $(e).closest(".network").find(".merchant_actions input").change();
    }

    $("#dfrapi_merchants .network .meta").click(function () {
        $(this).parent().find(".merchants").slideToggle(500);
    });

    $("#dfrapi_merchants .hide_empty_merchants").click(function () {
        var n = $(this).closest(".network");
        n.find(".no_products").addClass("hidden");
        n.find(".show_empty_merchants").show();
        $(this).hide();
        refreshSearch(this);
        return false;
    });

    $("#dfrapi_merchants .show_empty_merchants").click(function () {
        var n = $(this).closest(".network");
        n.find(".no_products").removeClass("hidden");
        n.find(".hide_empty_merchants").show();
        $(this).hide();
        refreshSearch(this);
        return false;
    });

    var selMerchant = function(elem, select, merchant) {
        var left  = $(elem).closest(".network").find(".dfrapi_pane_left  .dfrapi_pane_content");
        var right = $(elem).closest(".network").find(".dfrapi_pane_right .dfrapi_pane_content");

        var src = select ? left : right;
        var dst = select ? right : left;

        var m = merchant ? $(merchant)[0] : null;

        src.find(".merchant:visible").each(function() {
            if(!m || this == m) {
                dst.append(this);
            }
        });
        dst.sort({by: '.merchant_name'});
        refreshSearch(elem);

        var ids = [];
        $(".dfrapi_pane_right .merchant").each(function() {
            ids.push(this.id.split("_").pop());
        });
        $("#ids").val(ids.sort().join(","));
    };

    $('#dfrapi_merchants .add_all').click(function () {
        selMerchant(this, true, null);
        return false;
    });

    $('#dfrapi_merchants .remove_all').click(function () {
        selMerchant(this, false, null);
        return false;
    });

    $("#dfrapi_merchants .merchant").click(function() {
        var isLeft = $(this).closest(".dfrapi_pane_left").length;
        selMerchant(this, isLeft, this);
        return false;
    });


    $("#dfrapi_merchants .merchant_actions input").each(function() {
        $(this).searchFilter({
            element: function() { return $(this).closest(".network").find(".merchant") },
            subject: ".merchant_name",
            highlight: "<span style='background: yellow'>",
            after: function() { $(this).parent().find(".hidden").removeAttr("style") }
        });
    });

    $("#dfrapi_merchants .merchant_actions .reset_search").click(function() {
        $(this).parent().find("input").val("").change();
        return false;
    });


    if($("#dfrapi_networks").length || $("#dfrapi_merchants").length) {
        var initState = $("form").serialize();
        window.onbeforeunload = function() {
            var currState = $("form").serialize();
            if(currState != initState)
                return $("#dfr_unload_message").text();
        };
        $("form").on('submit', function() {
            window.onbeforeunload = null;
        })
    }

});


