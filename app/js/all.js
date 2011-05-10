/*
 * Facebox (for jQuery)
 * version: 1.2 (05/05/2008)
 * @requires jQuery v1.2 or later
 *
 * Examples at http://famspam.com/facebox/
 *
 * Licensed under the MIT:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Copyright 2007, 2008 Chris Wanstrath [ chris@ozmm.org ]
 *
 * Usage:
 *
 *  jQuery(document).ready(function() {
 *    jQuery('a[rel*=facebox]').facebox()
 *  })
 *
 *  <a href="#terms" rel="facebox">Terms</a>
 *    Loads the #terms div in the box
 *
 *  <a href="terms.html" rel="facebox">Terms</a>
 *    Loads the terms.html page in the box
 *
 *  <a href="terms.png" rel="facebox">Terms</a>
 *    Loads the terms.png image in the box
 *
 *
 *  You can also use it programmatically:
 *
 *    jQuery.facebox('some html')
 *    jQuery.facebox('some html', 'my-groovy-style')
 *
 *  The above will open a facebox with "some html" as the content.
 *
 *    jQuery.facebox(function($) {
 *      $.get('blah.html', function(data) { $.facebox(data) })
 *    })
 *
 *  The above will show a loading screen before the passed function is called,
 *  allowing for a better ajaxy experience.
 *
 *  The facebox function can also display an ajax page, an image, or the contents of a div:
 *
 *    jQuery.facebox({ ajax: 'remote.html' })
 *    jQuery.facebox({ ajax: 'remote.html' }, 'my-groovy-style')
 *    jQuery.facebox({ image: 'stairs.jpg' })
 *    jQuery.facebox({ image: 'stairs.jpg' }, 'my-groovy-style')
 *    jQuery.facebox({ div: '#box' })
 *    jQuery.facebox({ div: '#box' }, 'my-groovy-style')
 *
 *  Want to close the facebox?  Trigger the 'close.facebox' document event:
 *
 *    jQuery(document).trigger('close.facebox')
 *
 *  Facebox also has a bunch of other hooks:
 *
 *    loading.facebox
 *    beforeReveal.facebox
 *    reveal.facebox (aliased as 'afterReveal.facebox')
 *    init.facebox
 *    afterClose.facebox
 *
 *  Simply bind a function to any of these hooks:
 *
 *   $(document).bind('reveal.facebox', function() { ...stuff to do after the facebox and contents are revealed... })
 *
 */
