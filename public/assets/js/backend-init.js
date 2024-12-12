define(['backend'], function (Backend) {
    // 图片放大预览  在img标签中添加data-tips-image
    $('body').on('click', '[data-tips-image]', function () {
        var img = new Image();
        var imgWidth = this.getAttribute('data-width') || '500px';
        img.onload = function () {
            var $content = $(img).appendTo('body').css({background: '#fff', width: imgWidth, height: 'auto'});
            Layer.open({
                type: 1, area: imgWidth, title: false, closeBtn: 1,
                skin: 'layui-layer-nobg', shadeClose: true, content: $content,
                end: function () {
                    $(img).remove();
                },
                success: function () {

                }
            });
        };
        img.onerror = function (e) {

        };
        img.src = this.getAttribute('data-tips-image') || this.src;
    });

});

// if (!Config.isSuperAdmin){
//     console.log()
// document.getElementById('mer_account').style.display = 'none';
// }
// document.getElementById('mer_account').style.display = 'none';
$(document).on("change", "#c-country_id", function(){
    // 父级发生切换，清除子级所选
    $("#c-zone_id").selectPageClear();
});

// 给子级设置 data-params 属性
$("#c-zone_id").data("params", function(){
    // 绑定父级的值
    return {custom: {country_id:$("#c-country_id").val()}};
});