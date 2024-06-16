$(function(){



/*mobile-menu*/
$('.menu-button').click(function(){
    $(this).toggleClass('active');
    $('.mobile-menu').toggleClass('open');
});
$('.mobile-menu-inner > ul > li > a').click(function(){
    $('.menu-button').removeClass('active');
    $('.mobile-menu').removeClass('open');
});
$('.mobile-menu .dropdown-button').click(function(){
    $(this).toggleClass('active');
    if($(this).siblings('.menu-mobile-ul-wrap').is(':visible')){
        $(this).siblings('.menu-mobile-ul-wrap').slideUp();
    }else{
        $(this).siblings('.menu-mobile-ul-wrap').slideDown();
    }
    
});    
/*mobile-menu./*/

/*about-modal*/
$("a[href='#about']").click(function(e){
    e.preventDefault();
    $('#about').addClass('active');
    $('.overlay-about').fadeIn(200);
});
$('#about .close').click(function(){
    $('#about').removeClass('active');
    $('.overlay-about').fadeOut(200);
});  
/*about-modal./*/

/*header*/
    var panel=$('.header-wrapper'),pos=panel.offset().top;
    function fixPanel(){
        if($(this).scrollTop() > pos && !panel.hasClass('fixed')){
            panel.addClass('fixed');
        }else if($(this).scrollTop() < pos + 1 && panel.hasClass('fixed')){         
            panel.removeClass('fixed');  
        }
    }
    fixPanel()
    $(window).scroll(function(){
        fixPanel()
    });
    window.addEventListener("resize", function() {
        $(window).scroll(function(){
            fixPanel()
        });
    }, false);
    window.addEventListener("orientationchange", function() {
        $(window).scroll(function(){
            fixPanel()
        });
    }, false);
    $('.disabled').click(function(){
        return(false);
    })
/*header./*/

 $('.aside-menu-title').click(function() {
     if(!$('.aside-menu-wrap').hasClass('active')){
        $('.aside-menu-wrap').addClass('active');
        $(this).addClass('active');
        $('.aside-menu-wrap').slideDown();   
     }
     else{
        $('.aside-menu-wrap').removeClass('active');
        $(this).removeClass('active');
        $('.aside-menu-wrap').slideUp();   
     }
});
/*fancybox*/
$('.fancyboxModal').fancybox({
    backFocus : false,
    autoResize:true,            
    padding: 0,
    fitToView : false, 
    maxWidth: '100%',
    scrolling : "no",
    wrapCSS : 'fancybox-animate-wrap',
    touch: false,
    autoFocus: false,
    lang : 'ru',
    i18n : {
        'ru' : {
            CLOSE : 'Закрыть',
            NEXT: "Далее",
            PREV: "Назад",
        }
    }
});

$('.fancybox').fancybox({
        padding: 0,
        helpers: {
        overlay: {
                locked: false
            }
        },
        lang : 'ru',
        i18n : {
            'ru' : {
                CLOSE : 'Закрыть',
                NEXT: "Далее",
                PREV: "Назад",
                ERROR: "Запрошенные данные не могут быть загружены. <br/> Повторите попытку позже.",
                PLAY_START: "Начать слайд-шоу",
                PLAY_STOP: "Завершить слайд-шоу",
                FULL_SCREEN: "На весь экран",
                THUMBS: "Миниатюры",
                DOWNLOAD: "Скачать",
                SHARE: "Поделиться",
                ZOOM: "Увеличить"
            }
        }
    });
    
/*fancybox./*/
// validation

 

//Swiper
   var gallery_thumbs = new Swiper(".gallery-thumbs-wrapper", {
          spaceBetween: 10,
          slidesPerView: 4,
          freeMode: true,
          watchSlidesProgress: true,
        });
        var gallery_main = new Swiper(".gallery-main-wrapper", {
          spaceBetween: 10,
          thumbs: {
            swiper: gallery_thumbs,
          },
        });
        
//Swiper end

   
/* plus minus goods counter */        
$.fn.globalNumber = function(){
$('.btn-number').click(function(e){
    e.preventDefault();
    fieldName = $(this).attr('data-field');
    type      = $(this).attr('data-type');
    var input = $("input#"+fieldName);

    var currentVal = parseInt(input.val());
    if (!isNaN(currentVal)) {
        if(type == 'minus') {
            
            if(currentVal > input.attr('data-min')) {
                input.val(currentVal - 1).change();
            } 
            if(parseInt(input.val()) == input.attr('data-min')) {
                $(this).attr('disabled', true);
            }

        } else if(type == 'plus') {

            if(currentVal < input.attr('data-max')) {
                input.val(currentVal + 1).change();
            }
            if(parseInt(input.val()) == input.attr('data-max')) {
                $(this).attr('disabled', true);
            }

        }
    } else {
        input.val(0);
    }
});
$('.input-number').focusin(function(){
   $(this).data('oldValue', $(this).val());
});
$('.input-number').change(function() {
    
    minValue =  parseInt($(this).attr('data-min'));
    maxValue =  parseInt($(this).attr('data-max'));
    valueCurrent = parseInt($(this).val());

    name = $(this).attr('id');
    if(valueCurrent >= minValue) {
        $(".btn-number[data-type='minus'][data-field='"+name+"']").removeAttr('disabled')
    } else {
        alert('К сожалению, было достигнуто минимальное значение');
        $(this).val($(this).data('oldValue'));
    }
    if(valueCurrent <= maxValue) {
        $(".btn-number[data-type='plus'][data-field='"+name+"']").removeAttr('disabled')
    } else {
        alert('К сожалению, было превышено максимальное значение');
        $(this).val($(this).data('oldValue'));
    }
    
    
});
$(".input-number").keydown(function (e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
            (e.keyCode == 65 && e.ctrlKey === true) || 
            (e.keyCode >= 35 && e.keyCode <= 39)) {
                return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
};$.fn.globalNumber();
/* /. plus minus goods counter */ 
    
/*cost*/
 $('.ms2_form').each(function(){
 let $ms2_form = $(this),
    $product_cost = $ms2_form.find('.productcost'),
    $product_old_cost = $ms2_form.find('.productoldcost'),
    $product_price = $ms2_form.find('.price'),
    $input_number = $ms2_form.find('.input-number'),
    $product_old_price = $ms2_form.find('.old-price'),
    
    product_price = $product_price.text().replace(' ',''), 
    product_old_price = $product_old_price.text().replace(' ',''), 
    product_count = $input_number.val(),
    product_cost,
    product_old_cost;
        
    
    product_price = parseInt(product_price)
    product_cost = product_price * product_count
    product_cost = product_cost.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")
    $product_cost.text(product_cost)
    
    
    product_old_price = parseInt(product_old_price)
    product_old_cost = product_old_price * product_count
    product_old_cost = product_old_cost.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")
    $product_old_cost.text(product_old_cost)
    
    
    $input_number.change(function(){
        product_count = $input_number.val()
        product_cost = product_price * product_count
        product_cost = product_cost.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")
        $product_cost.text(product_cost)
        
        product_old_cost = product_old_price * product_count
        product_old_cost = product_old_cost.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")
        $product_old_cost.text(product_old_cost)
    });
    


    $product_price.bind("DOMSubtreeModified",function(){
        product_price = $product_price.text().replace(' ','')
        product_price = parseInt(product_price)
        product_count = $input_number.val()
        product_cost = product_price * product_count
        product_cost = product_cost.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")
        $product_cost.text(product_cost)
        
        product_old_price = $product_old_price.text().replace(' ','')
        product_old_price = parseInt(product_old_price)
        
        product_old_cost = product_old_price * product_count
        product_old_cost = product_old_cost.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")
        $product_old_cost.text(product_old_cost)
        
    });
 })

/*cost./*/

// styler
    if($('.select-styler').length > 0){
        $('.select-styler').styler({
            selectSearch: false,
        }); 
    }

    
    
    
    

}); // end document ready



