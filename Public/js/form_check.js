//显示页面ajax快捷修改状态
//$.ajaxE

$(function () {
    $(".toggle .icon_delete").click(function () {
        $(this).parent().parent().hide();
    });
    $(".toggle_btn").click(function () {
        var index = $(".toggle_btn").index(this);
        var ele = $(".toggle:eq(" + index + ")");
        ele.css("top", $(this).offset().top - parseInt($("#header").height()) + 30);
        //console.log($(this).offset().top - parseInt($("#header").height()) + 30);
        ele.toggle();
        /*
         if( ele.css("display") == "none")
         {
         $(this).removeClass("icon_up").addClass("icon_down");
         $(this).attr("title","展开");
         }
         else
         {
         $(this).removeClass("icon_down").addClass("icon_up");
         $(this).attr("title","收起");
         }
         */
    });

    //提交表单自动验证，根据网页元素 <input type="hidden" id="check_html" />
    $("form").submit(function () {
        var err = false;
        var sub_err_html = "<span class=\"icon_delete icon_circle icon_red\"></span><span style=\"color:red\"> 提交有误 , 请修改!</span>";
        var err_html = "<span class=\"icon_delete icon_circle icon_red\"></span><span> 不得为空!</span>";
        var suc_html = "<span class=\"icon_check icon_circle \"></span>";
        var check = $("#check_html").val();

        if (check) {
            check = check.split("|");
        }

        //alert(check);

        var element = $(":input", this);
        $(element).each(function () {
            var name = $(this).attr("name");
            if ($.inArray(name, check) >= 0) {
                var val = $(this).val();
                if($(this).attr("type") == 'radio')
                {
                    var name = $(this).attr('name');
                    val = $(":input[name='"+name+"']:checked").val();
                    if (val == "") {
                        $("~ *", $(":input[name='"+name+"']").last()).remove();
                        $(":input[name='"+name+"']").last().after(err_html);
                        err = true;
                        return false;
                    }
                    else {
                        $("~ *", $(":input[name='"+name+"']").last()).remove();
                        $(":input[name='"+name+"']").last().after(suc_html);
                        return true;
                    }

                }

                if (val == "") {
                    $("~ *", this).remove();
                    $(this).after(err_html);
                    err = true;
                    return false;
                }
                else {
                    $("~ *", this).remove();
                    $(this).after(suc_html);
                    return true;
                }
            }

        });
        if (err) {
            $(":submit").after(sub_err_html);
            setTimeout('$(":submit ~ *").remove(); ', 2000);
            return false;
        }
//        alert($(".select_move_container select").length);
//        return false;
        $(".select_move_container select").each(function () {
            $("option", this).attr("selected", "selected");
        });
    });


    //左侧导航栏随下拉框移动
    var elm = $('#m_left');
    var startPos = $(elm).offset().top;
    var marginTop = parseInt($(elm).css("margin-top"));
    $.event.add(window, "scroll", function () {
        var p = $(window).scrollTop();
        $(elm).css('position', ((p) > startPos) ? 'fixed' : 'static');
        $(elm).css('top', ((p) > startPos) ? (0 - marginTop) + 'px' : '');
    });
});


$(function () {

    $("table .ajax").each(function () {
        $(this).click(function () {
            var the = $(this);
            var url = $(this).attr('ajax');
            if (url == '' || $(the).hasClass('ajax_sel'))
                return;

            var load = setTimeout('loading(1)', 300);
            $.post(url, '', function (data) {
                clearTimeout(load);
                loading(0);
                notice(data.info);
                if (data.status == 1) {
                    $(".ajax", $(the).parent()).removeClass('ajax_sel');
                    $(the).addClass('ajax_sel');
                }
            }, 'json');
        });
    });
});


//显示页面直接修改字段，仅限 input type=“text”
$(function () {
    $("table .ajax_field").each(function () {
        $(this).blur(function () {    //失去焦点时
            var the = $(this);
            var url = the.attr('ajax');
            var val = the.val(); //原始数据
            var val_old = the.attr('data');
            if (url == '' || $(the).hasClass('ajax_sel'))
                return;

            if (val == val_old) {
                notice("未修改");
                return;
            }

            url += val;
            var load = setTimeout('loading(1)', 300);
            $.post(url, '', function (data) {
                clearTimeout(load);
                loading(0);
                notice(data.info);
                if (data.status == 1) {
                    //$(".ajax",$(the).parent()).removeClass('ajax_sel');
                    //$(the).addClass('ajax_sel');
                }
            }, 'json');
        });
    });
});







