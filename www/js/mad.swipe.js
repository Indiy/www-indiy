
(function($, document){

    var methods = {
        init: function(opts){

            // Extend default options
            var options = $.extend({}, $.fn.swipe.defaults, opts);

            //
            // append swipe to selected overflowed containers and return jquery object for chainability
            //
            return this.each(function(){

                var container = $(this);

                    // properties
                var props = {
                        arrows: options.arrows
                    };

                this.swipe = new $.fn.swipe.swipe(container, props, options);

                // build HTML, initialize Handle and append Events
                this.swipe.setupHtml();

                // callback function after creation of swipe
                if(typeof fn === "function"){
                    fn(container.find(".swipe-pane"), this.swipe);
                }
            });
        },


        // repaint the height and position of the scroll handle
        //
        // this method must be called in case content is added or reoved from the container.
        //
        // usage:
        //   $('selector').swipe("repaint");
        //
        repaint: function(){
            return this.each(function(){
                if(this.swipe) {
                  this.swipe.repaint();
                }
            });
        },


        // scroll to a specific item within the container or to a specific distance of the content from top
        //
        // usage:
        //   $('selector').swipe("scrollto");                    // scroll to top of content
        //   $('selector').swipe("scrollto", 20);                // scroll to content 20px from top
        //   $('selector').swipe("scrollto", "top");             // scroll to top of content
        //   $('selector').swipe("scrollto", "middle");          // scroll to vertically middle of content
        //   $('selector').swipe("scrollto", "bottom");          // scroll to bottom of content
        //   $('selector').swipe("scrollto", $('item'));         // scroll to first content item identified by selector $('item')
        //
        scrollto: function(to){
            return this.each(function(){
                this.swipe.scrollto(to);
            });
        },
        
        // Remove the swipe (and the generated HTML elements).
        //
        // usage:
        //   $('selector').swipe("unswipe");
        //
        unswipe: function() {
          return this.each(function() {
            if(this.swipe) {
              this.swipe.unswipe();
            }
          });
        }
    }

    $.fn.swipe = function(method){
        if(methods[method]){
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        }
        else if(typeof method === "function" || method === undefined){
            return methods.init.apply(this, arguments);
        }
        else if(typeof method === "object"){
            return methods.init.apply(this, [null, method]);
        }
        else {
            $.error("method '" + method + "' does not exist for $.fn.swipe");
        }
    }



    //
    // default options
    //
    $.fn.swipe.defaults = {
        containerHeight : 'auto',
        panelCount: 1,
        resizeCallback: function() {},
        onPanelChange: function() {}
    };



    //
    // swipe constructor
    //
    $.fn.swipe.swipe = function(container, props, options) {
        // set object properties
        this.container = container;
        this.props = props;
        this.opts = options;
        this.mouse = {};
    };

    //
    // swipe methods
    //
    $.fn.swipe.swipe.prototype = {

        setupHtml: function(){

            // set swipe-object properties
            this.container.bind('mousewheel.madsw',$.proxy(this, 'onMouseWheel'));
            this.container.bind('mousedown.madsw',$.proxy(this, 'onMouseDown'));
            $(window).bind('mouseup.madsw',$.proxy(this, 'onMouseUp'));
            $(window).bind('mousemove.madsw',$.proxy(this, 'onMouseMove'));
            
            this.panelIndex = 0;
            this.refreshHtml();
            
            $(window).resize($.proxy(this, 'onContainerResize'));

            return this;
        },
        refreshHtml: function() {
            this.contentWidth = this.container.width();
        },
        onContainerResize: function() {
            console.log("onContainerResize");
            this.opts.resizeCallback();
            this.refreshHtml();
        },

        setContentPositionIndex: function(percent){
            var visible_height = this.pane.height();
            var content_range = this.contentHeight - visible_height;
            
            var new_top = content_range * percent;
            //console.log("setContentPositionPercent: " + percent + ", content_range: " + content_range + ", new_top: " + new_top);
            this.setContentPositionPx(new_top);
        },
        setContentPositionPx: function(new_top){
            //console.log("setContentPositionPx: new_top: " + new_top);
            this.pane.scrollTop(new_top);
        },
        
        //
        // repaint swipe height and position
        //
        repaint: function(){
            this.refreshHtml();
        },


        //
        // scroll to a specific distance from the top
        //
        scrollto: function(index) {
            this.panelIndex = index;
        },
        
        //
        // Remove swipe dom elements
        //
        unswipe: function() {
            this.pane.css({overflow: 'auto'});
            this.handleContainer.hide();
        },
    
        onMouseWheel: function(ev, delta, deltaX, deltaY) {
            //console.log("onMouseWheel: ev: " + ev + ",delta: " + delta + ", dX: " + deltaX + ", dY: " + deltaY);
            ev.preventDefault();
        },
        onMouseDown: function(ev, delta, deltaX, deltaY) {
            this.mouseDown = true;
            this.moveStart = ev.pageX;
            this.scrollLeftStart = this.container.scrollLeft();
            console.log("mousedown: pageX: " + ev.pageX + ", scrollLeftStart: " + this.scrollLeftStart);
            ev.preventDefault();
        },
        onMouseUp: function(ev, delta, deltaX, deltaY) {
            if( this.mouseDown )
            {
                console.log("mouseup: " + ev);
                ev.preventDefault();
            }
            this.mouseDown = false;
        },
        onMouseMove: function(ev, delta, deltaX, deltaY) {
            if( this.mouseDown )
            {
                var delta = this.moveStart - ev.pageX;
                var new_left = this.scrollLeftStart + delta;

                console.log("mousemove: pageX: " + ev.pageX + ", newLeft: " + new_left + ", delta: " + delta);

                this.container.scrollLeft(new_left);
                ev.preventDefault();
            }
        },
        
    };
    
})(jQuery, document);  // inject global jQuery object
