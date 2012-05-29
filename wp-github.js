(function($) {
  $(window).ready(function() {
    // constants
    var gh_plugin_root = 'wp-content/plugins/wp-github-plugin/';

    /**
     * process widget function
     */

    function processWidget (widget) {
      // ph DOM element
      var ph = widget.find(".placeholder");

      // retrieving data from DOM elements
      var params = {
              user: widget.data('user')
            , repo: widget.data('repo')
            , type: widget.data('type')
          }

      function getData (params, fn) {
        $.ajax({
            url: gh_plugin_root + "wp-github-ajax.php"
          , data: params
          , success: fn
          , error: function (obj, type, desc) {
              console.log(desc);
            }
        });
      }

      function markUp (data) {
        var html = '<ul>';

        for (var i = 0; i < data.length; i++) {
          var user = data[i].user || {}
            , full = data[i].full || {}

          html += '<li' + (i == (data.length - 1) ? ' class="last"' : '') + '>'
                + '<a href="' + full.html_url + '" target="_blank" class="wp-github-user">'
                  + '<img src="' + user.avatar_url + '" />'
                + '</a>'

                + '<div class="profile">'
                  + '<div class="user">' + user.login + '</div>'
                  + (full.email ? ('<div class="contact">' + full.email + '</div>') : '')
                  + (full.blog ? ('<div class="blog">' + full.blog + '</div>') : '')
                + '</div>'
              + '</li>';
        }
        html += '</ul>';

        return html;
      }

      getData(params, function (data) {
        ph.html(markUp(data));
      });
    }

    // widget DOM element
    var widgets = $('.wp-github-widget');

    if (!widgets.length) return;

    $.each(widgets, function(i, widget) {
      processWidget($(widget));
    });

  });
})(jQuery);
