//todo: volume
//todo: fullscreen

const RW_PLAYING = 1;
const RW_PAUSED = 0;
//const RW_DRAGGING = -1;

jQuery(document).ready(function($) {
	if (window.RwPlayer === undefined) {
		window.rwPlayers = [];

		RwPlayer = function(post_id,video_data, opts) {
			//todo: add start/stop info for clip
			
			var options = opts || {};
			
			if (video_data === undefined ||
				video_data.post_id === undefined ||
				video_data.post_id === null ||
				video_data.foreign_key === undefined ||
				video_data.foreign_key == '') {
				//return false;
			} else {
				//todo: check for element id
				
				this.enable_form_errors = false;
				
				this.playState = RW_PAUSED;
				this.fullscreen = false;
				this.position_dirty = false;
				this.editingClip = false;
				this.dragging = false;
				for (var i in video_data) {
					if (video_data[i] !== undefined) {
						this[i] = video_data[i];
					}
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
					video_data.aspect_ratio = 4/3;
				}
				
				if (video_data.duration && video_data.duration !== undefined) {
					this.duration = video_data.duration;
				} else if (!this.duration) {
					this.duration = 0;
				}
				
				//initialize in and out points
				if (this.clip_start === null || this.clip_start === undefined) {
					this.clip_start = 0;
					this.clip_end = this.duration;
				}

				//set up clip form
				var clip_start_input = $('.clip_start_input',this.container);
				clip_start_input.val(this.secondsToTimeString(this.clip_start));

				var clip_end_input = $('.clip_end_input',this.container);
				clip_end_input.val(this.secondsToTimeString(this.clip_end));

				if (video_data.allow_edit) {
					//fill in data if allowed to edit
					$('.clip_title',this.container).val(this.clip_title);
					$('.clip_description',this.container).val(this.clip_description);
					
				}

				//update pointer
				var play_bar = $('.play_bar',this.container);
				var play_bar_width = play_bar.width();
				var in_marker = $('.in_marker',this.container);
				var out_marker = $('.out_marker',this.container);
				var play_px = (play_bar_width) * this.clip_start/this.duration;
				in_marker.css('left',play_px + 'px');
				play_px = (play_bar_width) * this.clip_end/this.duration - 7;
				out_marker.css('left',play_px + 'px');
				
				this.calcMarkerConstraints();
				
				var params = {
					wmode: 'opaque',
					allowScriptAccess: "always",
					allowFullScreen: "true"//,
					//movie: 'http://www.youtube.com/v/' + this.foreign_key
				};
				var atts = { id: 'myytplayer-' + post_id };
				
				var width;
				if (options.width) {
					width = options.width;
				} else {
					width = this.ytapiplayer.width() - 4;
				}
				if (!width) {
					//width = this.ytapiplayer.offsetWidth;
					width = 600;
				} else if (width > 800) {
					width = 800;
				}
				
				//this.aspect_ratio = video_data.aspect_ratio;
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
				swfobject.embedSWF("http://www.youtube.com/apiplayer?enablejsapi=1&fs=1&playerapiid=" + post_id + '&v=' + this.foreign_key, 
								   'ytapiplayer-' + post_id, width, height, "8", null, null, params, atts);

				//todo: resize event. maybe not necessary for yt api, but for our slider
				play_bar = $('.play_bar',this.container);
				var play_marker_range = $('.play_marker_range',this.container);
				play_bar_width = play_bar.width();
				//play_marker_range.width(play_bar_width);

				//set up events
				var rwp = this;
				$('.clip_title',this.container).bind('change keyup blur',function(event) {
					rwp.validate_all();
				});
				$('.clip_description',this.container).bind('change keyup blur',function(event) {
					rwp.validate_all();
				});
				$('.fullscreen_button',this.container).bind('click',function(event) {
					rwp.toggleFullscreen();
				});
				
				if (clip_start_input.length) {
					clip_start_input.bind('change',function(event) {
						rwp.validate_clip_input($(event.target));
					});
				}
				
				if (clip_end_input.length) {
					clip_end_input.bind('change',function(event) {
						rwp.validate_clip_input($(event.target));
					});
				}
				
				$(window).resize(function() {
					rwp.resize();
				});
				
				var vars;
				if (location.hash && (vars = location.hash.substring(1).split('&'))) {
					for (var i = 0; i < vars.length; i++) {
						var pair = vars[i].split('=');
						var arg = pair[0].split('-');
						if (arg.length &&
							arg[1] == this.post_id &&
							arg[0] == 'mode') {
							if (pair[1] == 'share') {
								this.toggleShareClip(true);
							} else if (pair[1] == 'edit') {
								this.toggleEditClip(true);
							}
						}
					}
				}

				return true;
			}
		};

		RwPlayer.prototype.setUpVideo = function() {
			var rwp = this;

			var duration = rwp.player.getDuration();
			if (duration > 0 && duration !== undefined) {
				rwp.duration = duration;
			}

			//todo: specify start seconds
			$('.video_time',rwp.container).html('0:00 / ' + this.secondsToTimeString(this.duration));

			rwp.player.addEventListener("onStateChange", "window.rwPlayers[" + this.post_id + "].onPlayerStateChange");
			
			var volume_slider_handle = $('.volume_slider_handle',rwp.container);
			volume_slider_handle.draggable({ axis: 'x', containment: 'parent', cursor: 'pointer',
				drag: function(event,ui) {
					rwp.dragVolume(event);
				}
			});
			
			var volume_slider = $('.volume_slider',rwp.container);
			volume_slider.click(function(event,ui) {
					rwp.dragVolume(event);
				}
			);
			
			$('.mute_button',rwp.container).click(function(event,ui) {
				if (rwp.player.isMuted()) {
					rwp.setVolume(-1);
				} else {
					rwp.setVolume(0);
				}
			});
			
			var play_marker = $('.play_marker',rwp.container);
			play_marker.draggable({ axis: 'x', containment: '', cursor: 'pointer',
				drag: function(event,ui) {
					//rwDragPlayer(rwp.post_id,event);
					rwp.dragVideo(event,ui);
				},
				stop: function(event,ui) {
					//rwStopDraggingPlayer(rwp.post_id,event);
					rwp.stopDraggingVideo(event);
				}
			});

			$('.in_marker',this.container).draggable({ axis: 'x', containment: 'parent', cursor: 'pointer',
				drag: function(event,ui) {
					//rwDragClipIn(rwp.post_id,event);
					rwp.dragClipIn(event);
				}
			});
			
			$('.out_marker',this.container).draggable({ axis: 'x', containment: 'parent', cursor: 'pointer',
				drag: function(event,ui) {
					//rwDragClipOut(rwp.post_id,event);
					rwp.dragClipOut(event);
				}
			});
			this.calcMarkerConstraints();
			
			$('.play_button',rwp.container).click(function(event) {
				//rwTogglePlay(rwp.post_id);
				rwp.togglePlay();
			});
			var editclip_button = $('.editclip_button',rwp.container);
			if (editclip_button.length) {
				editclip_button.click(function(event) {
					rwp.toggleEditClip();
				});
			} else {
				this.toggleEditClip();
			}

			$('.share_button',rwp.container).click(function(event) {
				rwp.toggleShareClip();
			});
			
			$('.start_button',rwp.container).click(function(event) {
				rwp.rewind();
			});
			$('.end_button',rwp.container).click(function(event) {
				rwp.ffwd();
			});
			
			$('.clip_save_button',rwp.container).click(function(event) {
				rwp.submit_clip();
			});

			$('.clip_save_new_button',rwp.container).click(function(event) {
				rwp.submit_clip(true);
			});

			rwp.player.cueVideoById(rwp.foreign_key,0); //,'hd720'

		};

		RwPlayer.prototype.validate_title = function() {
			var clip_title = $('.clip_title',this.container).val();
			if (clip_title == '' && this.enable_form_errors) {
				$('.clip_title',this.container).css('background-color','#F28484');
				$('.clip_title_error',this.container).html('Please enter a new title');
			} else {
				$('.clip_title',this.container).css('background-color','');
				$('.clip_title_error',this.container).html('&nbsp;');
			}
			return clip_title && true;
		};
		
		RwPlayer.prototype.validate_description = function() {
			var clip_description = $('.clip_description',this.container).val();
			if (clip_description == '' && this.enable_form_errors) {
				$('.clip_description',this.container).css('background-color','#F28484');
				$('.clip_description_error',this.container).html('Please enter a description');
			} else {
				$('.clip_description',this.container).css('background-color','');
				$('.clip_description_error',this.container).html('&nbsp;');
			}
			return clip_description && true;
		};

		RwPlayer.prototype.validate_clip_input = function(target) {
			//var clip_start_input = target; //$('.clip_start_input',this.container);
			var value = target.val();
			
			var seconds;
			var re = /((\d+):)?(\d+(\.\d*)?)/;
			var parts;
			if (parts = re.exec(value)) {
				seconds = parseFloat(parts[3]);
				if (parts[2]) {
					seconds += 60 * parseInt(parts[2]);
				}
			}
			
			//minimum clip length of 5 seconds
			
			if (!isNaN(seconds) && seconds > 0 && seconds <= this.duration) {
				if (target.hasClass('clip_start_input')) {
					if (seconds < this.clip_end) {
						this.clip_start = seconds;
						if (this.clip_start > this.clip_end - 5) {
							seconds = this.clip_start = this.clip_end - 5;
						}
					}
				} else if (target.hasClass('clip_end_input')) {
					if (seconds > this.clip_start) {
						this.clip_end = seconds;
						if (this.clip_start > this.clip_end - 5) {
							seconds = this.clip_end = this.clip_start + 5;
						}
					}
				}
			}

			//update pointer
			var play_bar = $('.play_bar',this.container);
			var play_bar_width = play_bar.width();
			var marker;
			var px_offset = 0;
			if (target.hasClass('clip_start_input')) {
				marker = $('.in_marker',this.container);
				target.val(this.secondsToTimeString(this.clip_start));
				seconds = this.clip_start;
			} else if (target.hasClass('clip_end_input')) {
				marker = $('.out_marker',this.container);			
				target.val(this.secondsToTimeString(this.clip_end));
				seconds = this.clip_end;
				px_offset = -7;
			} else {
				return;
			}
			var play_px = (play_bar_width) * seconds/this.duration;
			marker.css('left',play_px + px_offset + 'px');

			this.calcMarkerConstraints();

		};
		
		RwPlayer.prototype.validate_all = function() {
			var valid = true;
			valid = this.validate_title();
			valid = this.validate_description() && valid;
			//todo: valid = this.validate_captcha() && valid;

			if (!valid && this.enable_form_errors) {
				$('.clip_save_button',this.container).attr('disabled', 'disabled');
				$('.clip_save_new_button',this.container).attr('disabled', 'disabled');
			} else {
				$('.clip_save_button',this.container).removeAttr('disabled');
				$('.clip_save_new_button',this.container).removeAttr('disabled');
			}

			return valid;
		};
		
		RwPlayer.prototype.submit_clip = function(save_new) {
			this.enable_form_errors = true;
			
			if ((!save_new || save_new === undefined) &&
				(this.clip_id === undefined || this.clip_id === null) &&
				(	this.clip_start !== undefined && this.clip_start > 0 ||
					this.clip_end !== undefined && this.clip_end < this.duration
				)
			) {
				$('.clip_save_button',this.container).attr('disabled', 'disabled');
				$('.clip_error',this.container).html('Cannot change in and out points of original clip. Please save as a new clip.');
				return false;
			} else {
				$('.clip_save_button',this.container).removeAttr('disabled');
				$('.clip_error',this.container).html('&nbsp;');
			}
			
			if (!this.validate_all()) {
				return false;
			}

			var data = {
				action: 'save_clip_action',
				video_id: this.video_id,
				clip_start: this.clip_start,
				clip_end: this.clip_end
			};
			
			var error_msg = false;
			if (save_new) {
				data.clip_id = false;
			} else if (!this.allow_edit) {
				//this should never happen
				error_msg = "You don't own this clip.  Please save as a new clip.";
			} else {
				data.clip_id = this.clip_id;
				data.post_id = this.post_id;
			}
			
			var title = $('.clip_title',this.container).val();

			data.title = title;
			//todo: make sure title is not the same as clip description

			data.description = $('.clip_description',this.container).val();
			//todo: make sure description is not the same as clip description

			data.keywords = $('.clip_keywords',this.container).val();
			/*
			if (window.Recaptcha && window.Recaptcha.get_challenge && Recaptcha.get_challenge()) {
				data.captcha_response = Recaptcha.get_response();
				data.captcha_challenge = Recaptcha.get_challenge();
			}
			*/

			//todo: name and email address
			
			var obj = this;
			$.ajax({ url: ajaxurl, data:data, type:'POST', dataType: 'json', success: function(response){
				var recaptcha_widget_div = $('.recaptcha_widget_div',this.container);
				if (response.error && response.error.length) {
					window.rw.validate_all();
					if (response.error.clip_error) {
						$('.clip_error',this.container).html(response.error.clip_error); //todo: encode?
					}
					if (recaptcha_widget_div && recaptcha_widget_div.length && window.Recaptcha && window.Recaptcha.reload) {
						Recaptcha.reload();
					}
				} else if (response.post_id) {
					if (response.post_url) {
						var w = window.top;
						if (response.post_url != w.location.protocol + '//' + w.location.host + w.location.pathname) {
							w.location = response.post_url + '#mode-' + response.post_id + '=share';
						} else {
							obj.toggleShareClip(true);
						}
						return;
					}
				} else {
				/* todo: get rid of this
					//Recaptcha.reload();
					$('.video_url',this.container).css('background-color','');
					$('.video_url_error',this.container).html('&nbsp;');
					$('.add_video_div',this.container).css('display','block');
					$('.add_url_div',this.container).css('display','none');
					$('.video_title',this.container).val(response.title);
					$('.video_description',this.container).val(response.description);
					$('.video_keywords',this.container).val(response.keywords);
					window.rw.video_id = response.video_id;
					window.rw.validate_all();
				*/
				}
				return;
			}});
		};
		
		RwPlayer.prototype.rewind = function() {
			var currentTime = this.player.getCurrentTime();
			var new_position;
			if (this.clip_start === undefined || currentTime <= this.clip_start) {
				new_position = 0;
			} else {
				new_position = this.clip_start - 1;
			}

			if (this.player.getPlayerState() > -1) {  //not unstarted
				new_position -= 1; //fudge factor
			}
			this.player.seekTo(new_position,true);
		};
		
		RwPlayer.prototype.ffwd = function() {
			var currentTime = this.player.getCurrentTime();
			var new_position;
			if (currentTime >= this.clip_end || !this.editingClip) {
				new_position = this.duration;
			} else {
				new_position = this.clip_end;
			}

			this.player.seekTo(new_position,true);
		};
		
		RwPlayer.prototype.stopDraggingVideo = function(event) {
			var play_marker_range = $('.play_marker_range',this.container);
			var new_position = event.clientX - play_marker_range.offset().left + 17;
			new_position /= (play_marker_range.width() - 14);

			new_position *= this.duration;
			//this.player.seekTo(new_position,true);

			if (this.playState == RW_PLAYING) {
				this.playVideo();
			} else {
				this.pauseVideo();
			}
			this.dragging = false;
		};
		
		RwPlayer.prototype.dragVideo = function(event, ui) {
			this.dragging = true;
			this.position_dirty = true;
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
			//var new_position = event.clientX - play_marker_range.offset().left + 17;
			var new_position = ui.position.left + 17;
			new_position /= (play_marker_range.width() );

			var duration = this.duration;
			
			if (new_position < 0.0) {
				new_position = 0.0;
			} else if (new_position > 1.0) {
				new_position = 1.0;
			}

			var ready = true || new_position > start_fraction && new_position < end_fraction;
			if (!this.editingClip && (this.clip_start > 0 || this.clip_end < this.duration) ||
				ready) {

				var new_time;
				if (!this.editingClip && this.clip_start !== undefined) {
					duration = this.clip_end - this.clip_start;
					new_time = new_position * duration + this.clip_start;
				} else {
					new_time = new_position * duration;
				}

				$('.video_time',this.container).html(this.secondsToTimeString(new_time) + ' / ' + this.secondsToTimeString(duration));
				new_position = new_position * (play_marker_range.width() ) - 17;
				ui.position.left = new_position;
				ui.helper.css('left',new_position + 'px');

				this.player.seekTo(new_time,false);
				this.player.pauseVideo();
			}
		};

		RwPlayer.prototype.setVolume = function(level) {
			var volume_slider = $('.volume_slider',this.container);
			var handle = $('.volume_slider_handle',this.container);
			
			var range_width = volume_slider.width() - handle.width();
			
			if (level > 100) {
				level = 100;
			}

			if (level === 0) {
				this.player.mute();
			} else {
				this.player.unMute();
				if (level > 0) {
					this.player.setVolume(level);
				} else {
					level = this.player.getVolume();
				}
			}

			handle.css('left',range_width * level / 100.0);
		};

		RwPlayer.prototype.dragVolume = function(event) {
			var volume_slider = $('.volume_slider',this.container);
			var handle = $('.volume_slider_handle',this.container);
			
			var range_width = volume_slider.width() - handle.width();

			var new_position = event.clientX - volume_slider.offset().left;
			var level = 100 * new_position / range_width;

			if (level < 0) {
				level = 0;
			} else if (level > 100) {
				level = 100;
			}

			this.setVolume(level);
		};
		
		RwPlayer.prototype.dragClipIn = function(event) {
			var play_marker_range = $('.play_marker_range',this.container);
			var new_position = event.clientX - play_marker_range.offset().left;
			new_position /= (play_marker_range.width() - 14);
			if (new_position < 0) {
				new_position = 0;
			} else if (new_position > 1) {
				new_position = 1;
			}
			new_position *= this.duration;

			//minimum clip length of 5 seconds
			if (new_position > this.clip_end - 5) { //todo: make clip length > zero
				new_position = this.clip_end - 5;
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
			$('.clip_start_input',this.container).val(this.secondsToTimeString(new_position));
			
			this.calcMarkerConstraints();
		};

		RwPlayer.prototype.dragClipOut = function(event) {
			var play_marker_range = $('.play_marker_range',this.container);
			var new_position = event.clientX - play_marker_range.offset().left;
			new_position /= (play_marker_range.width() - 14);
			if (new_position < 0) {
				new_position = 0;
			} else if (new_position > 1) {
				new_position = 1;
			}
			new_position *= this.duration;

			//minimum clip length of 5 seconds
			if (new_position < this.clip_start + 5) {
				new_position = this.clip_start + 5;
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
			$('.clip_end_input',this.container).val(this.secondsToTimeString(new_position));
			this.calcMarkerConstraints();
		};
		
		RwPlayer.prototype.calcMarkerConstraints = function() {
			var play_bar = $('.play_bar',this.container);
			var play_bar_width = play_bar.width();
			var pixel_bar_left = play_bar.offset().left;
			var pixel_scale = play_bar_width / this.duration; //todo: divide by zero?

			var out_left_constraint = this.clip_start + 5;
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
			out_right_constraint = out_right_constraint * pixel_scale + pixel_bar_left - 7;
			$('.out_marker',this.container).draggable('option','containment',[out_left_constraint,0,out_right_constraint,0]);

			var in_left_constraint = 0;
			var in_right_constraint = this.clip_end - 5;
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
			in_right_constraint = in_right_constraint * pixel_scale + pixel_bar_left - 7;
			$('.in_marker',this.container).draggable('option','containment',[in_left_constraint,0,in_right_constraint,0]);	
		};

		RwPlayer.prototype.onPlayerStateChange = function(state) {
			var playerState = this.player.getPlayerState();
			if (!this.dragging) {
				if (state == 1) {
					this.play();
				} else if (state != 3) {
					this.pause();
				}
			}
		};
		
		RwPlayer.prototype.play = function() {
			this.playState = RW_PLAYING;
			var state = this.player.getPlayerState();
			$('.play_button',this.container).css('background-position','left top');

			var video_container = $('.video_container',this.container);
			video_container.addClass('playing');
			video_container.removeClass('paused');			

			if (state != 1) {
				switch(state) {
					case 2: //paused
					case 3: //buffering
					case 5: //cued
						var currentTime = this.player.getCurrentTime();
						if (this.clip_end === undefined ||
							this.editingClip && this.position_dirty ||
							currentTime < this.clip_end && this.clip_start <= currentTime
							) {
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
						break;
					default:
				}
				this.player.playVideo();
			}
		};
		
		RwPlayer.prototype.resize = function() {
			if (this.fullscreen) {
				var video_container = $('.video_container',this.container);
	
				video_container.css('width',window.innerWidth + 'px');
				video_container.css('height',window.innerHeight - 43 + 'px');
				this.player.width = window.innerWidth;
				this.player.height = window.innerHeight - 43;
			}
		};

		RwPlayer.prototype.toggleShareClip = function(sharingClip) {
			if (sharingClip === undefined) {
				this.sharingClip = !this.sharingClip;
			} else if (this.sharingClip == sharingClip) {
				return;
			} else {
				this.sharingClip = sharingClip;
			}

			if (this.sharingClip) {
				this.toggleFullscreen(false);
				this.toggleEditClip(false);

				$('.share_clip_form', this.container).css('display','block');
			} else {
				$('.share_clip_form', this.container).css('display','none');
			}
		};
		
		RwPlayer.prototype.toggleFullscreen = function(fullscreen) {
			if (fullscreen === undefined) {
				this.fullscreen = !this.fullscreen;
			} else if (this.fullscreen == fullscreen) {
				return;
			} else {
				this.fullscreen = fullscreen;
			}

			var video_container = $('.video_container',this.container);
			if (this.fullscreen) {
				this.toggleEditClip(false);
				this.toggleShareClip(false);

				$(document.body).addClass('fullscreen');
				this.container.addClass('fullscreen');
				video_container.css('width',window.innerWidth + 'px');
				video_container.css('height',window.innerHeight - 43 + 'px');
				this.player_width = this.player.width;
				this.player_height = this.player.height;
				this.player.width = window.innerWidth;
				this.player.height = window.innerHeight - 43;

				//this.player.setSize(document.body.clientWidth,document.body.clientHeight);
			} else {
				this.container.removeClass('fullscreen');
				$(document.body).removeClass('fullscreen');
				video_container.css('width','');
				video_container.css('height','');
				this.player.width = this.player_width;
				this.player.height = this.player_height;
				//this.player.setSize(video_container.offset().width, video_container.offset().height);
			}

			this.calcMarkerConstraints();
		};

		RwPlayer.prototype.togglePlay = function() {
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
		};
		
		RwPlayer.prototype.toggleEditClip = function(editingClip) {
			if (editingClip === undefined) {
				this.editingClip = !this.editingClip;
			} else if (this.editingClip == editingClip) {
				return;
			} else {
				this.editingClip = editingClip;
			}

			if (this.editingClip) {
				this.toggleFullscreen(false);
				this.toggleShareClip(false);
			
				if (!this.allow_edit && this.clip_start === 0 && this.clip_end == this.duration) {
					var clip_duration;
					if (this.duration < 120) {
						clip_duration = this.duration / 2;
					} else if (this.duration > 60 * 20) {
						clip_duration = this.duration / 10;
					} else {
						clip_duration = 120;
					}

					var currentTime = this.player.getCurrentTime();
					if (currentTime <= 0) {
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
				play_px = (play_bar_width) * this.clip_end/this.duration - 7;
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
			this.updatePlayerInfo(true);
		};
		
		RwPlayer.prototype.pause = function() {
			this.playState = RW_PAUSED;
			$('.play_button',this.container).css('background-position','right top');

			var video_container = $('.video_container',this.container);
			video_container.addClass('paused');
			video_container.removeClass('playing');
			
			if (this.player.getPlayerState() != 2) {
				this.player.pauseVideo();
			}
		};

		RwPlayer.prototype.pauseVideo = function() {
			var player = this.player;
			setTimeout(function() {
				player.pauseVideo();
			}, 1);
		};

		RwPlayer.prototype.playVideo = function() {
			var player = this.player;
			setTimeout(function() {
				player.playVideo();
			}, 10);
		};
		
		RwPlayer.prototype.updatePlayerInfo = function(force) {
			if (this.player === undefined) {
				return;
			}
			
			if (this.duration && !this.playState && !force) {
				//we don't need to update play time if it's not playing
				//this uses a bunch of CPU so don't do it if we don't need it
				return;
			}

			//update play pointer
			var play_bar = $('.play_bar',this.container);
			var play_bar_width = play_bar.width();

			//update bytesLoaded
			/*
			var bytesLoaded = this.player.getVideoBytesLoaded();
			var bytesTotal = this.player.getVideoBytesTotal();
			var startBytes = this.player.getVideoStartBytes();
			play_bar = $('.play_bar',this.container);
			var start_px = play_bar_width * startBytes/bytesTotal;
			var mid_px = play_bar_width * bytesLoaded/bytesTotal;
			
			//update for messed up bytes results. todo: make more sense of this
			if (mid_px + start_px >=play_bar_width) {
				start_px = play_bar_width - mid_px;
			}

			if (this.editingClip || this.clip_start === undefined ||
				this.clip_start === 0 && this.clip_end == this.duration) {
				play_bar.children().eq(2).css('width',start_px + 'px');
				play_bar.children().eq(3).css('width',mid_px + 'px');
				play_bar.children().eq(4).css('width',(play_bar_width-mid_px-start_px) + 'px');
			} else {
				play_bar.children().eq(2).css('width','0');
				play_bar.children().eq(3).css('width','100%');
				play_bar.children().eq(4).css('width','0');
			}
			*/

			//var videoDuration = this.player.getDuration();
			var state = this.player.getPlayerState();
			var currentTime = this.player.getCurrentTime();

			if (!this.editingClip || currentTime < 0 || state === -1 || state === 0) {
				if (currentTime < this.clip_start) {
					currentTime = this.clip_start
				} else if (currentTime > this.clip_end) {
					currentTime = this.clip_end
				}
			}

			if (currentTime < 0) {
				currentTime = 0;
			}

			var duration = this.player.getDuration();
			if (duration > 0) {
				this.duration = duration;
				if (this.clip_end !== undefined && this.clip_end > duration) {
					this.clip_end = duration;
				}
			} else if (duration < 0) {
				duration = this.duration;
			}

			if (this.clip_end !== undefined && currentTime >= this.clip_end &&
				(this.pause_at_end || !this.editingClip)) {
				this.pause();
				this.position_dirty = true;
			}

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
				play_marker.css('left',(play_px - 17) + 'px');

				$('.video_time',this.container).html(this.secondsToTimeString(currentTime) + ' / ' + this.secondsToTimeString(duration));
/*
				if (this.player.getPlayerState() == 1) {
					$('.play_button',this.container).css('background-position','left top');
				} else {
					$('.play_button',this.container).css('background-position','right top');
				}
*/
			}

		};
		
		RwPlayer.prototype.secondsToTimeString = function(seconds) {
			var minutes = Math.floor(seconds / 60);
			var s = Math.round(seconds) % 60;
			if (s < 10) {
				s = '0' + s;
			}
			return minutes + ':' + s;
		};
	}
});

var rwInterval = 0;

function updatePlayerInfo() {
	for (var id in window.rwPlayers) {
		if (window.rwPlayers[id]) {
			window.rwPlayers[id].updatePlayerInfo();
		}
	}
}

function onYouTubePlayerReady(post_id) {
	post_id = parseInt(post_id,10);
	var player = document.getElementById("myytplayer-" + post_id);
	var rwp = window.rwPlayers[post_id];
	rwp.player = player;
	rwp.setUpVideo();
	rwp.setVolume(-1);
	if (!rwInterval) {
		setInterval(updatePlayerInfo, 100);
		updatePlayerInfo(true);
	}
}

