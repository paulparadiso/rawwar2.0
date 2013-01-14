//jQuery(document).ready(function($) {

	//constructor
	function FlexDateForm(element, opt_opts) {
		if (!element) {
			return false;
		} else if (element.tagName) {
			this.container = jQuery(element);
		} else {
			element = document.getElementById(element);
			if (element) {
				this.container = jQuery(element);
			} else {
				return false;
			}
		}

		if (!this.discoverForm(opt_opts.date_field, opt_opts.accuracy_field)) {
			return false;
		}
		
		this.buildForm();
		
		this.enable_errors = false;
		this.year = null;
		this.month = null;
		this.day = null;

		var me = this;

		this.initializeEvents();
		this.initializeValues();
		
		return;
	}

jQuery(function($) {
	//constants
	FlexDateForm.prototype.accuracy_labels = [
		'Decade',
		'Part Decade',
		'Year',
		'Quarter',
		'Date'
	];
	
	FlexDateForm.prototype.discoverForm = function(date_field, accuracy_field) {
		this.elements = {};

		if (date_field instanceof HTMLElement) {
			this.elements.flex_date_value = $(date_field);
		} if (date_field instanceof jQuery) {
			this.elements.flex_date_value = date_field;
		} else if (typeof date_field == 'string') {
			this.elements.flex_date_value = $('input[name=' + date_field + ']',this.container);
		} else {
			return false;
		}
		
		if (date_field instanceof HTMLElement) {
			this.elements.flex_date_accuracy_value = $(accuracy_field);
		} if (date_field instanceof jQuery) {
			this.elements.flex_date_accuracy_value = accuracy_field;
		} else if (typeof date_field == 'string') {
			this.elements.flex_date_accuracy_value = $('input[name=' + accuracy_field + ']',this.container);
		} else {
			return false;
		}
		
		if (!this.elements.flex_date_value ||
			!this.elements.flex_date_value.length ||
			!this.elements.flex_date_accuracy_value ||
			!this.elements.flex_date_accuracy_value.length
			) {
			return false;
		} else {
			return true;
		}
	}


	FlexDateForm.prototype.buildForm = function() {
		//var flex_date_forms = this.container;
		var e = this.elements;

		this.container.addClass('flex_date_container');

		var flex_date_forms = $('<div></div>').appendTo(this.container).addClass('flex_date_forms');
		e.flex_date_forms = flex_date_forms;

		var div = $('<div></div>').appendTo(flex_date_forms).addClass('flex_date_form');

		e.flex_date_decade = $('<select></select>').addClass('year').addClass('flex_date_decade').appendTo(div);
		var option = document.createElement('option');
		if(option) {
			e.flex_date_decade.append(option);
		}

		//set up decade options
		var now = new Date();
		var this_year = now.getFullYear();
		var baseyear = this_year - this_year % 10;
		var i, decade;
		for (i = 0; i <= 110; i += 10) {
			decade = baseyear - i;
			e.flex_date_decade.append('<option value="' + decade + '">' + decade + 's</option>');
		}

		div = $('<div></div>').appendTo(flex_date_forms).addClass('flex_date_form');
		e.flex_date_decade_part = $('<select></select>').addClass('year').addClass('flex_date_decade_part').appendTo(div);
		option = document.createElement('option');
		if(option) {
			e.flex_date_decade_part.append(option);
		}
		this_year %= 10;
		decade = baseyear;
		if (this_year >= 6) {
			e.flex_date_decade_part.append('<option value="' + (decade + 8) + '">Late ' + decade + 's</option>');
		}
		if (this_year >= 3) {
			e.flex_date_decade_part.append('<option value="' + (decade + 5) + '">Mid ' + decade + 's</option>');
		}
		e.flex_date_decade_part.append('<option value="' + (decade + 1) + '">Early ' + decade + 's</option>');
		for (i = 10; i < 110; i += 10) {
			decade = baseyear - i;
			e.flex_date_decade_part.append('<option value="' + (decade + 8) + '">Late ' + decade + 's</option>');
			e.flex_date_decade_part.append('<option value="' + (decade + 5) + '">Mid ' + decade + 's</option>');
			e.flex_date_decade_part.append('<option value="' + (decade + 1) + '">Early ' + decade + 's</option>');
		}


		div = $('<div></div>').appendTo(flex_date_forms).addClass('flex_date_form');
		e.flex_date_year = $('<select></select>').addClass('year').addClass('flex_date_year').appendTo(div);
		option = document.createElement('option');
		if(option) {
			e.flex_date_year.append(option);
		}
		for (i = now.getFullYear(); i >= 1890; i--) {
			e.flex_date_year.append('<option>' + i + '</option>');
		}


		div = $('<div></div>').appendTo(flex_date_forms).addClass('flex_date_form');
		e.flex_date_year_q = $('<select></select>').addClass('year').addClass('flex_date_year_q').appendTo(div);
		option = document.createElement('option');
		if(option) {
			e.flex_date_year_q.append(option);
		}
		for (i = now.getFullYear(); i >= 1890; i--) {
			e.flex_date_year_q.append('<option>' + i + '</option>');
		}

		e.flex_date_quarter = $('<select></select>').addClass('flex_date_quarter').appendTo(div);
		option = document.createElement('option');
		if(option) {
			e.flex_date_quarter.append(option);
		}
		for (i = 1; i < 4; i++) {
			e.flex_date_quarter.append('<option value="' + i + '">Q' + i + '</option>');
		}


		div = $('<div></div>').appendTo(flex_date_forms).addClass('flex_date_form');

		e.flex_date_month = $('<select></select>').addClass('month').addClass('flex_date_month').appendTo(div);
		option = document.createElement('option');
		if(option) {
			e.flex_date_month.append(option);
		}
		var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
		for (i = 0; i < 12; i++) {
			e.flex_date_month.append('<option value="' + (i + 1) + '">' + months[i] + '</option>');
		}


		e.flex_date_day = $('<select></select>').addClass('day').addClass('flex_date_day').appendTo(div);
		option = document.createElement('option');
		if(option) {
			e.flex_date_day.append(option);
		}
		for (i = 1; i <= 31; i++) {
			e.flex_date_day.append('<option value="' + i + '">' + i + '</option>');
		}


		e.flex_date_year_d = $('<select></select>').addClass('year').addClass('flex_date_year_d').appendTo(div);
		option = document.createElement('option');
		if(option) {
			e.flex_date_year_d.append(option);
		}
		for (i = now.getFullYear(); i >= 1890; i--) {
			e.flex_date_year_d.append('<option>' + i + '</option>');
		}

		e.flex_date_accuracy_label = $('<label></label>').addClass('flex_date_accuracy_label').appendTo(this.container);

		var textNode = document.createTextNode('Accuracy: ');
		if(textNode) {
			e.flex_date_accuracy_label.append(textNode);
		}

		var span = document.createElement('span');
		if(span) {
			e.flex_date_accuracy_label.append(span);
		}

		this.container.append('<br/>');

		e.flex_date_accuracy = $('<div></div>').addClass('slider').addClass('flex_date_accuracy').appendTo(this.container);

		//now add slider
		//e.flex_date_accuracy.appendTo(slider);

		var me = this;
		e.flex_date_accuracy.slider({
			value:4,
			min: 0,
			max: 4,
			step: 1,
			slide: function(event, ui) {
				me.updateAccuracy(ui.value);
			}
		});
	};
	
	FlexDateForm.prototype.initializeValues = function() {

		//initialize date values
		var accuracy = 4;
		switch (this.elements.flex_date_accuracy_value.val()) {
			case 'decade':
				accuracy = 0; break;
			case 'part_decade':
				accuracy = 1; break;
			case 'year':
				accuracy = 2; break;
			case 'quarter':
				accuracy = 3; break;
			case 'date':
				accuracy = 4; break;
		}
		this.elements.flex_date_accuracy.slider("value",accuracy);
		
		this.updateAccuracy(accuracy);

		var date_parts = /(\d{4})-(\d\d?)-(\d\d?)/.exec(this.elements.flex_date_value.val());
		if (date_parts && date_parts.length) {
			this.year = parseInt(date_parts[1],10);
			this.month = parseInt(date_parts[2],10);
			this.day = parseInt(date_parts[3],10);
		}
		this.update_form();
	}

	FlexDateForm.prototype.initializeEvents = function() {
		//initialize events for date input
		//$('#flex_date_forms select').bind('change blur',this.updateDate);
		var me = this;
		this.elements.flex_date_decade.bind('change', function(event) {
				me.updateYear(event);
			});
	
		this.elements.flex_date_decade_part.bind('change',  function(event) {
				me.updateYear(event);
			});
		this.elements.flex_date_year.bind('change',  function(event) {
				me.updateYear(event);
			});
		this.elements.flex_date_year_q.bind('change',  function(event) {
				me.updateYear(event);
			});
		this.elements.flex_date_year_d.bind('change',  function(event) {
				me.updateYear(event);
			});
		
		this.elements.flex_date_quarter.bind('change',  function(event) {
				me.updateDate(event);
			});
		this.elements.flex_date_month.bind('change',  function(event) {
				me.updateDate(event);
			});
		this.elements.flex_date_day.bind('change',  function(event) {
				me.updateDate(event);
			});
	}

	//utility function for number of days in a month
	FlexDateForm.prototype.days_in_month = function(year, month) {
		var d;
		if (month == 12) {
			month = 0;
			year++;
		} else {
			//month++;
		}
		
		d = new Date(year, month, 1);
		d = new Date(d.getTime() - (24 * 60 * 60 * 1000)); //go back one day
		return d.getDate();
	};
	
	FlexDateForm.prototype.update_form = function () {
		var now = new Date();
		if (this.year && this.year <= now.getFullYear()) {
			$('select.year',this.container).val(this.year);
			
			//set decade part
			var decade_part = (this.year % 10);
			var decade = this.year - decade_part;
			
			if (this.month && this.month > 0 && this.month <= 12) {
				decade_part += (this.month - 1) / 12;
			
				this.elements.flex_date_month.val(this.month);
				
				var days = this.days_in_month(this.year,this.month);
				if (this.day && this.day > 0 && this.day <= days) {
					this.elements.flex_date_day.val(this.day);
				} else {
					this.elements.flex_date_day.val('');
				}
				
				var quarter = Math.floor((this.month - 1) * 4 / 12) + 1;
				this.elements.flex_date_quarter.val(quarter);
				
				var flex_date_day = this.elements.flex_date_day[0];
				for (var i = 31; i > 28; i--) {
					if (i > days) {
						//flex_date_day.options[i].style.display = 'none';
						flex_date_day.options[i].disabled = true;
					} else {
						//flex_date_day.options[i].style.display = '';
						flex_date_day.options[i].disabled = false;
					}
				}
			} else {
				$('select.month',this.container).val('');
				this.elements.flex_date_quarter.val('');
			}
			
			decade_part *= 3/10;
			
			if (decade_part < 1) {
				decade_part = 1;
			} else if (decade_part < 2) {
				decade_part = 5;
			} else {
				decade_part = 8;
			}			

			this.elements.flex_date_decade.val(decade);
			this.elements.flex_date_decade_part.val((decade + decade_part));
		} else {
			$('select',this.container).val('');
		}
	};

	FlexDateForm.prototype.updateDate = function (event) {
		if (event && event.target) {
			var new_value = parseInt(event.target.value,10);
			if (!new_value) {
				this.year = this.month = this.day = null;
				this.elements.flex_date_value.val('')
			} else {
				var now = new Date();

				if (event.target == this.elements.flex_date_quarter[0]) {
					this.month = (new_value - 1) * 4 + 1;
				} else if (event.target == this.elements.flex_date_month[0]) {
					this.month = new_value;
				} else if (event.target == this.elements.flex_date_day[0]) {
					this.day = new_value;
				}
				
				if (!this.year) {
					this.year = now.getFullYear();
				}
				
				if (this.year == now.getFullYear()) {
					if (!this.month) {
						this.month = now.getMonth() + 1;
						if (this.day && this.day > this.days_in_month(this.year,this.month)) {
							this.month = 1;
						}
					}
					if (!this.day) {
						if (this.month == now.getMonth() + 1) {
							this.day = now.getDate();
						} else {
							this.day = 1;
						}
					}
				} else {
					if (!this.month) {
						this.month = 1;
					}
					if (!this.day) {
						this.day = 1;
					}
				}
				
				var days = 31;
				if (this.day > 28 && this.day > (days = this.days_in_month(this.year,this.month))) {
					this.day = days;
				}
			}
			this.elements.flex_date_value.val(this.year + '-' + this.month + '-' + this.day);
			var accuracy = this.elements.flex_date_accuracy.slider("value");
			this.updateAccuracy(accuracy);
		}
		this.update_form();
	}
	
	FlexDateForm.prototype.updateYear = function (event) {
		if (event && event.target) {
			this.year = parseInt(event.target.value,10);

			if (!this.year) {
				this.month = this.day = null;
				this.elements.flex_date_value.val('')
			} else {
				var now = new Date();
				if (this.year == now.getFullYear()) {
					if (!this.month) {
						this.month = now.getMonth() + 1;
					}
					if (!this.day) {
						this.day = now.getDate();
					}
				} else {
					if (!this.month) {
						this.month = 1;
					}
					if (!this.day) {
						this.day = 1;
					}
				}
				
				this.elements.flex_date_value.val(this.year + '-' + this.month + '-' + this.day);
				var accuracy = this.elements.flex_date_accuracy.slider("value");
				this.updateAccuracy(accuracy);
			}
		}
		this.update_form();
	}

	FlexDateForm.prototype.updateAccuracy = function (val) {
		$('span',this.elements.flex_date_accuracy_label).html(this.accuracy_labels[val]);
		$(".flex_date_form",this.container).each(function(index,element) {
			if (index == val) {
				$(element).css('display','block');
			} else {
				$(element).css('display','none');
			}
		});	

		//save value
		var accuracy = ['decade','part_decade','year','quarter','date'][val];
		this.elements.flex_date_accuracy_value.val(accuracy);
	}

	var rw_youtube_regex = /^(http:\/\/)?(www.)?(youtube\.com\/((watch(\?|#!)v=|v\/)|watch_videos\?([a-zA-Z_\-]+=[^&]*&)*video_ids=)|youtu\.be\/)([A-Za-z0-9_\-]+)/; //id=8
	
	var rw_enable_errors = false;

	if (!window.ajaxurl && window.userSettings && userSettings.ajaxurl) {
		window.ajaxurl = userSettings.ajaxurl;
	}
	
	function rw_validate_video_all(event,no_server) {
		var valid = rw_validate_video_url(event,no_server);

		//disable submit button
		if (!valid && rw_enable_errors) {
			$('#publish').addClass('button-primary-disabled');
			$('#save-post').addClass('button-disabled');
			$('#post-preview').addClass('button-disabled');
		} else {
			$('#publish').removeClass('button-primary-disabled');
			$('#save-post').removeClass('button-disabled');
			$('#post-preview').removeClass('button-disabled');
		}

		return valid;
	}

	var old_video_url;
	function rw_validate_video_url(event,no_server) {
		//first validate URL
		var video_url = $('#video_url_input');
		if (!video_url) {
			return true;
		}
		
		video_url = $.trim(video_url.val());
		var video_url_valid = true;

		var video_url_error = '';
		if (video_url == ''){
			video_url_valid = false;
			video_url_error = "Please enter a YouTube video watch page URL";
		} else {	
			if (!rw_youtube_regex(video_url)){
				video_url_valid = false;
				video_url_error = "Not a valid YouTube video URL";
			} else {
				//valid = true;
			}
		}
		
		//todo: turn yellow if blank and not error and pc is not blank
		if (video_url_error == '' || !rw_enable_errors) {
			$('#video_url_input').css('background-color','');
			$('#video_url_error').html('&nbsp;');

			if (video_url_valid && !no_server && old_video_url != video_url) {
				//submit url to server for validation	
				$.ajax({ url: ajaxurl, data:{
						action: 'validate_video_url',
						video_url: video_url,
						post_id: $('#post_ID').val()
					}, type:'POST', dataType: 'json', success: function(response){
					var video_preview = $('#video_preview');
					var recaptcha_widget_div = $('#recaptcha_widget_div');
					rw_enable_errors = true;
					if (response.error) {
						rw_validate_video_all(event,true);
						if (rw_enable_errors || response.error !== 'invalid-url') {
							$('#video_url_input').css('background-color','#F28484');
							$('#video_url_error').html(response.error_msg); //todo: encode?
						}
						if (recaptcha_widget_div && recaptcha_widget_div.length && window.Recaptcha && window.Recaptcha.reload) {
							Recaptcha.reload();
						}
						
						//clear preview
						video_preview.replaceWith('<div id="video_preview"></div>');
						$('#video_preview_keywords').empty();
						$('#video_preview_title').empty();
						$('#video_preview_description').empty();
						$('#video_preview_stats').css('display','none');						
					} else {
						//Recaptcha.reload();
						$('#video_url_input').css('background-color','');
						$('#video_url_error').html('&nbsp;');
						rw_validate_video_all(event,true);
						
						//update preview
						$('#video_preview_stats').css('display','block');
						if (video_preview && video_preview.length) {
							swfobject.embedSWF('http://www.youtube.com/v/' + response.video_id + '&amp;hl=en_US&amp;fs=1', 'video_preview', 233, 200, '8', null, null, {
								allowFullScreen: 'true',
								allowscriptaccess: 'always'
							});
						}
						
						var video_date = $('#video_date_container input[name=video_date]');
						if (response.recorded_date && video_date && !video_date.val()) {
							video_date.val(response.recorded_date);
							var video_date_accuracy = $('#video_date_container input[name=video_date_accuracy]');
							video_date_accuracy.val('date');
							videoFlexDate.initializeValues();
						}

						//title and description
						$('#video_preview_title').html(response.title);
						
						var short_descr = response.description;
						short_descr = short_descr.replace(/(<([^>]+)>)/ig,'');
						short_descr = short_descr.replace(/[\n\r]/ig,'<br/>');
						/*
						short_descr = short_descr.split(/[\n\r\t ]+/,56);
						if (short_descr.length > 55) {
							short_descr.pop();
							short_descr = short_descr.join(' ') + '...';
						} else {
							short_descr = short_descr.join(' ');
						}
						*/
						$('#video_preview_description').html(short_descr);

						//tag cloud
						var video_preview_keywords = $('#video_preview_keywords');
						video_preview_keywords.empty();
						for (var i = 0; i < response.keywords.length; i++) {
							//todo: filter keyword
							video_preview_keywords.append('<li><a href="#">' + response.keywords[i].trim() + '</a></li>');
							//tagBox.flushTags($('#post_tag').closest('.inside'),a);
						}
						$('a',video_preview_keywords).click(function() {
							tagBox.flushTags($('#post_tag').closest('.inside'),this);
							return false;
						});
					}
					return;
				}});
			} else if (!video_url_valid) {
				//clear preview
				$('#video_preview').replaceWith('<div id="video_preview"></div>');
				$('#video_preview_stats').css('display','none');						
				$('#video_preview_keywords').empty();
				$('#video_preview_title').empty();
				$('#video_preview_description').empty();
			}
		} else {
			$('#video_url_input').css('background-color','#F28484');
			$('#video_url_error').html(video_url_error); //todo: encode?

			//clear preview
			$('#video_preview').replaceWith('<div id="video_preview"></div>');
			$('#video_preview_stats').css('display','none');						
			$('#video_preview_keywords').empty();
			$('#video_preview_title').empty();
			$('#video_preview_description').empty();
		}

		old_video_url = video_url;
		
		return video_url_valid;
	}
	
	function rw_trap_submit(event) {
		rw_enable_errors = true;
		return rw_validate_video_all(event,true);
	}
	
	$('#publish').click(rw_trap_submit);
	$('#save-post').click(rw_trap_submit);
	$('#post-preview').click(rw_trap_submit);

	//initialize video url input
	$('#video_url_input').bind('change keyup',rw_validate_video_all);


	var videoFlexDate = new FlexDateForm('video_date_container',{
		date_field: 'video_date',
		accuracy_field: 'video_date_accuracy'
	});
	
	var workFlexDate = new FlexDateForm('work_date_container',{
		date_field: 'work_date',
		accuracy_field: 'work_date_accuracy'
	});

	rw_validate_video_url();		
});