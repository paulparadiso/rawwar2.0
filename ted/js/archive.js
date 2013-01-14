/**********************************

	RAW/WAR Website Archive Code
	
	Ted Hayes
	
**********************************/

/************* Constants     **************/

DEBUG = true;

VIEW_THUMBS				= 0;
VIEW_LIST				= 1;
ITEMS_PER_PAGE_THUMBS	= 16;
ITEMS_PER_PAGE_LIST		= 20;
THUMB_WIDTH				= 200;
THUMB_HEIGHT			= 50;

/************* Globals       **************/

var currentView;
var archiveItems;
var currentPage = 0;
var numPages;

/************* Configuration **************/

//var thumbnailTemplate = '<div id="archive-item-%ID%" class="archive-item-thumbnail"><a href="javascript:openItem(%ID%);"><img src="%IMG_SRC%" /></a></div>';

var thumbnailTemplate = '<div id="" class="archive-item-thumbnail"><a href=""><img src="" /></a></div>';

var listTemplate = '<a href=""><div id="" class="archive-item-list"><div class="archive-item-list-bg translucent"></div><div class="archive-item-list-contents"><img src="" class="list-item-image" /><div class="list-item-info"><div class="list-item-property"><span id="list-item-artist">ARTIST</span> - <span id="list-item-date">YEAR</span></div><div class="list-item-property">&laquo;&nbsp;<span id="list-item-title">TITLE OF ARTWORK</span>&nbsp;&raquo;</div><div class="list-item-property"><span class="list-property-name">theme </span><span id="list-item-themes">Themes</span></div><div class="list-item-property"><span class="list-property-name">tagged </span><span id="list-item-tags">Tags</span></div></div></div></div></a>';

var videoEmbedTemplate = '<object width="566" height="450"><param name="movie" value="http://www.youtube.com/v/%VID%?fs=1&amp;hl=en_US"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/%VID%?fs=1&amp;hl=en_US" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="566" height="450"></embed></object>';

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
	
	setView(VIEW_THUMBS);
	
	doSearch();
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

function doSearch(){
	var searchVal = $('#archive-search').val();
	//trace("doSearch: "+searchVal);
	$.ajax({
			type: "POST",
			url: "http://184.106.93.224/?page_id=808",
			data: {
				term:searchVal
			},
			success: function (result) {
				if(result.length){
					archiveItems = $.parseJSON(result);
				} else {
					archiveItems = getTestdata();
				}
				displayItems();
	        },
	        error: function (result) {
	        	trace("like failed: "+result);
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
	
	if(archiveItems)
		displayItems();
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
}

function itemThumbnailOut(){
	$(this).css({'border':'1px black solid'});
}

function openItem(which){
	// open pop-up viewer
	
	var thisItem = archiveItems[which];
	
	// set properties
	$('#item-title').html(thisItem.title);
	$('#item-artist').html(thisItem.name);
	$('#item-date').html(thisItem.work_date);
	$('#item-media').html("not in db yet?");
	$('#item-tags').html("not in db yet?");
	$('#item-description').html(thisItem.description);
	
	// embed video
	
	var re = new RegExp('%VID%','g');
	var vid_embed = videoEmbedTemplate.replace(re, thisItem.foreign_key);
	
	$('#viewer-video').html(vid_embed);
	
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

function displayItems(){
	/*	1. Clear existing items
		2. Repopulate #archive-list from current array with currentView setting
	*/
	
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
			thisObject.children('a').attr('href','javascript:openItem('+i+');');
			thisObject.children('a').children('img').attr('src',thisItem.thumbnail_url);
			thisObject.children('a').children('img').hover(itemThumbnailOver, itemThumbnailOut);
			$('#archive-list').append(thisObject);
		} else {
			// set properties
			thisObject = $(listTemplate);
			thisObject.attr('href','javascript:openItem('+i+');');
			thisObject.children('div').attr('id','archive-item-'+i);
			thisObject.find('img').attr('src',thisItem.thumbnail_url);
			thisObject.find('#list-item-artist').html(thisItem.name);
			thisObject.find('#list-item-date').html(thisItem.work_date);
			thisObject.find('#list-item-title').html(thisItem.title);
			// TODO: map theme indices to theme names
			thisObject.find('#list-item-themes').html(thisItem.themes.toString());
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