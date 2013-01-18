
/******************************************

	RAW/WAR Website Archive Code
	
	Ted Hayes
	
*******************************************/

/************* Constants     **************/

DEBUG = false;

VIEW_THUMBS				= 0;
VIEW_LIST				= 1;
ITEMS_PER_PAGE_THUMBS	= 16;
ITEMS_PER_PAGE_LIST		= 20;
THUMB_WIDTH				= 200;
THUMB_HEIGHT			= 50;

/************* Globals       **************/

var currentView;
var archiveItems;	// filtered items
var sourceItems;	// unfiltered items
var currentPage = 0;
var numPages;

// Themes
var themeDict = {
	3: "Consciousness Raising",
	4: "The Body Politic",
	5: "Media",
	6: "Social Protest",
	7: "Identity"
};

// Filters
var filterThemes = [];
var filterMedia = [];
var filterDecade = [];
var filterTags = [];

/************* Configuration **************/

var thumbnailTemplate = '<div id="" class="archive-item-thumbnail"><a href=""><img src="" /></a><div class="archive-item-thumbnail-info" style="display:none;"><span class="thumb-name">ARTIST_NAME</span>&nbsp;-&nbsp;<span class="thumb-date">WORK_DATE</span><br />&laquo;&nbsp;<span class="thumb-title">TITLE</span>&nbsp;&raquo;</div></div>';

//var listTemplate = '<a href=""><div id="" class="archive-item-list"><div class="archive-item-list-bg translucent"></div><div class="archive-item-list-contents"><img src="" class="list-item-image" /><div class="list-item-info"><div class="list-item-property"><span id="list-item-artist">ARTIST</span> - <span id="list-item-date">YEAR</span></div><div class="list-item-property">&laquo;&nbsp;<span id="list-item-title">TITLE OF ARTWORK</span>&nbsp;&raquo;</div><div class="list-item-property"><span class="list-property-name">theme </span><span id="list-item-themes">Themes</span></div><div class="list-item-property"><span class="list-property-name">tagged </span><span id="list-item-tags">Tags</span></div></div></div></div></a>';

var listTemplate = '<a href=""><div id="" class="archive-item-list"><div class="archive-item-list-bg translucent"></div><div class="archive-item-list-contents"><img src="" class="list-item-image" /><div class="list-item-info"><div class="list-item-property"><span id="list-item-artist">ARTIST</span> - <span id="list-item-date">YEAR</span></div><div class="list-item-property">&laquo;&nbsp;<span id="list-item-title">TITLE OF ARTWORK</span>&nbsp;&raquo;</div><div class="list-item-property"><span class="list-property-name">theme </span><span id="list-item-themes">Themes</span></div></div></div></div></a>';

var videoEmbedTemplate = '<object width="480" height="385"><param name="movie" value="/web/20110925233342/http://www.youtube.com/v/%VID%?fs=1&amp;hl=en_US"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="/web/20110925233342/http://www.youtube.com/v/%VID%?fs=1&amp;hl=en_US" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="480" height="385"></embed></object>';

/************* Functions     **************/

$(init);

function trace(what) {
	if(!DEBUG) return;
    $('#traceBox').append(what+"<br />");
}

function init(){
	// ********* INITIALIZE ***********
	if(DEBUG){
		$('#traceBox').show();
	}
	
	$('#archive-search').autoclear();
	
	// init Search Filter
	$('.cb').change(checkboxChange);
	// init Search Filter Parameters
	selectAll("filter-theme", false);
	selectAll("filter-media", false);
	selectAll("filter-decade", false);
	selectAll("filter-tags", false);
	
	setView(VIEW_LIST);
	
	doSearch();
}

