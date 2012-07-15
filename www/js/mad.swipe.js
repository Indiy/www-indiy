
(function($, document){

    var methods = {
        init: function(fn,opts){

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
        onPanelChange: function() {},
        onPanelVisible: function() {},
        onReady: function() {},
        overFlowRatio: 0.4
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

            this.pad = this.container.find('.pad');
            this.clickCatch = this.container;
            
            this.clickCatch.bind('mousewheel.madsw',$.proxy(this, 'onMouseWheel'));
            this.clickCatch.bind('mousedown.madsw',$.proxy(this, 'onMouseDown'));
            $(window).bind('mouseup.madsw',$.proxy(this, 'onMouseUp'));
            $(window).bind('mousemove.madsw',$.proxy(this, 'onMouseMove'));

            this.clickCatch.bind('touchstart.madsw',$.proxy(this, 'onTouchStart'));
            this.clickCatch.bind('touchmove.madsw',$.proxy(this, 'onTouchMove'));
            this.clickCatch.bind('touchend.madsw',$.proxy(this, 'onTouchEnd'));
            
            this.panelIndex = 0;
            this.refreshHtml();
            
            $(window).resize($.proxy(this, 'onContainerResize'));
            
            this.opts.onReady();
            
            return this;
        },
        refreshHtml: function() {
            this.contentWidth = this.container.width();
            this.overflow = Math.floor(this.contentWidth * this.opts.overFlowRatio);
            this.pad.width(this.overflow);
            var left = this.panelIndex * this.contentWidth + this.overflow;
            this.container.scrollLeft(left);
        },
        onContainerResize: function() {
            this.container.stop(true);
            //console.log("onContainerResize");
            this.opts.resizeCallback();
            this.refreshHtml();
        },

        onAnimateComplete: function()
        {
            this.opts.onPanelChange(this.panelIndex);
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
            var left = index * this.contentWidth;
            left += this.overflow;
            
            var opts = {
                complete: $.proxy(this, 'onAnimateComplete')
            };
            
            this.container.animate({ scrollLeft: left },opts);
        },
        
        //
        // Remove swipe dom elements
        //
        unswipe: function() {
        },
    
        onMouseWheel: function(ev, delta, deltaX, deltaY) {
            //console.log("onMouseWheel: ev: " + ev + ",delta: " + delta + ", dX: " + deltaX + ", dY: " + deltaY);
            ev.preventDefault();
        },
        onMouseDown: function(ev, delta, deltaX, deltaY) {
            this.container.stop(true);
            this.mouseDown = true;
            this.startMoveX = ev.pageX;
            this.scrollLeftStart = this.container.scrollLeft();
            //console.log("mousedown: pageX: " + ev.pageX + ", scrollLeftStart: " + this.scrollLeftStart);
            ev.preventDefault();
        },
        onMouseMove: function(ev, delta, deltaX, deltaY) {
            if( this.mouseDown )
            {
                ev.preventDefault();
                this.handleMove(ev.pageX);
            }
        },
        onMouseUp: function(ev, delta, deltaX, deltaY) {
            if( this.mouseDown )
            {
                ev.preventDefault();
                
                handleMoveDone();
                return;
                
                var left = this.container.scrollLeft();
                left -= this.overflow;
                var right = left + this.contentWidth;
                
                var left_index = Math.round(left / this.contentWidth);

                this.scrollto(left_index);
            }
            this.mouseDown = false;
        },
        onTouchStart: function(je) {
            je.preventDefault();
            this.container.stop(true);
            
            var ev = je.originalEvent;
            var touch = ev.touches[0];
            this.startMoveX = touch.screenX;
            this.scrollLeftStart = this.container.scrollLeft();
            this.startTime = Number(new Date());
            
            console.log("onTouchStart: screenX: " + touch.screenX + ", clientX: " + touch.screenX + ", pageX: " + touch.pageX);
        },

        onTouchMove: function(je) {
            je.preventDefault();

            var ev = je.originalEvent;
            if(ev.touches.length > 1 || ev.scale && ev.scale !== 1) 
                return;
            
            var touch = ev.touches[0];
            this.handleMove(touch.screenX);
        },
        onTouchEnd: function(je) {
            je.preventDefault();
            var ev = je.originalEvent;

            var left = this.container.scrollLeft();
            left -= this.overflow;
            var right = left + this.contentWidth;
            
            var left_index = Math.round(left / this.contentWidth);

            this.scrollto(left_index);
            
            return;

            var isValidSlide = 
                  Number(new Date()) - this.start.time < 250      // if slide duration is less than 250ms
                  && Math.abs(this.deltaX) > 20                   // and if slide amt is greater than 20px
                  || Math.abs(this.deltaX) > this.width/2;        // or if slide amt is greater than half the width

            var isPastBounds = 
                  !this.index && this.deltaX > 0                          // if first slide and slide amt is greater than 0
                  || this.index == this.length - 1 && this.deltaX < 0;    // or if last slide and slide amt is less than 0

            if( !this.isScrolling ) {
                this.slide( this.index + ( isValidSlide && !isPastBounds ? (this.deltaX < 0 ? 1 : -1) : 0 ), this.speed );
            }
        },

        handleMove: function(newX) {
            var deltaX = newX - this.startMoveX;
            
            var resistance = 1;
            /*
            if( ( this.panelIndex == 0 && deltaX < 0 )
               || this.panelIndex == this.panelCount - 1 && deltaX < 0 )
                resistance = Math.abs(deltaX) / this.contentWidth + 1;
            */
            deltaX = deltaX / resistance;
            
            var new_left = this.scrollLeftStart - deltaX;
            this.container.scrollLeft(new_left);
            
            var left = this.container.scrollLeft();
            left -= this.overflow;

            var right = left + this.contentWidth;
            var left_index = Math.floor(left / this.contentWidth);
            var right_index = Math.floor(right / this.contentWidth);
            
            if( left_index >= 0 )
                this.opts.onPanelVisible(left_index);
            if( right_index < this.opts.panelCount )
                this.opts.onPanelVisible(right_index);
        },
        handleMoveDone: function() {
            
            var left = this.container.scrollLeft();
            var deltaSL = this.startScrollLeft - left;
            var deltaT = Number(new Date()) - this.startTime;
            
            var isLeft = true;
            if( deltaSL > 0 )
                isLeft = false;
            
            var isValidSlide = false;
            if( deltaSL > this.contentWidth / 2 )
                isValidSlide = true;
            if( deltaT < 250 && Math.abs(deltaSL) > 20 )
                isValidSlide = true;
            
            if( isLeft && this.panelIndex == 0 )
                isValidSlide = false;
            
            if( !isLeft && this.panelIndex == this.panelCount - 1 )
                isValidSlide = false;
            
            if( isValidSlide )
            {
                var new_index = this.panelIndex;
                if( isLeft )
                    new_index--;
                else
                    new_index++;
                this.sctollto(new_index);
            }
            else
            {
                var left = index * this.contentWidth;
                left += this.overflow;
                this.container.animate({ scrollLeft: left });
            }
        }
    };
    
})(jQuery, document);  // inject global jQuery object
