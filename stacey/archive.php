<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title>RAW/WAR: Revolution Art Women</title>
        <meta name="keywords" content="raw/war, lynn leeson, lynn hershman leeson, feminist art, sundance, new frontier, revolution, women, art, women art revolution, paradiso projects, new frontier, interactive installation, wiimote, crowdsourced" />
		<meta name="description" content="RAW/WAR is built on user contributions with the goal of creating a history defined by the community." />
		<link type="text/css" href="static/css/archive.css" rel="Stylesheet" />
		<link type="text/css" href="static/css/smoothness/jquery-ui-1.8.6.custom.css" rel="Stylesheet" />
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
		<script type="text/javascript" src="static/js/jquery-ui-1.8.6.custom.min.js"></script>
		<script type="text/javascript" src="static/js/jquery.autoclear.min.js"></script>
		<script type="text/javascript" src="static/js/archive.js"></script>
		<script type="text/javascript" src="static/js/testdata.js"></script>
	</head>
	<body>
	
	<div id="traceBox" style="display:none;"></div>
	
	<div id="dimmer" class="translucent" style="display:none;"></div>
	
	<!-- ****** VIEWER ****** -->
	
	<div id="viewer" style="display:none;">
		<!-- display:none; -->
		<a href="javascript:closeViewer();">
		<img style="float:right; margin-top:-10px; margin-right:-10px;" src="img/rawwar-site_60.png" alt="rawwar-site_60" width="12" height="12" /></a>

		<br style="clear: both;" />

		<div id="viewer-video"></div>

		<div id="item-in
fo">
			<div class="item-headline">
				&laquo;&nbsp;<span id="item-type"></span><span id="item-title">Title of Artwork</span>&nbsp;&raquo;
			</div>
			
			<table class="item-property" cellspacing="10">
				<tr>
					<td align="right" valign="top"><img src="img/rawwar-archive-play-artist.png" alt="rawwar-archive-play-artist" width="48" height="14" /></td>
					<td><span id="item-artist">Artist Name</span></td>
				</tr>
				
				<tr>
					<td align="right" valign="top">
						<img src="img/rawwar-archive-date-created.png" alt="rawwar-archive-date-created" width="91" height="9" />
					</td>
					<td><span id="item-date">Date</span></td>
				</tr>
				
				<tr>
					<td align="right" valign="top">
						<img src="img/rawwar-archive-date-added.png" alt="rawwar-archive-date-added" width="91" height="9" />
					</td>
					<td><span id="item-date-added">Date</span></td>
				</tr>
				
				<tr>
					<td align="right" valign="top">
						<img src="img/rawwar-archive-play-media.png" alt="rawwar-archive-play-media" width="48" height="12" />
					</td>
					<td><span id="item-media">Media</span></td>
				</tr>
				<!--
				<tr>
					<td align="right" valign="top"><img src="img/rawwar-archive-play-tags.png" alt="rawwar-archive-play-tags" width="38" height="12" /></td>
					<td><span id="item-tags">Tags</span></td>
				</tr>
				-->
				<tr>
					<td align="right" valign="top"><img src="img/rawwar-archive-play-desc.png" alt="rawwar-archive-play-desc" width="87" height="14" /></td>
					<td><span id="item-description">No description for this artwork.</span></td>
				</tr>
			</table>
		</div> <!-- END item-info -->
		<!--
<div id="item-sidebar">
			Comments(2)<br />
			Share
			<div id="item-related">
				RELATED
			</div>
		</div>