function selectAll(which, refresh){
	if(refresh === undefined)
		refresh = true;
	var selector = "#"+which+" > input:checkbox";
	var filterArray;
	
	if(which == "filter-theme"){
		filterThemes = [];
		$(selector).attr('checked',true).each(function(i){
			filterThemes.push($(this).attr('value'));
		});
		$('#theme-selection').html("All");
	}
	if(which == "filter-media"){
		filterMedia = [];
		$(selector).attr('checked',true).each(function(i){
			filterMedia.push($(this).attr('value'));
		});
		$('#media-selection').html("All");
	}
	if(which == "filter-decade"){
		filterDecade = [];
		$(selector).attr('checked',true).each(function(i){
			filterDecade.push($(this).attr('value'));
		});
		$('#decade-selection').html("All");
	}
	if(which == "filter-tags"){
		filterTags = [];
		$(selector).attr('checked',true).each(function(i){
			filterTags.push($(this).attr('value'));
		});
		$('#tags-selection').html("All");
	}
	
	if(refresh){
		currentPage = 0;
		displayItems();
	}
	
}

function checkboxChange(){
	//console.log("value: "+$(this).attr('value'));
	//console.log($(this).hasClass('cb-theme'));
	
	var val = $(this).attr('value');
	var chkd = $(this).attr('checked');
	
	if($(this).hasClass('cb-theme')){
		// add to filter array
		
		filterThemes = [];
		
		$('#filter-theme > input:checkbox').each(function(i){
			if($(this).attr('checked'))
				filterThemes.push($(this).attr('value'));
		});
		
		if(filterThemes.length == $('#filter-theme > input:checkbox').length){
			$('#theme-selection').html("All");
		} else if (filterThemes.length > 1) {
			$('#theme-selection').html("Mixed");
		} else if (filterThemes.length == 1) {
			// need to map theme index to name
			$('#theme-selection').html(themeDict[filterThemes[0]]);
		} else {
			$('#theme-selection').html("None");
		}
	}
	if($(this).hasClass('cb-media')){
		// add to filter array
		
		filterMedia = [];
		
		$('#filter-media > input:checkbox').each(function(i){
			if($(this).attr('checked'))
				filterMedia.push($(this).attr('value'));
		});
		
		if(filterMedia.length == $('#filter-media > input:checkbox').length){
			$('#media-selection').html("All");
		} else if (filterMedia.length > 1) {
			$('#media-selection').html("Mixed");
		} else if (filterMedia.length == 1) {
			$('#media-selection').html(filterMedia[0]);
		} else {
			$('#media-selection').html("None");
		}
	}
	if($(this).hasClass('cb-decade')){
		// add to filter array
		
		filterDecade = [];
		
		$('#filter-decade > input:checkbox').each(function(i){
			if($(this).attr('checked'))
				filterDecade.push($(this).attr('value'));
		});
		
		if(filterDecade.length == $('#filter-decade > input:checkbox').length){
			$('#decade-selection').html("All");
		} else if (filterDecade.length > 1) {
			$('#decade-selection').html("Mixed");
		} else if (filterDecade.length == 1) {
			$('#decade-selection').html(filterDecade[0]);
		} else {
			$('#decade-selection').html("None");
		}
	}
	if($(this).hasClass('cb-tags')){
		// add to filter array
		
		filterTags = [];
		
		$('#filter-tags > input:checkbox').each(function(i){
			if($(this).attr('checked'))
				filterTags.push($(this).attr('value'));
		});
		
		if(filterTags.length == $('#filter-tags > input:checkbox').length){
			$('#tags-selection').html("All");
		} else if (filterTags.length > 1) {
			$('#tags-selection').html("Mixed");
		} else if (filterTags.length == 1) {
			$('#tags-selection').html(filterTags[0]);
		} else {
			$('#tags-selection').html("None");
		}
	}
	currentPage = 0;
	displayItems();
}

function closeSubmitInfo(){
	$('#submit-info').slideUp('slow');
}

function toggleFilterView(){
	$('#filter-collapsible').slideToggle('slow');
}

function getTestdata(){
	//trace("getTestdata()");
	var lastid = -1;
	var data = [];
	for (item in testdata){
		if(testdata[item].id != lastid){
			data.push(testdata[item]);
		}
		lastid = testdata[item].id;
	}
	return data;
}

function clearSearch(){
	$('#archive-search').val('');
	$('#clear-search-icon').hide();
	$('#search-icon').show();
	doSearch();
}

