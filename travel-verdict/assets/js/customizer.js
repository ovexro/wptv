(function($) {
    'use strict';

    // Live preview for Copyright Text
    wp.customize('copyright_text', function(value) {
        value.bind(function(newval) {
            var blogname = wp.customize.instance('blogname').get();
            $('.copyright').html('© ' + new Date().getFullYear() + ' ' + blogname + '. ' + newval);
        });
    });

})(jQuery);
