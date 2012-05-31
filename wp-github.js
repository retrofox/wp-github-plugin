(function($) {
  $(window).ready(function() {
    /**
     * process widget function
     */

    function processWidget (widget) {
      // retrieving data from DOM elements
      var params = {
              user: widget.data('user')
            , repo: widget.data('repo')
          }

      var plugin_path = widget.data('plugin-path');

      function getData (params, fn) {
        $.ajax({
            url: plugin_path + "/wp-github-ajax.php"
          , data: params
          , success: fn
        });
      }

      var markUp = {};

      /**
       * Contributors markUP
       */

      markUp.contributors  = function (data) {
        var html = '<ul>';
        var users = data.users;

        for (var i = 0; i < users.length; i++) {
          var user = users[i].user || {}
            , full = users[i].full || {}

          html += '<li' + (i == (users.length - 1) ? ' class="last"' : '') + '>'
                + '<a href="' + full.html_url + '" target="_blank" class="wp-github-user">'
                  + '<img src="' + user.avatar_url + '" />'
                + '</a>'

                + '<div class="profile">'
                  + '<div class="user">'
                    + '<a href="' + full.html_url + '" target="_blank">'
                      + user.login
                    + '</a>'
                  + '</div>'
                  + (full.email ? ('<div class="contact">' + full.email + '</div>') : '')
                  + (full.blog ? ('<div class="blog">' + full.blog + '</div>') : '')
                + '</div>'
              + '</li>';
        }

        html += '</ul>';
        return html;
      }

      /**
       * Issues Markup
       */

      markUp.issues  = function (data) {
        var html = '<ul>';

        for (var i = 0; i < data.issues.length; i++) {
          var issue = data.issues[i];

          html += '<li class="' + (i == (data.issues.length - 1) ? 'last ' : '') + (i%2 ? 'odd' : 'even') + '">'
                    + '<span class="issue-number">#' + issue.number + '</span>'
                    + '<a href="' + issue.html_url + '" target="_blank">'
                      + issue.title
                    + '</a>'
                    + (issue.assignee
                        ? ' assigned to <a href="' + issue.assignee.url + '">' + issue.assignee.login + '</a>'
                        : ''
                      )
                + '</li>';
        }

        html += '</ul>';
        return html;
      }

      /**
       * Repository Markup
       */

      markUp.repository  = function (data) {
        var repo = data.repository;

        var html = '<ul>'
                + '<li>'
                  + '<h2><a href="' + repo.html_url + '" title="' + repo.full_name + '" target="_blank">'
                    + repo.name
                  + '</a></h2>'
                  + '<p>' + repo.description + '</p>'
                + '</li>'
              + '</ul>';

        return html;
      }

      /**
       * process all widget sections
       */

      getData(params, function (data) {
        for (var k in data) {
          // ph DOM element
          var ph = widget.find("." + k + "-placeholder");

          ph.html(markUp[k](data[k]));
        }
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
