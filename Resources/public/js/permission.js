$().ready(function () {
    var label = new Array();
    var tmp = '';
    $('.role_table table th').each(function () {
        tmp = $(this).html().trim();
        if (tmp != 'MASTER') {
            label.push(tmp);
        }
    });
    $('.role_table input').on('ifChecked', function(event){
        var input = $(this);
        var item = null;
        var box = null;
        var slider = null;
        $.each(label, function (index, value) {
            item = $('input[value=' + input.val().replace('MASTER', value).replace('OPERATOR', value) + ']');
            item.prop('checked', input.is(':checked'));
            item.parent().addClass('checked');
        });
    });
    $('input[value$="MASTER"], input[value$="OPERATOR"]').change(function() {
        var input = $(this);
        var item = null;
        var box = null;
        var slider = null;
        $.each(label, function (index, value) {
            if (input.is(':checked') == true) {
                item = $('input[value=' + input.val().replace('MASTER', value).replace('OPERATOR', value) + ']');
                box = item.parent().children('.iToggle');
                slider = box.children('.slider');
                item.prop('checked', input.is(':checked'));
                if (input.prop('checked')) {
                    slider.animate({left: 0}, 70);
                }
                else {
                    slider.animate({left: Math.round(box.innerWidth()/2)}, 70);
                }
            }
        });
    });
    $(".readonly").click(function() {
        return false;
    });
});