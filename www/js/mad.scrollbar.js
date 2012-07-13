
(function($, document){

    var methods = {
        init: function(fn,opts){

            // Extend default options
            var options = $.extend({}, $.fn.scrollbar.defaults, opts);

            //
            // append scrollbar to selected overflowed containers and return jquery object for chainability
            //
            return this.each(function(){

                var container = $(this);

                    // properties
                var props = {
                        arrows: options.arrows
                    };

                this.scrollbar = new $.fn.scrollbar.Scrollbar(container, props, options);

                // build HTML, initialize Handle and append Events
                this.scrollbar.setupHtml();

                // callback function after creation of scrollbar
                if(typeof fn === "function"){
                    fn(container.find(".scrollbar-pane"), this.scrollbar);
                }
            });
        },


        // repaint the height and position of the scroll handle
        //
        // this method must be called in case content is added or reoved from the container.
        //
        // usage:
        //   $('selector').scrollbar("repaint");
        //
        repaint: function(){
            return this.each(function(){
                if(this.scrollbar) {
                  this.scrollbar.repaint();
                }
            });
        },


        // scroll to a specific item within the container or to a specific distance of the content from top
        //
        // usage:
        //   $('selector').scrollbar("scrollto");                    // scroll to top of content
        //   $('selector').scrollbar("scrollto", 20);                // scroll to content 20px from top
        //   $('selector').scrollbar("scrollto", "top");             // scroll to top of content
        //   $('selector').scrollbar("scrollto", "middle");          // scroll to vertically middle of content
        //   $('selector').scrollbar("scrollto", "bottom");          // scroll to bottom of content
        //   $('selector').scrollbar("scrollto", $('item'));         // scroll to first content item identified by selector $('item')
        //
        scrollto: function(to){
            return this.each(function(){
                this.scrollbar.scrollto(to);
            });
        },
        
        // Remove the scrollbar (and the generated HTML elements).
        //
        // usage:
        //   $('selector').scrollbar("unscrollbar");
        //
        unscrollbar: function() {
          return this.each(function() {
            if(this.scrollbar) {
              this.scrollbar.unscrollbar();
            }
          });
        }
    }

    $.fn.scrollbar = function(method){
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
            $.error("method '" + method + "' does not exist for $.fn.scrollbar");
        }
    }



    //
    // default options
    //
    $.fn.scrollbar.defaults = {
        containerHeight     : 'auto', // height of content container [Number in px || 'auto']. If set to 'auto', the naturally rendered height is used.
        arrows              : true,   // render up- and down-arrows [true || false].
        handleHeight        : 'auto', // height of handle [Number in px || 'auto']. If set to 'auto', the height will be calculated proportionally to the container-content height.
        handleMinHeight     : 30,     // minimum height of handle [Number in px]. This property will only be used if handleHeight-option is set to 'auto'.
        scrollTimeout       : 50,     // timeout of handle speed while mousedown on arrows [Number in milli sec].
        scrollStep          : 20,     // increment of handle position between two mousedowns on arrows [Number in px].
        scrollTimeoutArrows : 40,     // timeout of handle speed while mousedown in the handle container [Number in milli sec].
        scrollStepArrows    : 3,       // increment of handle position between two mousedowns in the handle container [px].
        measureTag          : false
    };



    //
    // Scrollbar constructor
    //
    $.fn.scrollbar.Scrollbar = function(container, props, options) {
        // set object properties
        this.container = container;
        this.props = props;
        this.opts = options;
        this.mouse = {};
    };

    //
    // Scrollbar methods
    //
    $.fn.scrollbar.Scrollbar.prototype = {

        setupHtml: function(){

            // set scrollbar-object properties
            this.pane = this.container.find('.scrollbar-pane');
            this.handle = this.container.find('.scrollbar-handle');
            this.handleContainer = this.container.find('.scrollbar-handle-container');
            this.handleSupport = this.container.find('.scrollbar-handle-suppport');

            this.pane.css({overflow: 'hidden'});
            
            this.handleContainer.bind('click.madsb',$.proxy(this, 'onHandleContainerClick'));

            var opts = {
                'axis': "y",
                'containment': "parent",
                'drag': $.proxy(this, 'onHandleDrag'),
                'dragstop': $.proxy(this, 'onHandleDragStop')
            };
            this.handle.draggable(opts);
            this.pane.bind('mousewheel.madsb',$.proxy(this, 'onMouseWheel'));
            
            
            this.refreshHtml();
            
            $(window).resize($.proxy(this, 'onPaneResize'));

            return this;
        },
        refreshHtml: function() {
            if( this.opts.measureTag == false )
            {
                this.contentHeight = $.fn.scrollbar.contentHeight(this.pane);
            }
            else
            {
                this.contentHeight = $(this.opts.measureTag).height();
            }
            this.setHandle();
        },
        onPaneResize: function() {
            this.refreshHtml();
        },

        //
        // calculate dimensions of handle
        //
        setHandle: function(){
            var visible_height = this.pane.height();
            if( visible_height >= this.contentHeight )
            {
                this.handleContainer.hide();
                this.handleSupport.hide();
                return;
            }
            
            this.handleContainer.show();
            this.handleSupport.show();

            var handle_container_height = this.handleContainer.height();
            var handle_height = Math.ceil(visible_height * handle_container_height / this.contentHeight);
            
            this.handle.height(handle_height);

            // This is post CSS min-height application
            handle_height = this.handle.height();

            var top = this.pane.scrollTop();
            var content_range = this.contentHeight - visible_height;
            
            var content_percent = top / content_range;
            var handle_range = handle_container_height - handle_height;
            var handle_top = handle_range * content_percent;
            
            this.handle.css({ top: handle_top });
            return this;
        },
        setContentPositionPercent: function(percent){
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
        // repaint scrollbar height and position
        //
        repaint: function(){
            this.refreshHtml();
        },


        //
        // scroll to a specific distance from the top
        //
        scrollto: function(to){

            var distance = 0;

            if(typeof to == "number"){
                distance = (to < 0 ? 0 : to) / this.props.handleContentRatio;
            }
            else if(typeof to == "string"){
                if(to == "bottom"){
                    distance = this.props.handlePosition.max;
                }
                if(to == "middle"){
                    distance = Math.ceil(this.props.handlePosition.max / 2);
                }
            }
            else if(typeof to == "object" && !$.isPlainObject(to)) {
                distance = Math.ceil(to.position().top / this.props.handleContentRatio);
            }

            this.setContentPositionPx(distance);
            this.setHandlePosition();
        },
        
        //
        // Remove scrollbar dom elements
        //
        unscrollbar: function() {
            this.pane.css({overflow: 'auto'});
            this.handleContainer.hide();
        },
    

        onHandleDrag: function(ev) {
            var handle_container_height = this.handleContainer.height();
            var handle_height = this.handle.height();
            var handle_container_top = this.handleContainer.offset().top;
            var handle_top = this.handle.offset().top;
            
            var handle_pos = handle_top - handle_container_top;
            var handle_range = handle_container_height - handle_height;

            var progress_percent = handle_pos / handle_range;
            
            this.setContentPositionPercent(progress_percent);
        },
        
        onHandleDragStop: function(ev) { onHandleDrag(ev); },

        onHandleContainerClick: function(ev) {
            var height = this.handleContainer.height();
            var bar_top = this.handleContainer.offset().top;
            var click_top = ev.pageY;

            var y = click_top - bar_top;
            var progress_percent = y / height;

            //console.log("click progress_percent: " + progress_percent);
        },
        
        onMouseWheel: function(ev, delta, deltaX, deltaY) {
            // calculate new handle position
            var old_top = this.pane.scrollTop();
            var new_top = old_top - deltaY * 30;

            //console.log("onMouseWheel: " + delta + ", dX: " + deltaX + ", dY: " + deltaY + ", new_top: " + new_top);
            
            this.setContentPositionPx(new_top);
            this.setHandle();

            new_top = this.pane.scrollTop();

            if( old_top != new_top )
                ev.preventDefault();
        },
        
        //
        // do not bubble down to avoid triggering click events attached within the container
        //
        preventClickBubbling: function(ev){
            ev.stopPropagation();
        }
    };

    // ----- helpers ------------------------------------------------------------------------------

    //
    // determine content height
    //
    $.fn.scrollbar.contentHeight = function(container){
      var wrapper = container.wrapInner('<div/>').find(':first');
      var height = wrapper.css({overflow:'hidden'}).height();
      wrapper.replaceWith(wrapper.contents());
      return height;
    };

    
})(jQuery, document);  // inject global jQuery object
