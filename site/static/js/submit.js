/*****************************************

	submit.js
	WOMEN ART REVOLUTION
	
	Ted Hayes / 2010
	
*****************************************/

var tagData = [
	"movie",
	"film",
	"experimental",
	"video",
	"feminist",
	"modern",
	"postmodern",
	"art",
	"documentary",
	"video art"
];

var themeLabels = [];
themeLabels["theme-body"] = false;
themeLabels["theme-con"] = false;
themeLabels["theme-media"] = false;
themeLabels["theme-id"] = false;
themeLabels["theme-act"] = false;

contentSet = false;

var artistStart = "";
var titleStart = "";
var yearStart = "";
var emailStart = "";
var nameStart = "";
var tagsStart = "";

var youtubePattern = /^(http:\/\/|www\.|http:\/\/www\.)?youtube.com\/watch\?(?=.*v=\w+)(?:\S+)?$/;
var emailPattern = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;

var imagePattern = /(.*?)\.(jpg|jpeg|png|gif)$/;

// www.youtube.com/watch?v=PIyQ-f1_bPs

/**** UTILITIES *****************/

function trace(what){
	$('#trace').append(what+"<br />");
}

/**** EVENT HANDLERS ************/

function selectTag(e, ui){
	trace(ui.item.value);
}

function urlChange(){
	var urlVal = $('#url').val();
	if(youtubePattern.test(urlVal)){
		contentSet = true;
		$('#artwork-type').val("video");
		$('#url-check').show();
		$('#url-check').removeClass('ui-icon-alert').addClass('ui-icon-circle-check');
	} else {
		contentSet = false;
		$('#url-check').show();
		$('#url-check').removeClass('ui-icon-circle-check').addClass('ui-icon-alert');
	}
	if(imagePattern.test(urlVal)){
		contentSet = true;
		$('#artwork-type').val("image");
		$('#url-check').show();
		$('#url-check').removeClass('ui-icon-alert').addClass('ui-icon-circle-check');
	} else {
		contentSet = false;
		$('#url-check').show();
		$('#url-check').removeClass('ui-icon-circle-check').addClass('ui-icon-alert');
	}
}

function readmoreClick(){
	//trace("readmoreClick: "+this);
	$(this).parent().children('.theme-description').toggle();
	$(this).parent().children('.theme-readmore').toggle();
	$(this).parent().children('.theme-readless').toggle();
}

function themeClick(){
	var themeBlob = $(this).parents('.theme-blob');
	var check = false;
	if($(this).parents('.theme-blob').children('input').val() == 1){
		check = true;
	}
	$(this).parents('.theme-blob').children('.hide-info').toggle();
	$(this).parents('.theme-blob').children('.show-info').toggle();
	if(check){
		themeBlob.children('.hide-info').children('.theme-check').removeClass('checkBox');
		themeBlob.children('.show-info').children('#theme-body-left').children('#theme-body-left-top').children('.theme-check').removeClass('checkBox');
		themeBlob.children('.hide-info').children('.theme-check').addClass('checkBoxClear');
		themeBlob.children('.show-info').children('#theme-body-left').children('#theme-body-left-top').children('.theme-check').addClass('checkBoxClear');	
	} else {
		themeBlob.children('.hide-info').children('.theme-check').removeClass('checkBoxClear');
		themeBlob.children('.show-info').children('#theme-body-left').children('#theme-body-left-top').children('.theme-check').removeClass('checkBoxClear');
		themeBlob.children('.hide-info').children('.theme-check').addClass('checkBox');
		themeBlob.children('.show-info').children('#theme-body-left').children('#theme-body-left-top').children('.theme-check').addClass('checkBox');	
	}
}

function submitClick(){
	var result = validateForm();
	if(result){	
		var form = document.getElementById("post");		
		form.submit();
	} else {
		//$("#error-overlay").overlay({api:true,left:100,top:200}).load();	
	}
}

function guideClick(){
	$("#overlay").overlay({api:true,left:"center",top:200}).load();
}

function tagClick(){
	$("#tag-overlay").overlay({api:true,left:"center",top:200}).load();
}

function validateForm(){
	var submit = true;
	var urlVal = $('#url').val();
	if(!youtubePattern.test(urlVal) && !imagePattern.test(urlVal)){
		submit = false;
		$('#url-error').html("*Please enter a valid youtube or image link");
	}
	if($('#artistname').val() == artistStart){
		submit = false;
		$('#artist-error').html("*Please enter the artist name(s)");
	}
	
	if($('#tags-input').val() == tagsStart){
		submit = false;
		$('#tag-error').html("*Please add <span class='tag-question'>key words</span> that describe this piece");
	}
	
	if($('#email').val() == emailStart){
		submit = false;
		$('#email-error').html("*Please enter an email address");
	}
	if(!emailPattern.test($('#email').val())){
		submit = false;
		$('#email-error').html("*Please enter an email address");
	}
	if($('#firstname').val() == nameStart){
		submit = false;
		$('#name-error').html("*Please enter your first name");
	}
	return submit;	
}

function checkBoxCheck(){
	if ($(this).hasClass("checkBox")){
   	$(this).removeClass("checkBox");
		$(this).addClass("checkBoxClear");
   } else {
		$(this).removeClass("checkBoxClear");
		$(this).addClass("checkBox");
	}
	if($(this).children("input").val() == "0"){
		$(this).children("input").attr('value','1');	
	} else {
		$(this).children("input").attr('value','0');
	}
	if($(this).parents(".theme-blob").children("input").val() == "0"){
		$(this).parents(".theme-blob").children("input").val("1");	
	} else {
		$(this).parents(".theme-blob").children("input").val("0");
	}
}

function setLocation(){
	var geoURL = "http://www.geoplugin.net/json.gp?jsoncallback=?";
	var city = "";
	var country = "";
	var region = "";
	$.getJSON(geoURL, function(data){
		city = data.geoplugin_city;	
		$('[name=geotag_city]').val(city);
		region = data.geoplugin_region;
		$('[name=geotag_region]').val(region);		
		country = data.geoplugin_countryName;		
		$('[name=geotag_country]').val(country);
		$('#geotag-location').html('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>'+ city + ', ' + region + '</b>');
		$('[name=geotag_country_code]').val(data.geoplugin_countryCode);
		$('[name=geotag_latitude]').val(data.geoplugin_latitude);
		$('[name=geotag_longitude]').val(data.geoplugin_longitude);
	});
	//alert(city);
}

/**** INIT **********************/

$(function(){
	$('#tags-input').autoclear();

	//$('#tags-input').autocomplete({
	//	source: window.tag_array,
	//	select: selectTag
	//});
	//trace("enterTag: "+enterTag);
	//$('#tags-input').result(enterTag);
	
	// Fields
	$('.autoclear').autoclear();
	//$('input').addClass("ui-corner-all");
	//$('#url').blur(urlChange);
	//$('#url').keyup(urlChange);
	$('.submit').click(submitClick);
	$('#content').hide();
	// misc
	$('#url-check').hide();
	$('#add-art-blurb').click(guideClick);
	
	// theme logic	
	$('.theme-description').hide();
	$('.theme-readmore').click(readmoreClick);
	$('.theme-readless').hide();
	$('.theme-readless').click(readmoreClick);

	$('.question-mark').click(themeClick);
	$('.show-info').hide();	
	
	$(".checkBox,.checkBoxClear").click(checkBoxCheck);   
	setLocation();
	$("#overlay").overlay({api:true,left:"center",top:200}).load();
	$(".tag-question").click(tagClick);
});