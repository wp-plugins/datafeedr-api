(function($) {

    var sel = function(ptr, context) {
        if($.isFunction(ptr))
            return ptr.apply(context);
        return typeof ptr == "string" ? context.find(ptr) : ptr;
    };

    var filter = function(obj, search, opts) {
        var re = new RegExp(search.replace(/[.\\\\+*?\[\]\{\}\(\)-]/g, "\\$&"),
            opts.caseSensitive ? "" : "i");
        var ctx = sel(opts.element, obj);

        ctx.each(function() {
            var e = $(this);

            var box = opts.subject ? sel(opts.subject, e) : this;
            var val = box.text();

            if(!search.length) {
                e.show();
                if(opts.highlight) {
                    box.html(val);
                }
            } else if(val.match(re)) {
                e.show();
                if(opts.highlight) {
                    box.html(val.replace(re, opts.highlightRe));
                }
            } else {
                e.hide();
            }

        });

        if(opts.after)
            opts.after.apply(ctx);

    };

    $.fn.searchFilter = function(opts) {
        if(opts.highlight) {
            opts.highlightRe = $("<div>@</div>").wrapInner(opts.highlight).html().replace(/@/g, "$$&");
        }
        $(this).keyup(function() { filter(this, this.value, opts) });
        $(this).change(function() { filter(this, this.value, opts) });
    }


})(window.jQuery);
