/*!
 * jQuery Tools v1.2.6 - The missing UI library for the Web
 * 
 * tooltip/tooltip.dynamic.js
 * tooltip/tooltip.slide.js
 * 
 * NO COPYRIGHTS OR LICENSES. DO WHAT YOU LIKE.
 * 
 * http://flowplayer.org/tools/
 * 
 */
(function(a){var b=a.tools.tooltip;b.dynamic={conf:{classNames:"top right bottom left"}};function c(b){var c=a(window),d=c.width()+c.scrollLeft(),e=c.height()+c.scrollTop();return[b.offset().top<=c.scrollTop(),d<=b.offset().left+b.width(),e<=b.offset().top+b.height(),c.scrollLeft()>=b.offset().left]}function d(a){var b=a.length;while(b--)if(a[b])return!1;return!0}a.fn.dynamic=function(e){typeof e=="number"&&(e={speed:e}),e=a.extend({},b.dynamic.conf,e);var f=a.extend(!0,{},e),g=e.classNames.split(/\s/),h;this.each(function(){var b=a(this).tooltip().onBeforeShow(function(b,e){var i=this.getTip(),j=this.getConf();h||(h=[j.position[0],j.position[1],j.offset[0],j.offset[1],a.extend({},j)]),a.extend(j,h[4]),j.position=[h[0],h[1]],j.offset=[h[2],h[3]],i.css({visibility:"hidden",position:"absolute",top:e.top,left:e.left}).show();var k=a.extend(!0,{},f),l=c(i);if(!d(l)){l[2]&&(a.extend(j,k.top),j.position[0]="top",i.addClass(g[0])),l[3]&&(a.extend(j,k.right),j.position[1]="right",i.addClass(g[1])),l[0]&&(a.extend(j,k.bottom),j.position[0]="bottom",i.addClass(g[2])),l[1]&&(a.extend(j,k.left),j.position[1]="left",i.addClass(g[3]));if(l[0]||l[2])j.offset[0]*=-1;if(l[1]||l[3])j.offset[1]*=-1}i.css({visibility:"visible"}).hide()});b.onBeforeShow(function(){var a=this.getConf(),b=this.getTip();setTimeout(function(){a.position=[h[0],h[1]],a.offset=[h[2],h[3]]},0)}),b.onHide(function(){var a=this.getTip();a.removeClass(e.classNames)}),ret=b});return e.api?ret:this}})(jQuery);
(function(a){var b=a.tools.tooltip;a.extend(b.conf,{direction:"up",bounce:!1,slideOffset:10,slideInSpeed:200,slideOutSpeed:200,slideFade:!a.browser.msie});var c={up:["-","top"],down:["+","top"],left:["-","left"],right:["+","left"]};b.addEffect("slide",function(a){var b=this.getConf(),d=this.getTip(),e=b.slideFade?{opacity:b.opacity}:{},f=c[b.direction]||c.up;e[f[1]]=f[0]+"="+b.slideOffset,b.slideFade&&d.css({opacity:0}),d.show().animate(e,b.slideInSpeed,a)},function(b){var d=this.getConf(),e=d.slideOffset,f=d.slideFade?{opacity:0}:{},g=c[d.direction]||c.up,h=""+g[0];d.bounce&&(h=h=="+"?"-":"+"),f[g[1]]=h+"="+e,this.getTip().animate(f,d.slideOutSpeed,function(){a(this).hide(),b.call()})})})(jQuery);