-->
	</div>
	
	<div id="content-container">
		
		<div id="content">
		
			<!-- BEGIN HEADER -->
											  
			<div id="header">		
															      <br />
			
				<img id="logo" src="static/img/rawwar-site_03.png" alt="rawwar-site_03" width="226" height="151" style="float:left;" />
				<div style="margin-top:30px;">
					<img src="static/img/rawwar-site_06.png" alt="rawwar-site_06" width="101" height="46" />
					<a href="submit">
						<img src="static/img/rawwar-site_down_07.png" alt="Subimt" width="90" height="46" />
					</a>
					<a href="http://womenartrevolution.com" target="_blank">
						<img src="static/img/rawwar-site_down_08.png" alt="The Film" width="72" height="46" />
					</a>
					<a href="events">
						<img src="static/img/rawwar-site_down_09.png" alt="Events" width="95" height="46" />
					</a>
						<a href="/view/">
						<img src="static/img/rawwar-site_down_10.png" alt="View" width="223" height="46" />
					</a>
					<a href="about">
						<img src="static/img/rawwar-site_down_11.png" alt="About" width="89" height="45" />
					</a>
				</div>
			</div>
			
			<div style="clear:both;"></div>

			<!-- BEGIN SUBMISSION INFO PANEL -->
			
			<div id="submit-info">
				<div class="hline"></div>
				<div class="close">
					<a href="javascript:closeSubmitInfo()">
						<img src="static/img/close.png" alt="close" width="46" height="14" />
						<img src="static/img/rawwar-site_60.png" alt="rawwar-site_60" width="12" height="12" />
					</a>
				</div>
				<div style="clear:both;"></div>
				
				<div style="float:left; margin-right:40px;">
				<!-- RAW/WAR is built on user contributions, with the goal of creating a history defined by the community. Submit an artwork or start by exploring the archive below. -->
					<img src="static/img/description.png" alt="description" width="525" height="139" />
				</div>
				
				<a href="http://184.106.93.224/submit/">
					<img src="static/img/submit_artwork.png" alt="submit_artwork" width="330" height="48" />
				</a>
				<br />
				
				<!-- <a href="javascript:showGettingStarted();" style="font-size: 14px;">
					<img src="img/getting-started.png" alt="getting-started" width="164" height="13" />
				</a>-->
				<div style="clear:both;"></div>
			</div>
			
			<br />

			<br style="clear:both;" />
			<!-- BEGIN ARCHIVE SECTION -->
			
			<div id="archive">
				
				<img style="float:left; margin-right:20px; margin-top:-1px;" src="img/rawwar-site_25.png" alt="rawwar-site_25" width="274" height="23" />
				
				<div style="margin-left:15px;">
				<span class="courier" style="font-size:12px; color:gray;">
					<span id="num-artists" class="filter-selection">
													      
<?php
global $wpdb;
$artist_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM artists;"));
echo trim($artist_count);
?>

</span> &nbsp;Artists <span style="font-size:11px;">and</span> <span id="num-artworks-total" class="filter-selection">
<?php
global $wpdb;
$artwork_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM artworks;"));
echo trim($artwork_count);
?>

</span> &nbsp;Works online
				</span>
				</div>
				
				<br />
				
				<!-- SEARCH INPUT -->
				
				<input id="archive-search" class="courier" type="text" name="archive-search" value="Search by Keyword"></input>
				
				<a href="javascript:doSearch();" style="">
					<img id="search-icon" src="static/img/rawwar-site_29.png" alt="rawwar-site_29" width="13" height="17" />
				</a>
				
				<a href="javascript:clearSearch();">
					<img id="clear-search-icon" style="display:none;" src="static/img/rawwar-site_60.png" alt="rawwar-site_60" width="12" height="12" />
				</a>
				
				<span id="searching-notification">
					Searching...
				</span>
				
				<a href="http://184.106.93.224/submit/" style="float:right;">
					<img src="static/img/rawwar-site_32.png" alt="rawwar-site_32" width="164" height="13" />
				</a>
				
				<br /><br />
				
				<!-- BEGIN SEARCH FILTER SECTION -->
				<div id="search-filter">
					<a href="javascript:toggleFilterView();" style="margin-right:70px;">
						<img id="filter-open-arrow" src="static/img/rawwar-site_40.png" alt="rawwar-site_40" width="16" height="10" />
						<img src="static/img/rawwar-site_down_12.png" alt="rawwar-site_down_12" width="187" height="17" />
					</a>
					
					<img src="static/img/rawwar-site_43.png" alt="rawwar-site_43" width="48" height="11"/>
					<span id="theme-selection" class="filter-selection">Multiple</span>
					
					<img src="static/img/rawwar-site_46.png" alt="rawwar-site_46" width="48" height="11"  style="margin-left:58px;"/>
					<span id="media-selection" class="filter-selection">All</span>
					
					<img src="static/img/rawwar-site_49.png" alt="rawwar-site_49" width="57" height="11"  style="margin-left:50px;"/>
					<span id="decade-selection" class="filter-selection">All</span>
		                        <!--
					<img src="img/rawwar-site_51.png" alt="rawwar-site_51" width="36" height="11"  style="margin-left:40px;"/>
		                        <span id="tags-selection" class="filter-selection"><Multiple</span>
		                        -->
					<img src="static/img/rawwar-site_blank.png" alt="rawwar-site_blank" width="36" height="11"  style="margin-left:40px;"/>
		                        <!--<span id="tags-selection" class="filter-selection">&nbsp;</span>-->
					
					<div id="filter-collapsible" style="display:none;">
						<!-- display:none; -->
						<div id="filter-info" class="">
							<br />
							&gt; Choose your search filter(s) from the categories on the right to narrow your results.
							<br /><br />
							&gt; You may select multiple filters.
							
							<!--
<div style="background-color:#333333;height:1px; margin:13px 0px;"></div>
							<br />
							<img src="img/rawwar-site_64.png" alt="rawwar-site_64" width="205" height="16" />