(function($) {
  $.facebox = function(data, klass) {
    $.facebox.loading()

    if (data.ajax) fillFaceboxFromAjax(data.ajax, klass)
    else if (data.image) fillFaceboxFromImage(data.image, klass)
    else if (data.div) fillFaceboxFromHref(data.div, klass)
    else if ($.isFunction(data)) data.call($)
    else $.facebox.reveal(data, klass)
  }

  /*
   * Public, $.facebox methods
   */

  $.extend($.facebox, {
    settings: {
      opacity      : 0.2,
      overlay      : true,
      loadingImage : '../images/loading.gif',
      closeImage   : '../images/closelabel.png',
      imageTypes   : [ 'png', 'jpg', 'jpeg', 'gif' ],
      faceboxHtml  : '\
    <div id="facebox" style="display:none;"> \
      <div class="popup"> \
        <div class="content"> \
        </div> \
        <a href="#" class="close"><img src="/facebox/closelabel.png" title="close" class="close_image" /></a> \
      </div> \
    </div>'
    },

    loading: function() {
      init()
      if ($('#facebox .loading').length == 1) return true
      showOverlay()

      $('#facebox .content').empty()
      $('#facebox .body').children().hide().end().
        append('<div class="loading"><img src="'+$.facebox.settings.loadingImage+'"/></div>')

      $('#facebox').css({
        top:	getPageScroll()[1] + (getPageHeight() / 10),
        left:	$(window).width() / 2 - 205
      }).show()

      $(document).bind('keydown.facebox', function(e) {
        if (e.keyCode == 27) $.facebox.close()
        return true
      })
      $(document).trigger('loading.facebox')
    },

    reveal: function(data, klass) {
      $(document).trigger('beforeReveal.facebox')
      if (klass) $('#facebox .content').addClass(klass)
      $('#facebox .content').append(data)
      $('#facebox .loading').remove()
      $('#facebox .body').children().fadeIn('normal')
      $('#facebox').css('left', $(window).width() / 2 - ($('#facebox .popup').width() / 2))
      $(document).trigger('reveal.facebox').trigger('afterReveal.facebox')
    },

    close: function() {
      $(document).trigger('close.facebox')
      return false
    }
  })

  /*
   * Public, $.fn methods
   */

  $.fn.facebox = function(settings) {
    if ($(this).length == 0) return

    init(settings)

    function clickHandler() {
      $.facebox.loading(true)

      // support for rel="facebox.inline_popup" syntax, to add a class
      // also supports deprecated "facebox[.inline_popup]" syntax
      var klass = this.rel.match(/facebox\[?\.(\w+)\]?/)
      if (klass) klass = klass[1]

      fillFaceboxFromHref(this.href, klass)
      return false
    }

    return this.bind('click.facebox', clickHandler)
  }

  /*
   * Private methods
   */

  // called one time to setup facebox on this page
  function init(settings) {
    if ($.facebox.settings.inited) return true
    else $.facebox.settings.inited = true

    $(document).trigger('init.facebox')
    makeCompatible()

    var imageTypes = $.facebox.settings.imageTypes.join('|')
    $.facebox.settings.imageTypesRegexp = new RegExp('\.(' + imageTypes + ')$', 'i')

    if (settings) $.extend($.facebox.settings, settings)
    $('body').append($.facebox.settings.faceboxHtml)

    var preload = [ new Image(), new Image() ]
    preload[0].src = $.facebox.settings.closeImage
    preload[1].src = $.facebox.settings.loadingImage

    $('#facebox').find('.b:first, .bl').each(function() {
      preload.push(new Image())
      preload.slice(-1).src = $(this).css('background-image').replace(/url\((.+)\)/, '$1')
    })

    $('#facebox .close').click($.facebox.close)
    $('#facebox .close_image').attr('src', $.facebox.settings.closeImage)
  }

  // getPageScroll() by quirksmode.com
  function getPageScroll() {
    var xScroll, yScroll;
    if (self.pageYOffset) {
      yScroll = self.pageYOffset;
      xScroll = self.pageXOffset;
    } else if (document.documentElement && document.documentElement.scrollTop) {	 // Explorer 6 Strict
      yScroll = document.documentElement.scrollTop;
      xScroll = document.documentElement.scrollLeft;
    } else if (document.body) {// all other Explorers
      yScroll = document.body.scrollTop;
      xScroll = document.body.scrollLeft;
    }
    return new Array(xScroll,yScroll)
  }

  // Adapted from getPageSize() by quirksmode.com
  function getPageHeight() {
    var windowHeight
    if (self.innerHeight) {	// all except Explorer
      windowHeight = self.innerHeight;
    } else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
      windowHeight = document.documentElement.clientHeight;
    } else if (document.body) { // other Explorers
      windowHeight = document.body.clientHeight;
    }
    return windowHeight
  }

  // Backwards compatibility
  function makeCompatible() {
    var $s = $.facebox.settings

    $s.loadingImage = $s.loading_image || $s.loadingImage
    $s.closeImage = $s.close_image || $s.closeImage
    $s.imageTypes = $s.image_types || $s.imageTypes
    $s.faceboxHtml = $s.facebox_html || $s.faceboxHtml
  }

  // Figures out what you want to display and displays it
  // formats are:
  //     div: #id
  //   image: blah.extension
  //    ajax: anything else
  function fillFaceboxFromHref(href, klass) {
    // div
    if (href.match(/#/)) {
      var url    = window.location.href.split('#')[0]
      var target = href.replace(url,'')
      if (target == '#') return
      $.facebox.reveal($(target).html(), klass)

    // image
    } else if (href.match($.facebox.settings.imageTypesRegexp)) {
      fillFaceboxFromImage(href, klass)
    // ajax
    } else {
      fillFaceboxFromAjax(href, klass)
    }
  }

  function fillFaceboxFromImage(href, klass) {
    var image = new Image()
    image.onload = function() {
      $.facebox.reveal('<div class="image"><img src="' + image.src + '" /></div>', klass)
    }
    image.src = href
  }

  function fillFaceboxFromAjax(href, klass) {
    $.get(href, function(data) { $.facebox.reveal(data, klass) })
  }

  function skipOverlay() {
    return $.facebox.settings.overlay == false || $.facebox.settings.opacity === null
  }

  function showOverlay() {
    if (skipOverlay()) return

    if ($('#facebox_overlay').length == 0)
      $("body").append('<div id="facebox_overlay" class="facebox_hide"></div>')

    $('#facebox_overlay').hide().addClass("facebox_overlayBG")
      .css('opacity', $.facebox.settings.opacity)
      .click(function() { $(document).trigger('close.facebox') })
      .fadeIn(200)
    return false
  }

  function hideOverlay() {
    if (skipOverlay()) return

    $('#facebox_overlay').fadeOut(200, function(){
      $("#facebox_overlay").removeClass("facebox_overlayBG")
      $("#facebox_overlay").addClass("facebox_hide")
      $("#facebox_overlay").remove()
    })

    return false
  }

  /*
   * Bindings
   */

  $(document).bind('close.facebox', function() {
    $(document).unbind('keydown.facebox')
    $('#facebox').fadeOut(function() {
      $('#facebox .content').removeClass().addClass('content')
      $('#facebox .loading').remove()
      $(document).trigger('afterClose.facebox')
    })
    hideOverlay()
  })

})(jQuery);



