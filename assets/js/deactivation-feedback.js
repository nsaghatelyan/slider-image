"use strict"
jQuery(document).ready(function(){
   var deactivationLink = jQuery('#the-list').find('tr[data-slug='+hugeitSliderL10n.slug+'] .deactivate a'),
       confirmDeactivationLink = jQuery(".hugeit-deactivate-plugin"),
       cancelDeactivationLink = jQuery(".hugeit-cancel-deactivation"),
       deactivationURL;


   deactivationLink.on('click',function(e){
       e.preventDefault();

       hugeitModal.show(hugeitSliderL10n.slug+'-deactivation-feedback');
       deactivationURL = jQuery(this).attr('href');

        return false;
   });

    confirmDeactivationLink.on('click',function(e){
       e.preventDefault();

       var checkedOption = jQuery('input[name='+hugeitSliderL10n.slug+'-deactivation-reason]:checked');

       if(checkedOption.length){
           alert(checkedOption.val());
       }else{
           hugeitModal.hide(hugeitSliderL10n.slug+'-deactivation-feedback');
       }

       return false;
    });

    cancelDeactivationLink.on('click',function(e){
        e.preventDefault();

        hugeitModal.hide(hugeitSliderL10n.slug+'-deactivation-feedback');

        return false;
    });
});