var url = 'http://m-local.baolq666.com';

var token=sessionStorage.getItem("token");
function GetRequest() {
   var url = location.search; //获取url中"?"符后的字串  
   var theRequest;  
   if (url.indexOf("?") != -1) {  
      var theRequest = decodeURIComponent(url.substr(1));   
   }  
   return theRequest;  
}  



/** 滚动事件监听 */
function scrolloading(open, fn) {
    if (open) {
        var vh = $(window).height();
        $(window).on('scroll.request', function () {
            var a = $(this).scrollTop();
            var b = $(document).height();
            (a + vh >= b * 0.92) && fn && fn();
        })
    } else {
        
        $(window).off('scroll.request');
    }
}
/**
 * 
 * @param {object} opts
 * @param {string} opts.api
 * @param {object} opts.params
 * @param {boolean} [opts.async]
 * @param {string} [opts.method]
 * @param {Function} opts.before
 * @param {Function} opts.after
 */
function ajax(opts) {
    $.ajax({
        url: url + opts.api,
        data: opts.params || {},
        dataType: 'JSON',
        async: opts.async || true,
        method: opts.method || 'GET',
        beforeSend: opts.before,
        success: opts.after,
    });
}
/**
 * 
 * @param {object} opts
 * @param {string} opts.api
 * @param {object} opts.params
 * @param {string} [opts.method]
 * @param {string} opts.noDataText
 * @param {jQuery} opts.container
 * @param {string} opts.item
 * @param {Function} opts.html
 * @param {boolean} opts.scroll
 */
function request(opts) {
    var api = opts.api;
    var params = opts.params;
    var $container = opts.container;
    ajax({
        api: opts.api,
        params: opts.params,
        method: opts.method,
        before: function () {
            opts.scroll && scrolloading(false);   
        },
        after: function (res) {
            /** @type {Array} */
            var list;
            if (res.content instanceof Array) {
                list = res.content;
            } else {
                list = res.content.list;
            }
            /** 如果返回的数据为空，则插入表示无数据的元素 */
            if (list.length === 0) {
            	if(params.page) {
            		$(".weui-loadmore").addClass('weui-loadmore_line').html('<span class="weui-loadmore__tips">' + opts.noDataText + '</span>');
            	} else {
            		$container.html('<div class="weui-loadmore weui-loadmore_line"><span class="weui-loadmore__tips">' + opts.noDataText + '</span></div>');
            	}
                return;
            }
            var html = opts.html(list);
            if (params.page === 0) {
                $container.children(opts.item).remove();                
            }
            $container.children().last().before(html);
            /** 如果数据少于请求的数目，则插入表示结束的元素 */
            if (list.length < params.size) {
                $container.children().last().remove();
                $container.append('<div class="weui-loadmore weui-loadmore_line weui-loadmore_dot"><span class="weui-loadmore__tips"></span></div>');
                return;
            }
            opts.params.page += 1;
            opts.scroll && scrolloading(true, function () {
                request(opts);
            });
        }
    })
}