/* JTools Tooltip */
(function(f){
    function p(a,b,c){
        var h=c.relative?a.position().top:a.offset().top,e=c.relative?a.position().left:a.offset().left,i=c.position[0];
        h-=b.outerHeight()-c.offset[0];
        e+=a.outerWidth()+c.offset[1];
        var j=b.outerHeight()+a.outerHeight();
        if(i=="center")h+=j/2;
        if(i=="bottom")h+=j;
        i=c.position[1];
        a=b.outerWidth()+a.outerWidth();
        if(i=="center")e-=a/2;
        if(i=="left")e-=a;
        return{
            top:h,
            left:e
        }
    }
    function t(a,b){
        var c=this,h=a.add(c),e,i=0,j=0,m=a.attr("title"),q=n[b.effect],k,r=a.is(":input"),u=r&&a.is(":checkbox, :radio, select, :button, :submit"),
        s=a.attr("type"),l=b.events[s]||b.events[r?u?"widget":"input":"def"];
        if(!q)throw'Nonexistent effect "'+b.effect+'"';
        l=l.split(/,\s*/);
        if(l.length!=2)throw"Tooltip: bad events configuration for "+s;
        a.bind(l[0],function(d){
            clearTimeout(i);
            if(b.predelay)j=setTimeout(function(){
                c.show(d)
            },b.predelay);else c.show(d)
        }).bind(l[1],function(d){
            clearTimeout(j);
            if(b.delay)i=setTimeout(function(){
                c.hide(d)
            },b.delay);else c.hide(d)
        });
        if(m&&b.cancelDefault){
            a.removeAttr("title");
            a.data("title",m)
        }
        f.extend(c,{
            show:function(d){
                if(!e){
                    if(m)e=
                        f(b.layout).addClass(b.tipClass).appendTo(document.body).hide().append(m);
                    else if(b.tip)e=f(b.tip).eq(0);
                    else{
                        e=a.next();
                        e.length||(e=a.parent().next())
                    }
                    if(!e.length)throw"Cannot find tooltip for "+a;
                }
                if(c.isShown())return c;
                e.stop(true,true);
                var g=p(a,e,b);
                d=d||f.Event();
                d.type="onBeforeShow";
                h.trigger(d,[g]);
                if(d.isDefaultPrevented())return c;
                g=p(a,e,b);
                e.css({
                    position:"absolute",
                    top:g.top,
                    left:g.left
                });
                k=true;
                q[0].call(c,function(){
                    d.type="onShow";
                    k="full";
                    h.trigger(d)
                });
                g=b.events.tooltip.split(/,\s*/);
                e.bind(g[0],function(){
                    clearTimeout(i);
                    clearTimeout(j)
                });
                g[1]&&!a.is("input:not(:checkbox, :radio), textarea")&&e.bind(g[1],function(o){
                    o.relatedTarget!=a[0]&&a.trigger(l[1].split(" ")[0])
                });
                return c
            },
            hide:function(d){
                if(!e||!c.isShown())return c;
                d=d||f.Event();
                d.type="onBeforeHide";
                h.trigger(d);
                if(!d.isDefaultPrevented()){
                    k=false;
                    n[b.effect][1].call(c,function(){
                        d.type="onHide";
                        k=false;
                        h.trigger(d)
                    });
                    return c
                }
            },
            isShown:function(d){
                return d?k=="full":k
            },
            getConf:function(){
                return b
            },
            getTip:function(){
                return e
            },
            getTrigger:function(){
                return a
            }
        });
        f.each("onHide,onBeforeShow,onShow,onBeforeHide".split(","),function(d,g){
            f.isFunction(b[g])&&f(c).bind(g,b[g]);
            c[g]=function(o){
                f(c).bind(g,o);
                return c
            }
        })
    }
    f.tools=f.tools||{
        version:"1.2.3"
    };

    f.tools.tooltip={
        conf:{
            effect:"toggle",
            fadeOutSpeed:"fast",
            predelay:0,
            delay:30,
            opacity:1,
            tip:0,
            position:["top","center"],
            offset:[0,0],
            relative:false,
            cancelDefault:true,
            events:{
                def:"mouseenter,mouseleave",
                input:"focus,blur",
                widget:"focus mouseenter,blur mouseleave",
                tooltip:"mouseenter,mouseleave"
            },
            layout:"<div/>",
            tipClass:"tooltip"
        },
        addEffect:function(a,b,c){
            n[a]=[b,c]
        }
    };

    var n={
        toggle:[function(a){
            var b=this.getConf(),c=this.getTip();
            b=b.opacity;
            b<1&&c.css({
                opacity:b
            });
            c.show();
            a.call()
        },function(a){
            this.getTip().hide();
            a.call()
        }],
        fade:[function(a){
            var b=this.getConf();
            this.getTip().fadeTo(b.fadeInSpeed,b.opacity,a)
        },function(a){
            this.getTip().fadeOut(this.getConf().fadeOutSpeed,a)
        }]
    };

    f.fn.tooltip=function(a){
        var b=this.data("tooltip");
        if(b)return b;
        a=f.extend(true,{},f.tools.tooltip.conf,a);
        if(typeof a.position=="string")a.position=a.position.split(/,?\s/);
        this.each(function(){
            b=new t(f(this),a);
            f(this).data("tooltip",b)
        });
        return a.api?b:this
    }
})(jQuery);