function doSearch(){
	$('#searching-notification').show();
	
	var searchVal = $('#archive-search').val();
	
	if(searchVal != "") {
		// show clear-search icon
		$('#search-icon').hide();
		$('#clear-search-icon').show();
	}
	
	$.ajax({
			type: "POST",
			url: "/web/20110925233342/http://rawwar.org/?page_id=808",
			data: {
				term:searchVal
			},
			success: function (result) {
				if(result.length){
					sourceItems = $.parseJSON(result);
				} else {
					sourceItems = getTestdata();
				}
				currentPage = 0;
				$('#searching-notification').hide();
				displayItems();
	        },
	        error: function (result) {
	        	trace("like failed: "+result);
	        	//$('#searching-notification').html("Error in search");
	        	$('#searching-notification').hide();
	        }
	});
}

function disableLink(which, o){
	which.click(function(e) {
	    return false;
	});
	which.css('cursor','default');
}

function enableLink(which){
	which.unbind('click');
	which.css('cursor','pointer');
}

function setView(to){
	if(currentView == to) return false;
	
	currentView = to;
	
	var activeLink, inactiveLink;
	if(to == VIEW_THUMBS){
		activeLink = "#archive-view-thumbnails";
		inactiveLink = "#archive-view-list";
	} else {
		activeLink = "#archive-view-list";
		inactiveLink = "#archive-view-thumbnails";
	}
	
	disableLink($(activeLink));
	enableLink($(inactiveLink));
	
	$(activeLink+" > span").addClass('archive-view-selected');
	$(activeLink+" > span").removeClass('archive-view-unselected');
	
	$(inactiveLink+" > span").addClass('archive-view-unselected');
	$(inactiveLink+" > span").removeClass('archive-view-selected');
	
	if(archiveItems){
		currentPage = 0;
		displayItems();
	}
}

function getItemTemplate(itemData){
	var result;
	if(currentView == VIEW_THUMBS){
		result = thumbnailTemplate;
		for (key in itemData){
			var re = new RegExp(key,"g");
			result = result.replace(re, itemData[key]);
		}
	} else {
		
	}
	
	return result;
}

function itemThumbnailOver(){
	$(this).css({'border':'1px gray solid'});
	//console.log("this.parent: "+$(this).parent().attr('id'));
	//if($(this).parent().hasClass('.archive-item-thumbnail'))
	$(this).parent().parent().find('.archive-item-thumbnail-info').slideDown('fast');
}

function itemThumbnailOut(){
	$(this).css({'border':'1px black solid'});
	$(this).parent().parent().find('.archive-item-thumbnail-info').slideUp('fast');
}

function openItem(which){
	// open pop-up viewer
	
	var thisItem = archiveItems[which];
	
	// set properties
	$('#item-title').html(thisItem.title);
	$('#item-artist').html(thisItem.name);
	$('#item-date').html(thisItem.work_date);
	$('#item-date-added').html(thisItem.upload_date);
	$('#item-media').html(thisItem.type);
	$('#item-tags').html("");
	$('#item-description').html(thisItem.description);
	
	if(thisItem.type == "video"){
		// embed video
		var re = new RegExp('%VID%','g');
		var vid_embed = videoEmbedTemplate.replace(re, thisItem.foreign_key);
		$('#viewer-video').html(vid_embed);
	}
	
	// position viewer
	var w = $(window).width();
	var vw = 600;//$('#viewer').css('width');
	var left = (w - vw) / 2;
	trace("left: "+left);
	$('#viewer').css('left', left);
	
	$('#dimmer').fadeIn('slow');
	$('#viewer').slideDown('slow');
}

function closeViewer(){
	$('#viewer').slideUp('slow');
	$('#dimmer').fadeOut('slow');
}

function filterItems(){
	// read through sourceItems and put into archiveItems
	archiveItems = [];
	var thisItem;
	var pushIt = false;
	for (var i in sourceItems){
		// test items for match with filter settings
		thisItem = sourceItems[i];
		if(testItem(thisItem))
			archiveItems.push(thisItem);
	}
}

