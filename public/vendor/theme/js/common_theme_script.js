$(document).on('click', '.institute-type-block a', function(){
    let path = window.location.href.split('?')[0];
    let char = $('.alphabet-href a.checked').data('href');
    let charFilter = (char) ? "&char="+char: "";
    location.href = path + "?type="+$(this).data('href')+charFilter;
})

$(document).on('click', '.alphabet-href a', function(){
    let path = window.location.href.split('?')[0];
    let type = $('.institute-type-block a.checked').data('href');
    let typeFilter = (type) ? "&type="+type: "";
    location.href = path + "?char="+$(this).data('href')+typeFilter;
});

$(document).ready(function(){
       
    });