-->
						</div>
						
						<div id="filter-theme" class="filter-section">
							<!-- <input type="checkbox" name="all" value="all" class="cb cb-theme"> All Themes<br> -->
							<a href="javascript:selectAll('filter-theme');">Select All &gt;</a>
							<div style="background-color:#333333;height:1px; margin:13px 0px;"></div>
							<input type="checkbox" name="1" value="4" class="cb cb-theme"> The Body Politic<br>
							<input type="checkbox" name="2" value="3" class="cb cb-theme"> Consciousness Raising<br>
							<input type="checkbox" name="3" value="7" class="cb cb-theme"> Identity<br>
							<input type="checkbox" name="4" value="5" class="cb cb-theme"> Media<br>
							<input type="checkbox" name="5" value="6" class="cb cb-theme"> Social Protest<br>
						</div>
						
						<div id="filter-media" class="filter-section">
							<!-- <input type="checkbox" name="all" value="all" class="cb cb-media"> All Media<br> -->
							<a href="javascript:selectAll('filter-media');">Select All &gt;</a>
							<div style="background-color:#333333;height:1px; margin:13px 0px;"></div>
							<input type="checkbox" name="video" value="video" class="cb cb-media"> Video<br>
							<!--<input type="checkbox" name="image" value="image" class="cb cb-media">Image<br>-->
						</div>
						
						<div id="filter-decade" class="filter-section">
							<!-- <input type="checkbox" name="option1" value="all" class="cb cb-decade"> All<br> -->
							<a href="javascript:selectAll('filter-decade');">Select All &gt;</a>
							<div style="background-color:#333333;height:1px; margin:13px 0px;"></div>
							<input type="checkbox" name="option1" value="1960" class="cb cb-decade"> 1960s<br>
							<input type="checkbox" name="option1" value="1970" class="cb cb-decade"> 1970s<br>
							<input type="checkbox" name="option1" value="1980" class="cb cb-decade"> 1980s<br>
							<input type="checkbox" name="option1" value="1990" class="cb cb-decade"> 1990s<br>
							<input type="checkbox" name="option1" value="2000" class="cb cb-decade"> 2000s<br>
							<input type="checkbox" name="option1" value="2010" class="cb cb-decade"> 2010+<br>
						</div>
						<!--
						<div id="filter-tags" class="filter-section">
							 <input type="checkbox" name="option1" value="all" class="cb cb-tags"> All<br> 
							<a href="javascript:selectAll('filter-tags');">Select All &gt;</a>
							<div style="background-color:#333333;height:1px; margin:13px 0px;"></div>
							<input type="checkbox" name="option1" value="1" class="cb cb-tags"> tag1<br>
							<input type="checkbox" name="option1" value="2" class="cb cb-tags"> tag2<br>
							<input type="checkbox" name="option1" value="3" class="cb cb-tags"> tag3<br>
							<input type="checkbox" name="option1" value="4" class="cb cb-tags"> tag4<br>
							<input type="checkbox" name="option1" value="5" class="cb cb-tags"> tag5<br>
							<input type="checkbox" name="option1" value="6" class="cb cb-tags"> tag6<br>
						</div> -->
					</div>
					<br style="clear:both;" />
				</div>
				
				
				<!-- BEGIN ARCHIVE LIST SECTION -->
				
				<div id="archive-nav" class="courier">
					<span id="num-items">178</span> Artworks
					<!--
<img src="img/rawwar-site_68.png" alt="rawwar-site_68" width="76" height="11" />
					<select id="sort-menu">
						<option value="0">Most Recent</option>
						<option value="1">Most Popular</option>
						<option value="2">Location of Contributor</option>
					</select>
-->
					
					<span style="margin-left: 50px; font-size:11px;">
						VIEW
						<a href="javascript:setView(VIEW_THUMBS);" id="archive-view-thumbnails">
							<span class="archive-view archive-view-selected">Thumbnails</span>
						</a>
						
						<a href="javascript:setView(VIEW_LIST);" id="archive-view-list">
							<span class="archive-view archive-view-unselected">List</span>
						</a>
					</span>
					
					<span id="page-nav">
						<a href="javascript:pagePrev();">&laquo;</a>
						<span id="page-current">2</span> of <span id="page-total">8</span>
						<a href="javascript:pageNext();">&raquo;</a>
					</span>
				</div>
				
				<div id="archive-list" class="archive">
				</div>
				
				<div id="archive-list-columns" class="archive">
					<div id="list-column-left"></div>
					<div id="list-column-right"></div>
				</div>
				
				<br style="clear:both;">
			
<?php get_footer(); ?>							      
		