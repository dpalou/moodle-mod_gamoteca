// Standard license block omitted.
/*
 * @package    mod_gamoteca
 * @copyright  2020 Catalyst IT Europe (http://www.catalyst-eu.net/)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 /**
   * Open gamoteca game in a new window / tab. Refresh the parent window if the
   * user closes the child window.
   *
   * @module mod_gamoteca/gamoteca
   * @param {string} linkid - Unique Link ID
   * @param {string} url - URL to go to
   * @param {string} windowmessage - Message to be displayed on the parent page.
   */
define(['jquery'], function($, linkid, url, windowmessage) {

    // Functionality to open the Gamoteca game in a new window / tab.
    var gamotecawindow;

    /**
      * Reload the parent window on closing the child window / tab.
      * @return void
      */
    var onClosed = function() {
        location.reload();
    };

    /**
      * Check if the child window /tab is closed.
      * @return void
      */
    var checkWindowClosed = function() {
        var timer = setInterval(function() {
            if (gamotecawindow.closed) {
                clearInterval(timer);
                gamotecawindow = undefined;
                onClosed();
            }
        }, 200);
    };

    return {
        initialise: function (linkid, url, windowmessage, wwwroot, instanceid) {
            $('#' + linkid).on("click", function(e) {
                e.preventDefault();
                if (gamotecawindow === undefined) {
                    // Start the timer when we get focus back to the main window
                    $('#' + linkid).replaceWith(windowmessage);

                    $(window).blur(function() {
                        if(gamotecawindow) {
                            checkWindowClosed(gamotecawindow);
                        }
                    });

                    var args = {
                        id: instanceid
                    };
                    $.post({
                        url: wwwroot + "/mod/gamoteca/ajax.php",
                        data: args
                    });

                    gamotecawindow = window.open(url, 'Popup');
                    checkWindowClosed(gamotecawindow);
                } else {
                    gamotecawindow.focus();
                }
            });
        }
    };

});
