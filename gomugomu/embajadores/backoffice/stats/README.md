#jQuery Stats Graph

![](http://forrst-production.s3.amazonaws.com/posts/snaps/66994/original.png?1299964214)

jQuery Stats Graph takes data from an array or a table and turns it into a nifty graph.

##Usage

Upon including the JavaScript of the plugin and of course of jQuery itself on your page, the plugin can fill up any container div with two types of information. For one, you can give it an array directly:

    $("#some-container-div").stats({
        data: [4, 8, 15]
    });

It can be a simple numbers array, or it can be an array of objects, each object going like `{label: "Something", number:23}`. Alternatively, you can make it fetch the results from a table if you specify a selector (with `tr`!):

    $("#some-container-div").stats({
        source: "table.some-table tr"
    });

##Data

This is how that source table should look if you decide to fetch the data that way:

    <table class="some-table">
        <tr>
            <td>4</td>
        </tr>
        <tr>
            <td>8</td>
        </tr>
        <tr>
            <td>15</td>
        </tr>
    </table>

Or in case with labels:

    <table class="some-table">
        <tr>
            <td>A label</td>
            <td>4</td>
        </tr>
        <tr>
            <td>Oh look another one</td>
            <td>8</td>
        </tr>
        <tr>
            <td>Yet again a label</td>
            <td>15</td>
        </tr>
    </table>

##Markup

This is what the plugin creates:

    <div class="stats-graph">
        <span><a href="#">4</a></span>
        <span><a href="#">8</a></span>
        <span><a href="#">15</a></span>
    </div>

The class is added to your container div automatically when it is filled. The span elements get their width and height inside the plugin. The contents of the a is either the number itself, or, if it is provided, the label.

*Remember that the looks of the visualized graph is mostly thanks to the CSS, which should be included as well. You can edit it to match your site's style.*

##Credits

Copyright (c) 2011 Gargron