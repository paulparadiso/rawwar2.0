//todo: volume
//todo: fullscreen

const RW_PLAYING = 1;
const RW_PAUSED = 0;
//const RW_DRAGGING = -1;

jQuery(document).ready(function($) {
	if (window.RwPlayer === undefined) {
		window.rwPlayers = [];

		window.RwPlayer = function(post_id,video_data) {
			this.setUpVideo = function() {
				var rwp = this;
				var duration = rwp.player.getDuration();
				if (duration > 0 && duration !== undefined) {
					rwp.duration = duration;
				}
				//todo: specify start seconds
				$('.video_time',rwp.container).html('0:00 / ' + secondsToTimeString(this.duration));

				rwp.player.addEventListener("onStateChange", "window.rwPlayers[" + this.post_id + "].onPlayerStateChange");
				rwp.player.cueVideoById(rwp.foreign_key,0,'hd720');
				
				var play_marker = $('.play_marker',rwp.container);
				play_marker.draggable({ axis: 'x', containment: 'parent', cursor: 'pointer',
					drag: function(event,ui) {
						rwDragPlayer(rwp.post_id,event);
					},
					stop: function(event,ui) {
						rwStopDraggingPlayer(rwp.post_id,event);
					}
				});

				$('.in_marker',this.container).draggable({ axis: 'x', containment: 'parent', cursor: 'pointer',
					drag: function(event,ui) {
						rwDragClipIn(rwp.post_id,event);
					}
				});
				
				$('.out_marker',this.container).draggable({ axis: 'x', containment: 'parent', cursor: 'pointer',
					drag: function(event,ui) {
						rwDragClipOut(rwp.post_id,event);
					}
				});
				this.calcMarkerConstraints();
				
				$('.play_button',rwp.container).click(function(event) {
					rwTogglePlay(rwp.post_id);
				});
				$('.newclip_button',rwp.container).click(function(event) {
					rwToggleEditClip(rwp.post_id);
				});
				
				var rwp = this;
				$('.start_button',rwp.container).click(function(event) {
					rwp.rewind();
				});
				$('.end_button',rwp.container).click(function(event) {
					rwp.ffwd();
				});
				
			}
			
			this.rewind = function() {
				var currentTime = this.player.getCurrentTime();
				var new_position;
				if (this.clip_start === undefined || currentTime <= this.clip_start) {
					new_position = 0;
				} else {
					new_position = this.clip_start;
				}

				if (this.player.getPlayerState() > -1) {  //not unstarted
					new_position -= 1; //fudge factor
				}
				this.player.seekTo(new_position,true);
			}
			
			this.ffwd = function() {
				var currentTime = this.player.getCurrentTime();
				var new_position;
				if (currentTime >= this.clip_end || !this.editingClip) {
					new_position = this.duration;
				} else {
					new_position = this.clip_end;
				}

				this.player.seekTo(new_position,true);
			}
			
			this.stopDraggingVideo = function(event) {
				var play_marker_range = $('.play_marker_range',this.container);
				var new_position = event.clientX - play_marker_range.offset().left;
				new_position /= (play_marker_range.width() - 14);

				new_position *= this.duration;
				this.player.seekTo(new_position,true);

				if (this.playState == RW_PLAYING) {
					this.player.playVideo();
				} else {
					this.player.pauseVideo();
				}
				this.dragging = false;
			}
			
			this.dragVideo = function(event) {
				this.dragging = true;
				//if (this.player.getPlayerState() == 1) {
					this.player.pauseVideo();
				//}

				//todo: do these in object
				var bytesLoaded = this.player.getVideoBytesLoaded();
				var bytesTotal = this.player.getVideoBytesTotal();
				var startBytes = this.player.getVideoStartBytes();
				
				//update for messed up bytes results. todo: make more sense of this
				if (startBytes + bytesLoaded >= bytesTotal) {
					startBytes = bytesTotal - bytesLoaded;
				}
				var start_fraction = (bytesTotal ? startBytes / bytesTotal : 0);
				var end_fraction = (bytesTotal ? (startBytes + bytesLoaded) / bytesTotal : 0);
				
				var play_marker_range = $('.play_marker_range',this.container);
				var new_position = event.clientX - play_marker_range.offset().left;
				new_position /= (play_marker_range.width() - 14);

				var duration = this.duration;

				var ready = new_position > start_fraction && new_position < end_fraction;
				if (!this.editingClip && (this.clip_start > 0 || this.clip_end < this.duration)
					|| ready) {

					if (!this.editingClip && this.clip_start !== undefined) {
						duration = this.clip_end - this.clip_start;
						new_position = new_position * duration + this.clip_start;
					} else {
						new_position *= duration;
					}
					this.player.seekTo(new_position,false);
					this.player.pauseVideo();
				}
			}
			
			this.dragClipIn = function(event) {
				var play_marker_range = $('.play_marker_range',this.container);
				var new_position = event.clientX - play_marker_range.offset().left;
				new_position /= (play_marker_range.width() - 14);
				if (new_position < 0) {
					new_position = 0;
				} else if (new_position > 1) {
					new_position = 1;
				}
				new_position *= this.duration;
				if (new_position > this.clip_end) { //todo: make clip length > zero
					new_position = this.clip_end;
				}
				
				if (this.clip_original_start !== undefined) {
					//todo: more efficient?
					if (new_position < this.clip_original_start - 10) {
						new_position = this.clip_original_start - 10;
					} else if (new_position > this.clip_original_start + 10) {
						new_position = this.clip_original_start + 10;
					}
				}
				
				this.clip_start = new_position;
				$('.clip_start_input',this.container).val(secondsToTimeString(new_position));
				
				this.calcMarkerConstraints();
			}

			this.dragClipOut = function(event) {
				var play_marker_range = $('.play_marker_range',this.container);
				var new_position = event.clientX - play_marker_range.offset().left;
				new_position /= (play_marker_range.width() - 14);
				if (new_position < 0) {
					new_position = 0;
				} else if (new_position > 1) {
					new_position = 1;
				}
				new_position *= this.duration;
				if (new_position < this.clip_start) { //todo: make clip length > zero
					new_position = this.clip_start;
				}
				
				if (this.clip_original_end !== undefined) {
					//todo: more efficient?
					if (new_position < this.clip_original_end - 10) {
						new_position = this.clip_original_end - 10;
					} else if (new_position > this.clip_original_end + 10) {
						new_position = this.clip_original_end + 10;
					}
				}
				
				this.clip_end = new_position;
				$('.clip_end_input',this.container).val(secondsToTimeString(new_position));
				this.calcMarkerConstraints();
			}
			
			this.calcMarkerConstraints = function() {
				var play_bar = $('.play_bar',this.container);
				var play_bar_width = play_bar.width();
				var pixel_bar_left = play_bar.offset().left;
				var pixel_scale = play_bar_width / this.duration; //todo: divide by zero?

				var out_left_constraint = this.clip_start;
				var out_right_constraint = this.duration;
				if (this.clip_original_end !== undefined) {
					out_left_constraint = this.clip_original_end - 10;
					if (out_left_constraint < this.clip_start + 1) {
						out_left_constraint = this.clip_start + 1;
					}

					out_right_constraint = this.clip_original_end + 10;
					if (out_right_constraint > this.duration) {
						out_right_constraint = this.duration;
					}
				}
				
				out_left_constraint = out_left_constraint * pixel_scale + pixel_bar_left;
				out_right_constraint = out_right_constraint * pixel_scale + pixel_bar_left;
				$('.out_marker',this.container).draggable('option','containment',[out_left_constraint,0,out_right_constraint,0]);

				var in_left_constraint = 0;
				var in_right_constraint = this.clip_end;
				if (this.clip_original_start !== undefined) {
					in_left_constraint = this.clip_original_start - 10;
					if (in_left_constraint < 0) {
						in_left_constraint = 0;
					}

					in_right_constraint = this.clip_original_start + 10;
					if (in_right_constraint > this.clip_end - 1) {
						in_right_constraint = this.clip_end - 1;
					}
				}
				
				in_left_constraint = in_left_constraint * pixel_scale + pixel_bar_left;
				in_right_constraint = in_right_constraint * pixel_scale + pixel_bar_left;
				$('.in_marker',this.container).draggable('option','containment',[in_left_constraint,0,in_right_constraint,0]);	
			}

			this.onPlayerStateChange = function(state) {
				var playerState = this.player.getPlayerState();
				if (!this.dragging) {
					if (state == 1) {
						this.play();
					} else if (state != 3) {
						this.pause();
					}
				}
			}
			
			this.play = function() {
				this.playState = RW_PLAYING;
				var state = this.player.getPlayerState();
				if (state != 1) {
					switch(state) {
						case 2: //paused
						case 3: //buffering
						case 5: //cued
							var currentTime = this.player.getCurrentTime();
							if (this.clip_end === undefined
								|| this.editingClip
								|| currentTime < this.clip_end) {
								break;
							}
						case -1: //unstarted
						case 0: //ended
							var start_point;
							if (this.clip_start !== undefined) {
								start_point = this.clip_start;
							} else {
								start_point = 0;
							}
							this.player.seekTo(start_point, true);
						default:
					}
					this.player.playVideo();
				}
			}
			
			this.togglePlay = function() {
				if (this.playState) {
					this.pause();
				} else {
					var currentTime = this.player.getCurrentTime();
					if (this.clip_end !== undefined && currentTime < this.clip_end) {
						this.pause_at_end = true;
					} else {
						this.pause_at_end = false;
					}
					this.play();
				}
			}
			
			this.toggleEditClip = function() {
				this.editingClip = !this.editingClip;
				if (this.editingClip) {
					if (!this.allow_edit && this.clip_start == 0 && this.clip_end == this.duration) {
						var clip_duration;
						if (this.duration < 120) {
							clip_duration = this.duration / 2;
						} else if (this.duration > 60 * 20) {
							clip_duration = this.duration / 10;
						} else {
							clip_duration = 120;
						}

						var currentTime = this.player.getCurrentTime();
						if (currentTime == 0) {
							this.clip_start = (this.duration - clip_duration) / 2.0;
						} else if (this.duration - currentTime < clip_duration) {
							this.clip_start = this.duration - clip_duration;
						} else {
							this.clip_start = currentTime;
						}
						
						this.clip_end = this.clip_start + clip_duration;

					}

					//update pointer
					var play_bar = $('.play_bar',this.container);
					var play_bar_width = play_bar.width();
					var in_marker = $('.in_marker',this.container);
					var out_marker = $('.out_marker',this.container);
					var play_px = (play_bar_width) * this.clip_start/this.duration;
					in_marker.css('left',play_px + 'px');
					play_px = (play_bar_width) * this.clip_end/this.duration;
					out_marker.css('left',play_px + 'px');
					
					this.calcMarkerConstraints();
				
					$('.edit_clip_form', this.container).css('display','block');
					$('.in_marker', this.container).css('display','block');
					$('.out_marker', this.container).css('display','block');
					$('.end_button', this.container).css('display','block');
				} else {
					$('.edit_clip_form', this.container).css('display','none');
					$('.in_marker', this.container).css('display','none');
					$('.out_marker', this.container).css('display','none');
					$('.end_button', this.container).css('display','');
				}
				this.updatePlayerInfo();
			}
			
			this.pause = function() {
				this.playState = RW_PAUSED;
				if (this.player.getPlayerState() != 2) {
					this.player.pauseVideo();
				}
			}
			
			this.updatePlayerInfo = function() {
				if (this.player === undefined) {
					return;
				}

				//update play pointer
				var play_bar = $('.play_bar',this.container);
				var play_bar_width = play_bar.width();

				//update bytesLoaded
				var bytesLoaded = this.player.getVideoBytesLoaded();
				var bytesTotal = this.player.getVideoBytesTotal();
				var startBytes = this.player.getVideoStartBytes();
				var play_bar = $('.play_bar',this.container);
				var start_px = play_bar_width * startBytes/bytesTotal;
				var mid_px = play_bar_width * bytesLoaded/bytesTotal;
				
				//update for messed up bytes results. todo: make more sense of this
				if (mid_px + start_px >=play_bar_width) {
					start_px = play_bar_width - mid_px;
				}
				
				if (this.editingClip || this.clip_start === undefined
					|| this.clip_start == 0 && this.clip_end == this.duration) {
					play_bar.children().eq(2).css('width',start_px + 'px');
					play_bar.children().eq(3).css('width',mid_px + 'px');
					play_bar.children().eq(4).css('width',(play_bar_width-mid_px-start_px) + 'px');
				} else {
					play_bar.children().eq(2).css('width','0');
					play_bar.children().eq(3).css('width','100%');
					play_bar.children().eq(4).css('width','0');
				}

				//var videoDuration = this.player.getDuration();
				var currentTime = this.player.getCurrentTime();
				var duration = this.player.getDuration();
				if (duration) {
					this.duration = duration;
					if (this.clip_end !== undefined && this.clip_end > duration) {
						this.clip_end = duration;
					}
				}

				if (this.clip_end !== undefined && currentTime >= this.clip_end
					&& (this.pause_at_end || !this.editingClip)) {
					this.pause();
				}

				$('.video_time',this.container).html(secondsToTimeString(currentTime) + ' / ' + secondsToTimeString(this.duration));

				var start = 0;
				if (!this.editingClip) {
					start = this.clip_start;
					duration = this.clip_end - start;
					currentTime -= start;
				}

				var play_marker = $('.play_marker',this.container);
				//play_bar_width += 14; //adjustment
				var play_px = (play_bar_width) * currentTime/duration;
				if (play_px < 0) { //correction, just in case
					play_px = 0;
				} else if (play_px > play_bar_width) {
					play_px = play_bar_width;
				}
				if (!this.dragging) {
					play_marker.css('left',play_px + 'px');
				}

				if (this.player.getPlayerState() == 1) {
					$('.play_button',this.container).css('background-position','left top');
				} else {
					$('.play_button',this.container).css('background-position','right top');
				}
			}

			//todo: add start/stop info for clip
			if (video_data === undefined
				|| video_data.post_id === undefined
				|| video_data.post_id == null
				|| video_data.foreign_key === undefined
				|| video_data.foreign_key == '') {
				//return false;
			} else {
				//todo: check for element id
				
				this.playState = RW_PAUSED;
				this.editingClip = false;
				this.dragging = false;
				for (var i in video_data) {
					this[i] = video_data[i];
				}
				
				//dumb but we gotta do this
				this.clip_start = this.start_offset;
				this.clip_end = this.end_offset;
				
				this.ytapiplayer = $('#ytapiplayer-' + this.post_id);
				this.container = $('#player_container_' + this.post_id);				
				if (this.ytapiplayer === undefined) {
					return false;
				}
				
				if (video_data.aspect_ratio === undefined) {
					aspect_ratio = 4/3;
				}
				
				if (video_data.duration && video_data.duration !== undefined) {
					this.duration = video_data.duration;
				} else {
					this.duration = 0;
				}
				
				//initialize in and out points
				if (this.clip_start === undefined) {
					this.clip_start = 0;
					this.clip_end = this.duration;
				}

				$('.clip_start_input',this.container).val(secondsToTimeString(this.clip_start));
				$('.clip_end_input',this.container).val(secondsToTimeString(this.clip_end));

				//update pointer
				var play_bar = $('.play_bar',this.container);
				var play_bar_width = play_bar.width();
				var play_range_width = play_bar.width();
				var in_marker = $('.in_marker',this.container);
				var out_marker = $('.out_marker',this.container);
				var play_px = (play_bar_width) * this.clip_start/this.duration;
				in_marker.css('left',play_px + 'px');
				play_px = (play_bar_width) * this.clip_end/this.duration;
				out_marker.css('left',play_px + 'px');
				
				this.calcMarkerConstraints();
				
				var params = { allowScriptAccess: "always", allowFullScreen: "true" };
				var atts = { id: 'myytplayer-' + post_id };
				
				var width;
				width = this.ytapiplayer.width() - 4;
				if (!width) {
					//width = this.ytapiplayer.offsetWidth;
					width = 500;
				} else if (width > 800) {
					width = 800;
				}
				
				var height = Math.round(width / this.aspect_ratio);
				if (height > 500) {
					height = 500;
					width = Math.round(height * this.aspect_ratio);
				}
				
				/*
				swfobject.embedSWF("http://www.youtube.com/v/" + foreign_key + "?enablejsapi=1&playerapiid=" + post_id, 
								   'ytapiplayer-' + post_id, "425", "356", "8", null, null, params, atts);
				*/
				
				this.container.width(width);
				swfobject.embedSWF("http://www.youtube.com/apiplayer?enablejsapi=1&fs=1&version=3&playerapiid=" + post_id, 
								   'ytapiplayer-' + post_id, width, height, "8", null, null, params, atts);

				//todo: resize event. maybe not necessary for yt api, but for our slider
				var play_bar = $('.play_bar',this.container);
				var play_marker_range = $('.play_marker_range',this.container);
				var play_bar_width = play_bar.width();
				play_marker_range.width(play_bar_width + 14);

				return true;
			}
		}
	}

	if (window.RW === undefined) {
		window.RW = function(element_id) {
			this.email_regex = /^[\w-][\+\w-]*(\.[\+\w-]+)*@([a-z0-9-]+(\.[a-z0-9-]+)*?\.[a-z]{2,6}|(\d{1,3}\.){3}\d{1,3})(:\d{4})?$/;
			this.youtube_regex = /^(http:\/\/)?(www.)?(youtube\.com\/(watch(\?|#!)v=|v\/)|youtu\.be\/)([A-Za-z0-9_\-]+)/; //id=6
	
			this.submit_url = function() {
				window.rw.enable_errors = true;
				
				if (!window.rw.validate_all()) {
					return false;
				}
				
				$('#submit_url_button').attr('disabled', 'disabled');
				var data = {
					action: 'add_video_action'
				}
				data.video_url = $('#video_url').val();
				if (data.video_url.toLowerCase() == 'video url') {
					data.video_url = '';
				}
				
				var title = $('#video_title').val();
				if (window.rw.video_id && ($('#add_video_div').offsetHeight || $('#add_video_div').css('display') == 'block') ) {
					data.action = 'save_video_action';
					data.title = title;
					data.description = $('#video_description').val();
					data.video_keywords = $('#video_keywords').val();
					if (window.Recaptcha && window.Recaptcha.get_challenge && Recaptcha.get_challenge()) {
						data.captcha_response = Recaptcha.get_response();
						data.captcha_challenge = Recaptcha.get_challenge();
					}
					var email = $('#video_email').val();
					if (email.toLowerCase() == 'email (optional)') {
						email = '';
					}
					if (email !== undefined && email !='') {
						data.video_email = email;
					}
					data.video_id = window.rw.video_id;
				}
				
				//$.post(ajaxurl, data, function(response) {
				//	alert('Got this from the server: ' + response);
				//});
				$.ajax({ url: ajaxurl, data:data, type:'POST', dataType: 'json', success: function(response){
					if (response.error) {
						window.rw.validate_all();
						$('#video_url').css('background-color','#F28484');
						$('#video_url_error').html(response.error); //todo: encode?
						Recaptcha.reload();
					} else if (response.post_id) {
						if (response.post_url) {
							window.location = response.post_url;
							return;
						}
					} else {
						//Recaptcha.reload();
						$('#video_url').css('background-color','');
						$('#video_url_error').html('&nbsp;');
						$('#add_video_div').css('display','block');
						$('#add_url_div').css('display','none');
						$('#video_title').val(response.title);
						$('#video_description').val(response.description);
						$('#video_keywords').val(response.keywords);
						window.rw.video_id = response.video_id;
						window.rw.validate_all();
					}
					return;
				}});
			}
			
			this.validate_all = function() {
				var valid = true;
				valid = window.rw.validate_url();
				valid = window.rw.validate_email() && valid;
				valid = window.rw.validate_title() && valid;
				valid = window.rw.validate_captcha() && valid;

				if (!valid && rw.enable_errors) {
					$('#submit_url_button').attr('disabled', 'disabled');
				} else {
					$('#submit_url_button').removeAttr('disabled');
				}
				
				return valid;
			}
			
			this.validate_url = function() {
				var video_url_valid = true;
				var rw = window.rw;
			
				//first validate URL
				var video_url = $('#video_url').val();
				if (video_url.toLowerCase() == 'video url') {
					video_url = '';
				}
				var video_url_error = '';
				if (video_url == ''){
					video_url_valid = false;
					video_url_error = "Please enter a YouTube video watch page URL";
				} else {	
					if (!rw.youtube_regex(video_url)){
						video_url_valid = false;
						video_url_error = "Not a valid YouTube video URL";
					} else {
						//valid = true;
					}
				}
				
				//todo: turn yellow if blank and not error and pc is not blank
				if (video_url_error == '' || !rw.enable_errors) {
					$('#video_url').css('background-color','');
					$('#video_url_error').html('&nbsp;');
				} else {
					$('#video_url').css('background-color','#F28484');
					$('#video_url_error').html(video_url_error); //todo: encode?
				}
	
				return video_url_valid;
	
			}
			
			this.validate_captcha = function() {
				var rw = window.rw;

				var valid = true;
				if (window.Recaptcha && window.Recaptcha.get_challenge && window.Recaptcha.get_challenge()) {
					var captcha = Recaptcha.get_response();
					if (!captcha) valid = false;
				} else {
					valid = true;
				}

				//todo: make captcha error div
				if (!valid && ($('#add_video_div').offsetHeight || $('#add_video_div').css('display') == 'block')) {
					$('#video_captcha_error').html('Please enter a captcha solution');
					return false;
				} else {
					$('#video_captcha_error').html('&nbsp;');
					return true;
				}
			}
			
			this.validate_title = function() {
				var rw = window.rw;
				
				var video_title = $('#video_title').val();
				if (video_title == '' && ($('#add_video_div').offsetHeight || $('#add_video_div').css('display') == 'block')) {
					$('#video_title_error').html('Please enter a title');
					return false;
				} else {
					$('#video_title_error').html('&nbsp;');
					return true;
				}
			}
			
			this.validate_email = function() {
				var video_email_valid = true;
				var rw = window.rw;
			
				//first validate email
				var video_email = $('#video_email').val();
				if (video_email.toLowerCase() == 'email (optional)') {
					video_email = '';
				}
				var video_email_error = '';
				if (video_email == '' || video_email === undefined){
					video_email_valid = true;
					//video_email_error = "Please enter a YouTube video watch page URL";
				} else {	
					if (!rw.email_regex(video_email)){
						video_email_valid = false;
						video_email_error = "Not a valid email address";
					} else {
						//valid = true;
					}
				}
				
				//todo: turn yellow if blank and not error and pc is not blank
				if (video_email_error == '' || !rw.enable_errors) {
					$('#video_email').css('background-color','');
					$('#video_email_error').html('&nbsp;');
				} else {
					$('#video_email').css('background-color','#F28484');
					$('#video_email_error').html(video_email_error); //todo: encode?
				}
	
				return video_email_valid;
	
			}
			
			//initialize everything
			this.enable_errors = false;
			$('#video_url').bind('change keyup blur',this.validate_all);
			$('#video_email').bind('change keyup blur',this.validate_all);
			$('#video_title').bind('change keyup blur',this.validate_all);
			$('#recaptcha_response_field').bind('change keyup blur',this.validate_all);
			$('#submit_url_button').click(this.submit_url);
		}
	}

	window.rw = new RW('add_video_form');
});

var rwInterval = 0;

function onYouTubePlayerReady(post_id) {
	//var ytplayer = document.getElementById("myytplayer-" + post_id);
	window.rwPlayers[post_id].player = document.getElementById("myytplayer-" + post_id);
	//rwPlayers[post_id].player = jQuery('#myytplayer-' + post_id);
	var obj = window.rwPlayers[post_id];
	window.rwPlayers[post_id].setUpVideo();
	if (!rwInterval) {
		setInterval(updatePlayerInfo, 100);
		updatePlayerInfo()
	}
}

function updatePlayerInfo() {
	for (var id in window.rwPlayers) {
		window.rwPlayers[id].updatePlayerInfo();
	}
}

function secondsToTimeString(seconds) {
	var minutes = Math.floor(seconds / 60);
	var s = Math.round(seconds) % 60;
	if (s < 10) {
		s = '0' + s;
	}
	return minutes + ':' + s;
}

function rwDragClipIn(post_id, event) {
	window.rwPlayers[post_id].dragClipIn(event);
}

function rwDragClipOut(post_id, event) {
	window.rwPlayers[post_id].dragClipOut(event);
}

function rwDragPlayer(post_id, event) {
	window.rwPlayers[post_id].dragVideo(event);
}

function rwStopDraggingPlayer(post_id, event) {
	window.rwPlayers[post_id].stopDraggingVideo(event);
}

function rwTogglePlay(post_id) {
	window.rwPlayers[post_id].togglePlay();
}

function rwToggleEditClip(post_id) {
	window.rwPlayers[post_id].toggleEditClip();
}
