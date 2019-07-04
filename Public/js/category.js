$(document).on("click", ".icon_add",
    function () {
        $("ul", $(this).parent()).show();
        $(this).removeClass('icon_add');
        $(this).addClass('icon_min');
        return false;
    }
);

$(document).on("click", ".icon_min",
    function () {
        $("ul", $(this).parent()).hide();
        $(this).removeClass('icon_min');
        $(this).addClass('icon_add');
        return false;
    }
);


//$(function()
//		{
//	$('.icon_min').click(function(){
//		closeCategory($(this));
//});
//
//	$('.icon_add').click(function(){
//			openCategory($(this));
//	});
//		
//});

//function closeCategory(element)
//{
////	alert($(element).parent().parent().html());
//	$("ul",$(element).parent()).hide();
//	$(element).removeClass('icon_min');
//	$(element).addClass('icon_add');
//	$(element).bind('click',function(){
//			openCategory($(element));
//			});
//}
//
//function openCategory(element)
//{
//	$("ul",$(element).parent()).show();
//	$(element).removeClass('icon_add');
//	$(element).addClass('icon_min');
//	$(element).bind('click',function(){
//		closeCategory($(element));
//	});
//
//}
