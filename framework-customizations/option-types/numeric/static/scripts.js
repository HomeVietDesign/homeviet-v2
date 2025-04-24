jQuery(document).ready(function ($) {
    var optionTypeClass = '.fw-option-type-numeric';

    /**
     * Listen to special event that is triggered for uninitialized elements
     */
    fwEvents.on('fw:options:init', function (data) {
        /**
         * data.$elements are jQuery selected elements
         * that contains options html that needs to be initialized
         *
         * Find uninitialized options by main class
         */
        var $options = data.$elements.find(optionTypeClass +':not(.initialized)');
        //console.log(optionTypeClass);
        $.each($options, function(index, el) {
            var $el = $(el),
                option = $el.parent().data('fw-for-js').option;

            var integer = (option.integer)?true:false;
            var negative = (option.negative)?true:false;
            var decimals = parseInt(option.decimals);
    
            var input = $el.find('input').inputNumber({integer:integer, negative:negative, decimals:decimals});
            
        });

        /**
         * After everything has done, mark options as initialized
         */
        $options.addClass('initialized');
    });
});