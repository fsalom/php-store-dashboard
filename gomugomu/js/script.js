/**
 * Parallax Scrolling Tutorial
 * For Smashing Magazine
 * July 2011
 *   
 * Author: Richard Shepherd
 * 		   www.richardshepherd.com
 * 		   @richardshepherd   
 */

// On your marks, get set...
$(document).ready(function(){
						
	// Cache the Window object
	$window = $(window);
	
	// Cache the Y offset and the speed of each sprite
	$('[data-type]').each(function() {	
		$(this).data('offsetY', parseInt($(this).attr('data-offsetY')));
		$(this).data('Xposition', $(this).attr('data-Xposition'));
		$(this).data('speed', $(this).attr('data-speed'));
	});
	
	// For each element that has a data-type attribute
	$('section[data-type="background"]').each(function(){
	
	
		// Store some variables based on where we are
		var $self = $(this),
			offsetCoords = $self.offset(),
			topOffset = offsetCoords.top;
		
		// When the window is scrolled...
	    $(window).scroll(function() {
	
			// If this section is in view
			if ( ($window.scrollTop() + $window.height()) > (topOffset) &&
				 ( (topOffset + $self.height()) > $window.scrollTop() ) ) {
	
				// Scroll the background at var speed
				// the yPos is a negative value because we're scrolling it UP!								
				var yPos = -($window.scrollTop() / $self.data('speed')); 
				
				// If this element has a Y offset then add it on
				if ($self.data('offsetY')) {
					yPos += $self.data('offsetY');
				}
				
				// Put together our final background position
				var coords = '50% '+ yPos + 'px';

				// Move the background
				$self.css({ backgroundPosition: coords });
				
				// Check for other sprites in this section	
				$('[data-type="sprite"]', $self).each(function() {
					
					// Cache the sprite
					var $sprite = $(this);
					
					// Use the same calculation to work out how far to scroll the sprite
					var yPos = -($window.scrollTop() / $sprite.data('speed'));					
					var coords = $sprite.data('Xposition') + ' ' + (yPos + $sprite.data('offsetY')) + 'px';
					
					$sprite.css({ backgroundPosition: coords });													
					
				}); // sprites
			
				// Check for any Videos that need scrolling
				$('[data-type="video"]', $self).each(function() {
					
					// Cache the video
					var $video = $(this);
					
					// There's some repetition going on here, so 
					// feel free to tidy this section up. 
					var yPos = -($window.scrollTop() / $video.data('speed'));					
					var coords = (yPos + $video.data('offsetY')) + 'px';
	
					$video.css({ top: coords });													
					
				}); // video	
			
			}; // in view
	
		}); // window scroll
			
	});	// each data-type

}); // document ready



