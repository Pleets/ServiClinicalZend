/**
 * Historia clínica v1.0
 *
 * @link        http://www.medicadiz.com.co
 * @copyright   Copyright (c) 2014 Medicadiz S.A.S.
 * @license     Free to use under the MIT license. (http://www.opensource.org/licenses/mit-license.php)
 * @version     1.0
 *
 * Date:        2014-04-25
 * Autor:       Darío Rivera
 */

/* Namespace app */
if (!(Srvdata instanceof Object))
    var Srvdata = {};

$(function () {

    // jQuery UI and Bootstrap functionality
    if (!Srvdata.noConflict) {
        var btn = $.fn.button.noConflict() // reverts $.fn.button to jqueryui btn
        $.fn.btn = btn // assigns bootstrap button functionality to $.fn.btn
        Srvdata.noConflict = true;
    }

    /*
     *  SHOW HIDDEN ELEMENTS
     *  Shorcut to show hidden elements
     */

    Srvdata.showHiddenElements = function() {
        $(".hidden").css("display", "none").removeClass("hidden").delay("500").fadeIn("slow");
    }

    $("body").keydown(function(event){
        var keycode = event.which;

        // Ctrl + Alt + U
        if (event.ctrlKey && event.altKey && keycode == 85) 
        {
            Srvdata.showHiddenElements();
        }

    });


    /*
     *  ALTERNATE TABLE
     *  Show and hide columns in tables
     */

    Srvdata.alterTable = function (table, button)
    {
        $("body").delegate(button, "click", function(event) {
            event.preventDefault();

            var exception = $(this)[0].getAttribute('data-exception');

            if (!eval(exception) instanceof Array)
                throw "Expression is not an array";

            $(table + " tr > *").fadeToggle();
            for (var i = eval(exception).length - 1; i >= 0; i--) {
                $(table + " tr > *:nth-child(" + eval(exception)[i] + ")").fadeToggle();
            };
        });
    }

});