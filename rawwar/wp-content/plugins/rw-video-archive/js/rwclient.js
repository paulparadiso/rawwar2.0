jQuery(document).ready(function($) {
	if (window.RW === undefined) {
		window.RW = function(element_id) {	
			//initialize everything
			this.enable_errors = false;
			$('#video_url').bind('change keyup blur',this.validate_all);
			$('#video_email').bind('change keyup blur',this.validate_all);
			$('#video_title').bind('change keyup blur',this.validate_all);
			$('#recaptcha_response_field').bind('change keyup blur',this.validate_all);
			$('#submit_url_button').click(this.submit_url);
			$('#video_back_button').click(function() {
				window.rw.video_id = null;
				$('#add_url_div').css('display','');
				$('#add_video_div').css('display','');
			});
		}

		RW.prototype.email_regex = /^[\w-][\+\w-]*(\.[\+\w-]+)*@([a-z0-9-]+(\.[a-z0-9-]+)*?\.[a-z]{2,6}|(\d{1,3}\.){3}\d{1,3})(:\d{4})?$/;
		RW.prototype.youtube_regex = /^(http:\/\/)?(www.)?(youtube\.com\/((watch(\?|#!)v=|v\/)|watch_videos\?([a-zA-Z_\-]+=[^&]*&)*video_ids=)|youtu\.be\/)([A-Za-z0-9_\-]+)/; //id=8

		RW.prototype.submit_url = function() {
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
				data.post_keywords = $('#video_keywords').val();
				data.video_cat = $('#video_cat').val();
				if (window.Recaptcha && window.Recaptcha.get_challenge && Recaptcha.get_challenge()) {
					data.captcha_response = Recaptcha.get_response();
					data.captcha_challenge = Recaptcha.get_challenge();
				}
				var email = $('#video_email').val();
				if (email == undefined || email.toLowerCase() == 'email (optional)') {
					email = '';
				}
				if (email !== undefined && email !='') {
					data.video_email = email;
				}

				var name = $('#video_name').val();
				if (name !== undefined && name !='') {
					data.video_name = name;
				}

				data.video_id = window.rw.video_id;
			}
			
			//$.post(ajaxurl, data, function(response) {
			//	alert('Got this from the server: ' + response);
			//});
			$.ajax({ url: ajaxurl, data:data, type:'POST', dataType: 'json', success: function(response){
				var recaptcha_widget_div = $('#recaptcha_widget_div');
				if (response.error) {
					window.rw.validate_all();
					$('#video_url').css('background-color','#F28484');
					$('#video_url_error').html(response.error); //todo: encode?
					if (recaptcha_widget_div && recaptcha_widget_div.length && window.Recaptcha && window.Recaptcha.reload) {
						Recaptcha.reload();
					}
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
		
		RW.prototype.validate_all = function() {
			var valid = true;
			valid = window.rw.validate_url();
			valid = window.rw.validate_email() && valid;
			valid = window.rw.validate_title() && valid;
			valid = window.rw.validate_captcha() && valid;

			var video_name = $('#video_name').val();
			if (video_name == '' && ($('#add_video_div').offsetHeight || $('#add_video_div').css('display') == 'block')) {
				$('#video_name_error').html('Please enter your name');
				valid = false;
			} else {
				$('#video_name_error').html('&nbsp;');
			}

			if (!valid && rw.enable_errors) {
				$('#submit_url_button').attr('disabled', 'disabled');
			} else {
				$('#submit_url_button').removeAttr('disabled');
			}
			
			return valid;
		}
		
		RW.prototype.validate_url = function() {
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
		
		RW.prototype.validate_captcha = function() {
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
		
		RW.prototype.validate_title = function() {
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
		
		RW.prototype.validate_email = function() {
			var video_email_valid = true;
			var rw = window.rw;
		
			//first validate email
			var video_email = $('#video_email').val();
			if (!video_email || video_email.toLowerCase() == 'email (optional)') {
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

	}

	window.rw = new RW('add_video_form');
});
