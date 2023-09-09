import $ from 'jquery';

$('.media-object').on('click', function () {
    event.preventDefault();
    console.log($(this).attr('alt'));
});        