
$('body').on('click','.set-currency',function (e) {
    e.preventDefault()
    $.get('/ajax-set-currency',{
        data:$(this).data('key')
    },function () {
        location.reload()
    })
})