function testItem(which){
	// THEMES
	var themeTest = false;
	for (var i in which.themes){
		if($.inArray(which.themes[i],filterThemes) != -1)
			//return true;
			themeTest = true;
	}
	
	// MEDIA
	var mediaTest = false;
	if(!which.type) which.type = "video";
	if($.inArray(which.type, filterMedia) != -1)
		//return true;
		mediaTest = true;
	
	// DECADE
	var thisYear = parseInt(which.work_date);
	var dec, limit;
	var decTest = false;
	for (var i in filterDecade){
		dec = parseInt(filterDecade[i]);
		if(thisYear >= dec && thisYear <= (dec+10))
			//return true;
			decTest = true;
		if(dec == 2010 && thisYear >= dec)
			//return true; 	// 2010 and up
			decTest = true;
	}
	
	// TAGS
	// TODO
	
	if(themeTest && mediaTest && decTest)
		return true;
}

function displayItems(){
	/*	1. Clear existing items
		2. Repopulate #archive-list from current array with currentView setting
	*/
	filterItems();
	
	var itemsPerPage;
	
	if(currentView == VIEW_THUMBS){
		itemsPerPage = ITEMS_PER_PAGE_THUMBS;
		
	} else {
		itemsPerPage = ITEMS_PER_PAGE_LIST;
	}
	
	$('#archive-list').html('');
	$('#list-column-left').html('');
	$('#list-column-right').html('');
	
	$('#num-items').html(archiveItems.length);
	
	
	numPages = Math.ceil(archiveItems.length / itemsPerPage);
	$('#page-total').html(numPages);
	
	if(currentPage >= numPages)
		currentPage = numPages-1;
	
	$('#page-current').html(currentPage + 1);
	
	var startIndex = currentPage * itemsPerPage;
	var container;
	var thisObject;
	
	var endIndex = startIndex + itemsPerPage;
	if(endIndex > archiveItems.length)
		endIndex = archiveItems.length;
	
	for(var i = startIndex; i < endIndex; i++){
		var thisItem = archiveItems[i];
		
		if(currentView == VIEW_THUMBS){			
			thisObject = $(thumbnailTemplate);
			thisObject.attr('id','archive-item-'+i);
			thisObject.find('a').attr('href','javascript:openItem('+i+');');
			thisObject.find('.thumb-name').html(thisItem.name.toUpperCase());
			thisObject.find('.thumb-date').html(thisItem.work_date);
			thisObject.find('.thumb-title').html(thisItem.title);
			thisObject.find('img').attr('src',thisItem.thumbnail_url).hover(itemThumbnailOver, itemThumbnailOut);
			$('#archive-list').append(thisObject);
		} else {
			// set properties
			thisObject = $(listTemplate);
			thisObject.attr('href','javascript:openItem('+i+');');
			thisObject.children('div').attr('id','archive-item-'+i);
			thisObject.find('img').attr('src',thisItem.thumbnail_url);
			thisObject.find('#list-item-artist').html(thisItem.name.toUpperCase());
			thisObject.find('#list-item-date').html(thisItem.work_date);
			thisObject.find('#list-item-title').html(thisItem.title);
			// map indices to themes
			var themes = "";
			var ending = ", ";
			for (var t in thisItem.themes){
				if(t == thisItem.themes.length - 1)
					ending = "";
				themes += themeDict[thisItem.themes[t]] + ending;
			}
			thisObject.find('#list-item-themes').html(themes);
			// TODO: get tags
			thisObject.find('#list-item-tags').html('Not in DB yet?');
			thisObject.children('div').hover(itemThumbnailOver, itemThumbnailOut);
			
			if(i-startIndex < Math.ceil((endIndex-startIndex) / 2)){
				container = '#list-column-left';
			} else {
				container = '#list-column-right';
			}
			$(container).append(thisObject);
		}
	}
}

function pagePrev(){
	if(currentPage == 0) return false;
	
	currentPage--;
	displayItems();
}

function pageNext(){
	if(currentPage == numPages-1) return false;
	
	currentPage++;
	displayItems();
}