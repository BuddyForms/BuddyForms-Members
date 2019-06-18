jQuery(document).ready(function (jQuery) {
    jQuery('#sub_nav_edit-personal-li').remove();
    jQuery('#sub_nav_revison-personal-li').remove();
    jQuery('#sub_nav_page-personal-li').remove();

    var wrapper =  document.createElement('div');
    wrapper.setAttribute("id", "buddypress");
    wrapper.setAttribute("class", "buddypress-wrap bp-dir-hori-nav");
    var element = jQuery('.entry-content')[0];
    if(element){
        var parent = element.parentNode;
        parent.replaceChild(wrapper, element);
        wrapper.appendChild(element);
    }
});