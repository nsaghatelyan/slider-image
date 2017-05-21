var hugeitModal = {
    show: function(elementId, args){
        var el = jQuery('#'+elementId);
        console.log(el);
        if(el.length){
            el.css('display','flex');
        }
    },

    hide: function(elementId){
        var el = jQuery('#'+elementId);
        el.css('display','none');
    }
};