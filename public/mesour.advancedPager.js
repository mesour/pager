/**
 * Mesour Grid Selection Component - mesour.advancedPager.js
 * @author Matous Nemec (http://mesour.com)
 */
var mesour = !mesour ? {} : mesour;
mesour.advancedPager = !mesour.advancedPager ? {} : mesour.advancedPager;

(function ($) {
    var AdvancedPager = function (options) {

        var resizeInput = function () {
                var $this = $(this),
                    size = $this.val().length;
                size = size * 10;
                $this.css('width', size + 15);
            },
            sendData = function (e) {
                e.preventDefault();

                var $this = $(this),
                    input = $this.closest('.input-group').find('input[' + options.inputAttribute + ']');

                var out = mesour.core.createLink($this.attr(options.buttonAttribute), 'setPage', {
                    'page': input.val()
                }, true);

                $.post.apply($, out).complete(mesour.core.redrawCallback);
            };

        this.create = function () {
            $('input[' + options.inputAttribute + ']')
                .keyup(resizeInput)
                .keyup(function (e) {
                    if (e.keyCode === 13) {
                        sendData.call(
                            $(this).closest('.input-group').find('[' + options.buttonAttribute + ']'),
                            e
                        );
                    }
                })
                .each(resizeInput);

            $('[' + options.buttonAttribute + ']')
                .on('click', sendData);
        };
    };

    mesour.core.createWidget('advancedPager', new AdvancedPager({
        inputAttribute: 'data-page-input',
        buttonAttribute: 'data-page-button'
    }));

    mesour.on.live('mesour-advanced-pager', mesour.advancedPager.create);
})(jQuery);