(function($) {
    $.fn.stats = function(o) {
        var def = {
            data: [],
            source: null,
            max: 0,
            height: null,
            width: null
        };

        var d = $.extend(def, o);

        (d.width == null) ? d.width = $(this).width() : d.width = d.width;
        (d.height == null) ? d.height = $(this).height() : d.height = d.height;

        if(d.data == 0 && d.source !== null) {
            $(d.source).each(function(i, e) {
                var cell = $(e);
                if(cell.find("td").length == 2) {
                    var label  = cell.find("td").eq(0).text();
                    var number = cell.find("td").eq(1).text() * 1;
                    d.data.push({label: label, number: number});
                } else {
                    var number = cell.find("td").eq(0).text() * 1;
                    d.data.push(number);
                }
                (number > d.max) ? d.max = number : d.max = d.max;
            });
        } else {
            var last   = d.data.length - 1;
            var to_sort = d.data.slice(0);
            to_sort.sort();
            d.max = to_sort[last];
        }

        var n   = d.data.length;
        var width = (d.width - (4 * n)) / n;
        var graph = $("<div/>");

        $.each(d.data, function(i, v) {
            if(typeof(v) !== "object") {
                t = v;
                v = {label: t, number: t};
            } else {
                v = v;
            }
            var percent = ((v.number / d.max) * (d.height - 15)) + 15;
            var label = $("<a/>").attr("href", "#").text(v.label);
            var bar = $("<span/>").css({"height": percent, "width": width});
            bar.append(label);
            bar.appendTo(graph);
        });

        return this.each(function() {
            $(this).addClass("stats-graph");
            $(this).css({"width": d.width, "height": d.height});
            $(this).html(graph.html());
        });
    };
})(jQuery);