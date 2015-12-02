jQuery(function($){

    // 延迟加载图片
    var blazy = new Blazy();

    // 最小高度
    (function(){
      var minScreenH = 623, // 最小高度 768-120-25
            windowHeight = $(window).height();
      if (windowHeight <= minScreenH){
          windowHeight = minScreenH;
      }
      $('.mall-row').css('height', windowHeight);
      $('.mall-row-1').css('height', windowHeight - 100);
      $('.mall-row-1>.content').css('height', windowHeight - 200);
      $(window).resize(function(){
          windowHeight = $(window).height();
          if($(window).height() <= minScreenH){
              $('.mall-row').css('height', minScreenH);
          }else{
              $('.mall-row').css('height', windowHeight);
          }
      });
    })();

    (function(){
          var curIndex = 0;
          var isScrolling = false; // 是否滑动中
          var sectionList = $('.section'); // 要作用的分屏对象
          var sectionCount = sectionList.length; // 滑屏的分屏对象数量

          // 当屏函数出现时的一些功能定义
          function showSection(i){
              sectionList.find('.tit').removeClass(' '); // 展现此屏时对应的变动
              $(sectionList[i]).find('.tit').addClass(' ');
          }

          // 缩略图滑屏滚动导航及固定
          var slideNav = $(".shoes-thumb-index>.thumb-index-1"); // 获取圆点导航对象

          function sectionNav(index){
              slideNav.eq(index).children().addClass("cur").parents().siblings().children().removeClass("cur");
          }

          slideNav.on("click", function(){
            curIndex = $(this).index();
            isScrolling = true;
            $('html,body').stop().animate({
                  scrollTop: $(sectionList[curIndex]).offset().top
              },800, function() {
                      isScrolling = false;
                  });
          });

          function indexThumbNav(){
            var unHeight = (sectionCount - 3)*$(".mall-row").height(); //3屏未满屏
            if($(this).scrollTop() >= $(window).height()){//&& $(this).scrollTop() < unHeight
              $(".index-nav").addClass("fixed");
            }else{
              $(".index-nav").removeClass("fixed");
            }
          };
          indexThumbNav();
          $(window).scroll(function(){
            indexThumbNav();
          });

          //页面刷新调整
          adjustCurIndex();

          // 滑屏滚动索引判断定义
          function adjustCurIndex(){
              var $w = $(window);
              var winScrollTop = $w.scrollTop();
              var winHeight = $w.height(); // 获取window窗口高度
              var viewTop = $w.scrollTop(), // 获取window窗口相对滚动条顶部的偏移
                  viewBottom = viewTop + $w.height(); // 向下滑屏滚动的高度是window窗口的高度
              for (var i = 0; i < sectionCount; i++) {
                  var section = $(sectionList[i]); // 定义分屏的索引
                  var sectionTop = section.offset().top; // 屏的当前视口的相对偏移
                  var sectionHeight = section.height(); // 屏幕视口的高度
                  var sectionBottom = sectionTop + sectionHeight; // 向下滑屏滚动的高度是屏幕视口的高度
                  if (winScrollTop <= (sectionTop + sectionHeight) && winScrollTop > (sectionTop + sectionHeight / 2)){
                      showedSection = i + 1;
                      showSection(showedSection);
                      sectionNav(curIndex);
                  }
              }
          }

          // scroll 滚动触发相关事件
          var ele;
          $(window).on('scroll', function(){
              adjustCurIndex();
              ele = sectionList[curIndex];
              $(ele).find('.tit').addClass(' ');
          }).resize(function() {
              if(ele){
                  scrollTo(ele);
              }
          });

          // 滚动对象的定义
          function scrollTo(ele){
              sectionList.find('.tit').removeClass(' ');
              isScrolling = true;
              $('html,body').stop().animate({
                  scrollTop: $(ele).offset().top
              }, 500, function() {
                  isScrolling = false;
                  $(ele).find('.tit').addClass(' ');
              });
          }

          $('body').on('mousewheel', function(e){
              e.preventDefault();
              if (!isScrolling){
                  var deltaY = e.deltaY;
                  switch (deltaY) {
                      case 1:
                         if(curIndex > 0)
                              curIndex -= 1;
                          else
                            return false;
                          if(curIndex < 7)
                              $('.index-nav').fadeIn();
                          scrollTo(sectionList[curIndex]);
                          break;
                      case -1:
                          curIndex += 1;
                          if(curIndex === 7)
                             $('.index-nav').fadeOut();
                          //向下滑动到最后一屏
                          if(curIndex > sectionCount - 1){
                              curIndex = sectionCount - 1;
                          }else{
                              scrollTo(sectionList[curIndex]);
                          }
                          break;
                      default:
                          break;
                  }
              }
          });
    })();
});