(function($) {

  
  $.fn.Scrollable = function(settings) {
   
     var config = { threshold: -100, offset_scroll: 6, offset_intertia: .15 };
 
     if (settings) $.extend(config, settings);
    
     this.each(function() { 
      
        var $self = $(this),
            $id = $self.attr('id');
            
        config.threshold = 0
        
        if ($.Mobile || $.Unsupported) {  
          $self.css({backgroundAttachment:'scroll'})
        }else{
        
        $.Window
          .bind('scroll',
            function(e){
            
              if ( $.inview($self,{threshold:config.threshold}) ) {
                
                if (!$self.hasClass('_active')){
                
                  $self.addClass('_active');
                  
                  if (config.is_nav)
                    $.Body.triggerHandler($.Events.SECTION_ENTER,$id);
                  
                  $self.triggerHandler($.Events.SCROLL_ENTER);
                  
                }
                  
                _scroll_background();
                  
                $self.triggerHandler($.Events.SCROLL,$.distancefromfold($self,{threshold:config.threshold}) - config.threshold)
                
              }else{
                
                if ($self.hasClass('_active')){
                
                  $self.removeClass('_active');
                  
                  $self.triggerHandler($.Events.SCROLL_LEAVE);
                  
                }
              
              }
              
            
            })
            
            
        }
        
        function _scroll_background() {

          var _x = '50% '
                  
          var bpos = _x + (-($.distancefromfold($self,{threshold:config.threshold}) - config.threshold) * config.offset_intertia) + 'px';
          
          $self.css({'backgroundPosition':bpos})

        }
        
        /*if (config.auto_scroll)
          _scroll_background();*/
            
     });
     
    return this;
     
  } //Story


  $.fn.StoryFreeXT = function() {
   
     this.each(function() { 
      
        var $self = $(this),
            $header = $self.find('header'),
            $fb = $self.find('li.facebook-hover'), // facebook
	        $fbShare = $self.find('.fb-share'), // facebook share
	        $twitter = $self.find('li.twitter-hover'), // twitter
	        $closeFB = $self.find('.close-fb'),
	        $closeTwitter = $self.find('.close-twitter'),
            $bg = $self.find('.bg'),
            $h1 = $self.find('h1'),
            $h2 = $self.find('h2'),
            $id = $self.attr('id'),
            $img = $self.find('img'),
            
            _threshold = -200;
        
        $self
          .Scrollable({threshold: _threshold,is_nav:true})
          .bind($.Events.SCROLL,on_scroll)
          .bind($.Events.SCROLL_ENTER,on_scroll_enter)
          .bind($.Events.SCROLL_LEAVE,on_scroll_leave);
        
        $fb
          .bind('mouseenter',
            function(e) {
        	  $fb.find('span').show();
        	  $fb.css('backgroundPosition','bottom');
        	  $twitter.find('span').hide();
        	  $twitter.css('backgroundPosition','top');
          });
        
        if (!$.Mobile) {
	        $fb
	          .bind('mouseleave',
	            function(e) {
	        	  $(this).find('span').hide();
        		  $(this).css('backgroundPosition','top');
	        });	        	
        } 	        	 
        
        $fbShare
          .bind('click',
            function(e) {
              var _fb = window.open('http://www.facebook.com/sharer.php?s=100&p[url]=http://nikebetterworld.com/about&p[title]=Nike Better World&p[images][0]=http://www.nike.com/betterworld/images/nbw_facebook.jpg&p[summary]=We will not rest until every living, breathing person on this planet has access to sport.' ,'_fb','width=550,height=450')	              
              _centerPopup(_fb)
              $.Body.triggerHandler($.Events.OMNITURE_TRACK,'facebook_site')	      
              e.preventDefault();
          });	        
        
        $closeFB
          .bind('click',
            function(e) {
        	  $fb.find('span').hide();
        	  $fb.css('backgroundPosition','top');
          });
        
        $closeTwitter
          .bind('click',
            function(e) {
        	  $twitter.find('span').hide();
        	  $twitter.css('backgroundPosition','top');
          });	        
        
        $twitter
          .bind('mouseenter',
            function(e) {
        	  $(this).find('span').show();
        	  $(this).css('backgroundPosition','bottom');
        	  $fb.find('span').hide();
        	  $fb.css('backgroundPosition','top');
          });	 	        
        
        if (!$.Mobile) {
	        $twitter
	          .bind('mouseleave',
	            function(e) {
	        	  $(this).find('span').hide();
        		  $(this).css('backgroundPosition','top');	       
	        });	        
        }
        
        function _centerPopup(_win) {
       
        _win.focus();
        
        _win.moveTo($(window).width()/2 - 275, $(window).height()/2 - 225)
       
       }
          
        function on_scroll(e,distance) {
        
          var bpos = '50% ' + ($.Window.height()/2.5-distance/3) + 'px';
                  
          $bg.css({'backgroundPosition':bpos})
          
        }
        
        function on_scroll_enter(e) {
        
        }
        
        function on_scroll_leave(e) {
        
        }
        
        
            
     });
     
    return this;
     
  } //StoryFreeXT
})(jQuery);