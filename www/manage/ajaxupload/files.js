$(function ()
{
	$('form#ajax_from').iframePostForm
	({
		json : true,
		post : function ()
		{
			var message;
			
			if (!$('.message').length)
			{
				$('#demonstrations').after('<div class="message" style="display:none; padding:10px; text-align:center" />');
			}
			
			
			if ($('input[type=file]').val().length || $('#song_id').val()!='')
			{
				$('.message')
					.html('Uploading file&hellip;')
					.css({
						color : '#006100',
						background : '#c6efce',
						border : '2px solid #006100'
					})
					.slideDown();
			}
			
			else
			{
				$('.message')
					.html('Please select an image for uploading.')
					.css({
						color : '#9c0006',
						background : '#ffc7ce',
						border : '2px solid #9c0006'
					})
					.slideDown();
				
				return false;
			}
		},
		complete : function (response)
		{
			var style,
				width,
				html = '';
			
			
			if (!response.success)
			{
				$('.message').slideUp(function ()
				{
					$(this)
						.html('There was a problem with the image you uploaded')
						.css({
							color : '#9c0006',
							background : '#ffc7ce',
							borderColor : '#9c0006'
						})
						.slideDown();
				});
			}
			
			else
			{
				//html += '<p>Below is the uploaded image and the values that were posted when you submitted the demonstration form.</p>';
				if (response.postedValues)
				{
					for (title in response.postedValues)
					{
						if(title!="submit" || title!="PHPSESSID")
						;//html += '<strong>' + title + ':</strong> ' + response.postedValues[title] + '<br />';
					}
				}
				if (response.imageSize)
				{
					width = response.imageSize[0] > 500 ? 500 : response.imageSize[0];
				}
				if (response.imageSource)
				{
					//html += '<img src="' + response.imageSource + '" width="250" id="image" alt="Your uploaded image" />';
				}
				
				$('.message').slideUp(function ()
				{
					$(this)
						.html(html)
						.css({
							color : '#006100',
							background : '#c6efce',
							borderColor : '#006100'
						})
						.slideDown();
				});
				if (response.audio_sound)
				{
						$('.message').after('<div id="jquery_jplayer_1" class="jp-jplayer"></div>		<div id="jp_container_1" class="jp-audio">			<div class="jp-type-single">				<div class="jp-gui jp-interface">					<ul class="jp-controls">						<li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>						<li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>						<li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>						<li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>						<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>						<li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>					</ul>					<div class="jp-progress">						<div class="jp-seek-bar">							<div class="jp-play-bar"></div>						</div>					</div>					<div class="jp-volume-bar">						<div class="jp-volume-bar-value"></div>					</div>					<div class="jp-time-holder">						<div class="jp-current-time"></div>						<div class="jp-duration"></div>						<ul class="jp-toggles">							<li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li>							<li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li>						</ul>					</div>				</div>		</div>		</div>');
						$.getScript('js/jquery.jplayer.min.js', function() {
							
						$("#jquery_jplayer_1").jPlayer({
								ready: function (event) {	
									$(this).jPlayer("setMedia", {
										mp3:response.audio_sound
									});
								
								},
								swfPath: "js",
								supplied: "mp3",
								wmode: "window"
							});
						});
				}
				if (response.video_sound)
				{
						$('.message').after('<div id="jquery_jplayer_1" class="jp-jplayer"></div>		<div id="jp_container_1" class="jp-audio">			<div class="jp-type-single">				<div class="jp-gui jp-interface">					<ul class="jp-controls">						<li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>						<li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>						<li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>						<li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>						<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>						<li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>					</ul>					<div class="jp-progress">						<div class="jp-seek-bar">							<div class="jp-play-bar"></div>						</div>					</div>					<div class="jp-volume-bar">						<div class="jp-volume-bar-value"></div>					</div>					<div class="jp-time-holder">						<div class="jp-current-time"></div>						<div class="jp-duration"></div>						<ul class="jp-toggles">							<li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li>							<li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li>						</ul>					</div>				</div>		</div>		</div>');
						$.getScript('js/jquery.jplayer.min.js', function() {
							
						$("#jquery_jplayer_1").jPlayer({
								ready: function (event) {	
									$(this).jPlayer("setMedia", {
										mp3:response.video_sound
									});
								
								},
								swfPath: "js",
								supplied: "mp3",
								wmode: "window"
							});
						});
				}
				$("#form_field").hide();
				$("#form_message").show();
			}
		}
	});
});