jQuery.fn.corner = function(o) {
    function hex2(s) {
        var s = parseInt(s).toString(16);
        return ( s.length < 2 ) ? '0'+s : s;
    };
    function gpc(node) {
        for ( ; node && node.nodeName.toLowerCase() != 'html'; node = node.parentNode  ) {
            var v = jQuery.css(node,'backgroundColor');
            if ( v.indexOf('rgb') >= 0 ) {
                rgb = v.match(/\d+/g);
                return '#'+ hex2(rgb[0]) + hex2(rgb[1]) + hex2(rgb[2]);
            }
            if ( v && v != 'transparent' )
                return v;
        }
        return '#ffffff';
    };
    function getW(i) {
        switch(fx) {
            case 'round':
                return Math.round(width*(1-Math.cos(Math.asin(i/width))));
            case 'cool':
                return Math.round(width*(1+Math.cos(Math.asin(i/width))));
            case 'sharp':
                return Math.round(width*(1-Math.cos(Math.acos(i/width))));
            case 'bite':
                return Math.round(width*(Math.cos(Math.asin((width-i-1)/width))));
            case 'slide':
                return Math.round(width*(Math.atan2(i,width/i)));
            case 'jut':
                return Math.round(width*(Math.atan2(width,(width-i-1))));
            case 'curl':
                return Math.round(width*(Math.atan(i)));
            case 'tear':
                return Math.round(width*(Math.cos(i)));
            case 'wicked':
                return Math.round(width*(Math.tan(i)));
            case 'long':
                return Math.round(width*(Math.sqrt(i)));
            case 'sculpt':
                return Math.round(width*(Math.log((width-i-1),width)));
            case 'dog':
                return (i&1) ? (i+1) : width;
            case 'dog2':
                return (i&2) ? (i+1) : width;
            case 'dog3':
                return (i&3) ? (i+1) : width;
            case 'fray':
                return (i%2)*width;
            case 'notch':
                return width;
            case 'bevel':
                return i+1;
        }
    };
    o = (o||"").toLowerCase();
    var keep = /keep/.test(o);                       // keep borders?
    var cc = ((o.match(/cc:(#[0-9a-f]+)/)||[])[1]);  // corner color
    var sc = ((o.match(/sc:(#[0-9a-f]+)/)||[])[1]);  // strip color
    var width = parseInt((o.match(/(\d+)px/)||[])[1]) || 10; // corner width
    var re = /round|bevel|notch|bite|cool|sharp|slide|jut|curl|tear|fray|wicked|sculpt|long|dog3|dog2|dog/;
    var fx = ((o.match(re)||['round'])[0]);
    var edges = {
        T:0,
        B:1
    };
    var opts = {
        TL:  /top|tl/.test(o),
        TR:  /top|tr/.test(o),
        BL:  /bottom|bl/.test(o),
        BR:  /bottom|br/.test(o)
    };
    if ( !opts.TL && !opts.TR && !opts.BL && !opts.BR )
        opts = {
            TL:1,
            TR:1,
            BL:1,
            BR:1
        };
    var strip = document.createElement('div');
    strip.style.overflow = 'hidden';
    strip.style.height = '1px';
    strip.style.backgroundColor = sc || 'transparent';
    strip.style.borderStyle = 'solid';
    return this.each(function(index){
        var pad = {
            T: parseInt(jQuery.css(this,'paddingTop'))||0,
            R: parseInt(jQuery.css(this,'paddingRight'))||0,
            B: parseInt(jQuery.css(this,'paddingBottom'))||0,
            L: parseInt(jQuery.css(this,'paddingLeft'))||0
        };

        if (jQuery.browser.msie) this.style.zoom = 1; // force 'hasLayout' in IE
        if (!keep) this.style.border = 'none';
        strip.style.borderColor = cc || gpc(this.parentNode);
        var cssHeight = jQuery.curCSS(this, 'height');

        for (var j in edges) {
            var bot = edges[j];
            strip.style.borderStyle = 'none '+(opts[j+'R']?'solid':'none')+' none '+(opts[j+'L']?'solid':'none');
            var d = document.createElement('div');
            var ds = d.style;

            bot ? this.appendChild(d) : this.insertBefore(d, this.firstChild);

            if (bot && cssHeight != 'auto') {
                if (jQuery.css(this,'position') == 'static')
                    this.style.position = 'relative';
                ds.position = 'absolute';
                ds.bottom = ds.left = ds.padding = ds.margin = '0';
                if (jQuery.browser.msie)
                    ds.setExpression('width', 'this.parentNode.offsetWidth');
                else
                    ds.width = '100%';
            }
            else {
                ds.margin = !bot ? '-'+pad.T+'px -'+pad.R+'px '+(pad.T-width)+'px -'+pad.L+'px' :
                (pad.B-width)+'px -'+pad.R+'px -'+pad.B+'px -'+pad.L+'px';
            }

            for (var i=0; i < width; i++) {
                var w = Math.max(0,getW(i));
                var e = strip.cloneNode(false);
                e.style.borderWidth = '0 '+(opts[j+'R']?w:0)+'px 0 '+(opts[j+'L']?w:0)+'px';
                bot ? d.appendChild(e) : d.insertBefore(e, d.firstChild);
            }
        }
    });
};

jQuery(document).ready(function() {

    if($(".buttonLink").length) {
        jQuery(".buttonLink").live("click", function() {
            // noLoader is for button links that we don't want displaying
            // a loading indicator (such as hover tooltips)
            if(!$(this).hasClass('noLoader')) {
                $(this).addClass("ajaxLoaderSmall");

                jQuery(this).ajaxComplete(function() {
                    $(this).removeClass("ajaxLoaderSmall");
                });
            }
        });
    }


    if($(".taskNavLink").length) {
        jQuery('.taskNavLink').click(function() {
            jQuery('#taskPanel').fadeOut('fast').html('<div class="loading"></div>').fadeIn('fast');
            jQuery('.taskNavLink').removeClass('active');
            jQuery(this).addClass('active');
        });

        jQuery('.taskNavLink').tooltip({
            effect: 'fade',
            position: 'center right',
            opacity: 0.85,
            delay:0,
            predelay:200
        });
    }

    if($(".showTooltip, .grid-view > table.items > tbody > tr > td > a").length) {
        jQuery(".showTooltip, .grid-view > table.items > tbody > tr > td > a").tooltip({
            effect: 'fade',
            position: 'center right',
            opacity: 0.9,
            delay: 0,
            predelay:500,
            offset: [0,10]
        });
    }


    if($(".closeFlash").length) {
        $('.closeFlash').click(function(){
            $(".closeFlash").parent().fadeOut("slow");
            return false;
        });
    }


    if($("#projectFormTabs").length) {
        $("#projectFormTabs").tabs({
            select: function(event, ui) {
                if($("#saved").val()!="true") {
                    if(!confirm("You may have unsaved information on this tab. Press \"Ok\" to continue to the next tab, or press \"Cancel\" to go back and save your information.")) {
                        return false;
                    }
                }
            }
        });
    }

    if($(".IGSTClick").length) {
        jQuery(".IGSTClick").live('click', function() {
            var container = $("#ajaxGuiContainer");
            var loading = $("#loading");

            container.hide('slide', { direction : 'left' });
            loading.addClass('loading').fadeIn('fast');

            IGSTClick(jQuery(this).attr("href"),container,loading);
            return false;
        });
    }

    if($(".IGSTUpdate").length) {
        jQuery(".IGSTUpdate").live('click', function() {
            IGSTUpdate(jQuery(this).attr("href"));
            return false;
        });
    }

    if($(".editReviewers").length) {
        jQuery(".editReviewers").live('click',function() {
            editReviewers(jQuery(this).attr("href"));
            return false;
        });
    }

});

/**
 * Used in a CGridView to edit a project's reviewers.
 */
function editReviewers(url) {
    var urlParams = getUrlVars(url);

    $.ajax({
        url: url.substring(0,url.indexOf('&')),
        global: false,
        type: 'GET',
        data: ({
            projectOID : urlParams['projectOID']
        }),
        dataType: 'html',
        error: function(a,b,c) {
            alert('Error retrieving panel., XMLHttpRequest: '+ a + '; textStatus: '+b+' errorThrown: '+c);
        },
        success: function(data){
            $("#dialogContainerForm").html(data);
            $("#editReviewersDialog").dialog("open");
        }
    });
}

/*
 * Opens form to edit item when edit button is clicked in issues/goals/strategies/tasks
 * section of project under issues tab.
 */
function IGSTUpdate(url) {
    var urlParams = getUrlVars(url);

    $.ajax({
        url: url.substring(0,url.indexOf('&')),
        global: false,
        type: 'GET',
        data: ({
            id : urlParams['id']
        }),
        dataType: 'html',
        error: function(a,b,c) {
            alert('Error retrieving panel., XMLHttpRequest: '+ a + '; textStatus: '+b+' errorThrown: '+c);
        },
        success: function(data){
            $("#dialogContainerForm").html(data);
            $("#addDialog").dialog("open");
        }
    });
}

/*
 * Renders the child panel in the issues/goals/strategies/tasks section of project
 * under the issues tab. Triggered by clicking the view button in the gridview.
 */
function IGSTClick(url,container,loading) {
    var urlParams = getUrlVars(url);

    $.ajax({
        url: url.substring(0,url.indexOf('&')),
        global: false,
        type: 'GET',
        dataType: 'json',
        data: ({
            tableName : urlParams['tableName'],
            parentID : urlParams['parentID'],
            grandparentID : urlParams['grandparentID'],
            action : urlParams['action'],
            idTrail : urlParams['idTrail'],
            ajaxPanel : true
        }),
        error: function(a,b,c) {
            alert('Error retrieving panel., XMLHttpRequest: '+ a + '; textStatus: '+b+' errorThrown: '+c);
        },
        success: function(data){
            loading.fadeOut('fast').removeClass('loading');
            if(data.status=="t") {
                container.empty().html(data.response).show("slide",{ direction: "right" });
            }  else {
                container.empty().html('There was a problem with this request.').show("slide",{ direction: "right" });
            }
            $("#addDialog").remove();
            $('.tooltip').remove();
        }
    });
}
/**
 * Splits up a URL's $_GET params; can be used as
 * url = "http://localhost/somepage.php?GetVar1='Blah'";
 * var urlVars = getUrlVars(url);
 * alert(urlVars['GetVar1']); (alerts 'Blah')
 */
function getUrlVars(url) {
    var map = {};
    var parts = url.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        map[key] = value;
    });
    return map;
}

/**
 * For CGridView updates (binding JS using Yii's system doesn't always work,
 * especially inside CJuiTabs)
 */
function gridButtonClick(url,gridId) {

    var id = url.substring(url.indexOf('id=')+3);

    $.fn.yiiGridView.update(gridId, {
        type:'POST',
        url: url.substring(0,url.indexOf('&')),
        data: {
            'ajaxData':{
                'id':id
            }
        },
        success:function(data, status) {
            $.fn.yiiGridView.update(gridId);
            if(data != '') {
                alert(data);
            }
        }
    });
}

function loadPanel(panelID) {
    var urlHost = document.location.href.substr(0,document.location.href.indexOf('?'));
    var controller = document.location.href.substr(document.location.href.indexOf('r=')+2,
        document.location.href.lastIndexOf('/')-document.location.href.indexOf('r=')-2);

    jQuery.ajax({
        'success': function(data) {
            jQuery('#taskNav #' + panelID).addClass('active');
            $('#taskPanel').html(data).fadeIn('fast');
            location.hash = panelID + 'Panel';
        },
        'url': urlHost + '?r=' + controller + '/renderTaskPanel&panel=' + panelID
    });
}

/* For Google Maps Geocoding */
function loadAPI()
{
    var script = document.createElement("script");
    script.src = "http://www.google.com/jsapi?key=ABQIAAAA1eUnjLfMmXT7VevjJjJRoBTPeaomCSTIHU1og4orDQNfSGDYzBTS_VkhAaV7U0uYhU8-VUZswtCKIA&amp;callback=loadMaps";
    script.type = "text/javascript";
    document.getElementsByTagName("head")[0].appendChild(script);
}

function loadMaps()
{
    //AJAX API is loaded successfully. Now lets load the maps api
    google.load("maps", "3", {other_params : "sensor=false", "callback" : geocodeAddress});
}

// Takes selector for address and returns geocoding
function geocodeAddress() {
        var address = null;

        geocoder = new google.maps.Geocoder();

        address = $("#Location_address_line_1").val()+' '+
                    $("#Location_address_line_2").val()+' '+
                    $("#Location_city").val()+' '+
                    $("#Location_state").val()+' '+
                    $("#Location_zip").val()+' '+
                    $("#Location_country").val();

        if(geocoder) {
            geocoder.geocode( {'address': address}, function(results, status) {
                if(status == google.maps.GeocoderStatus.OK) {
                    $("#Location_latlng").val(results[0].geometry.location);
                    addLocation();
                    return true;
                } else {
                    alert("Geocode was not successful for the following reason: " + status);
                    return false;
                }
            });
        } else {
            alert("Failure initializing geocoder.");
            return false;
        }
    }

function addLocation() {
    $.ajax({
        url: $("a#addLocationSubmit").attr("href"),
        global: false,
        data: $("form").serialize(),
        type: 'POST',
        dataType: 'json',
        error: function(xhr) {
            alert('Sorry, there was an error. '.xhr.statusText);
        },
        success : function(data) {
            if(data.status=="f") {
                $("#ajaxResponse").html(data.response).fadeIn();
                $(".buttonLink").removeClass("ajaxLoaderSmall");
            } else if(data.status=="t") {
                $("#locationsList").html(data.response);
                $("#addLocationContainer").slideToggle();
                $(".buttonLink").removeClass("ajaxLoaderSmall");
            }
        }
    });
}