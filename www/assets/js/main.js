$(window).scroll(function() {
    if($(window).scrollTop() > 420) {
        $('.sidebar').css({'position' : 'fixed', 'margin-top' : '120px'});
    } else {
        $('.sidebar').css({'position' : 'relative', 'margin-top' : '20px'});
    }
});

$(document).ready(function() {

    $('.menu a, a[rel=clickable]').click(function() {
        $('.menu a').removeClass('active');
        if( $(this).attr('href') == '#top') $('.menu a[href=#top]').addClass('active');
        else if( $(this).attr('href') == '#download' ) $('.menu a[href=#download]').addClass('active');
        else $(this).addClass('active');
        $('html, body').animate({scrollTop: $($(this).attr('href').substr(1)).offset().top - 115}, 'slow');
    });

    $('#prev').click(function() {
        $('.slideshow ul li:first').before($('.slideshow ul li:last'));
        $('#magnify').attr('href', $('.slideshow ul li:first img').attr('src').replace('small-', '') );
    });

    $('#next').click(function() {
        $('.slideshow ul li:last').after($('.slideshow ul li:first'));
        $('#magnify').attr('href', $('.slideshow ul li:first img').attr('src').replace('small-', '') );
    });
    $("#magnify").click(function(e) {
        e.preventDefault();
        $(this).colorbox();

    });
    SyntaxHighlighter.all();

});