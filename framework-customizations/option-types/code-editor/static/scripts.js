(function ($, fwe) {
    var optionTypeClass = '.fw-option-type-code-editor';

    /**
     * Listen to special event that is triggered for uninitialized elements
     */
    fwe.on('fw:options:init', function (data) {
        /**
         * data.$elements are jQuery selected elements
         * that contains options html that needs to be initialized
         *
         * Find uninitialized options by main class
         */
        var $options = data.$elements.find(optionTypeClass +':not(.initialized)');

        $.each($options, function(index, el) {
            var textarea = $(el).find('textarea');
            var codeEditor = wp.codeEditor.initialize(textarea);
            codeEditor.codemirror.on('change', function(cm){
                cm.save();
            })
        });

        /**
         * After everything has done, mark options as initialized
         */
        $options.addClass('initialized');
    });

})(jQuery, fwEvents);