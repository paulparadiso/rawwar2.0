<?php
// Template Name: archive index Template
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title>Women Art Revolution | Add an Artwork</title>
		<link type="text/css" href="css/archive.css" rel="Stylesheet" />
		<link type="text/css" href="css/smoothness/jquery-ui-1.8.6.custom.css" rel="Stylesheet" />
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.8.6.custom.min.js"></script>
		<script type="text/javascript" src="js/jquery.autoclear.min.js"></script>
		<script type="text/javascript" src="js/archive.js"></script>
		<script type="text/javascript" src="js/testdata.js"></script>
	</head>
	<body>
	
	<div id="traceBox" style="display:none;"></div>
	
	<div id="dimmer" class="translucent" style="display:none;"></div>
	
	<div id="viewer" style="display:none;">
		<!-- display:none; -->
		<a href="javascript:closeViewer();">
		<img style="float:right; margin-top:-10px; margin-right:-10px;" src="img/rawwar-site_60.png" alt="rawwar-site_60" width="12" height="12" /></a>
		<br style="clear: both;" />
		<div id="viewer-video">
			
		</div>
		<div id="item-info">
			<div class="item-headline">
				&laquo;&nbsp;<span id="item-type"></span><span id="item-title">Title of Artwork</span>&nbsp;&raquo;
			</div>
			
<!-- OLD
						<div class="item-property">ARTIST <span id="item-artist">Artist Name</span></div>
			<div class="item-property">DATE <span id="item-date">2004</span></div>
			<div class="item-property">TAGS <span id="item-tags">Woman, Innocent, Post-modern</span></div>
			<div class="item-property">DESCRIPTION <span id="item-description">No description for this artwork.</span></div>
-->

			
			<table class="item-property" cellspacing="10">
				<tr>
					<td align="right" valign="top"><img src="img/rawwar-archive-play-artist.png" alt="rawwar-archive-play-artist" width="48" height="14" /></td>
					<td><span id="item-artist">Artist Name</span></td>
				</tr>
				
				<tr>
					<td align="right" valign="top"><img src="img/rawwar-archive-play-date.png" alt="rawwar-archive-play-date" width="38" height="11" /></td>
					<td><span id="item-date">Date</span></td>
				</tr>
				
				<tr>
					<td align="right" valign="top"><img src="img/rawwar-archive-play-tags.png" alt="rawwar-archive-play-tags" width="38" height="12" /></td>
					<td><span id="item-tags">Tags</span></td>
				</tr>
				
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
		
		<div class="bg translucent">
			<!-- <img src="img/rawwar-site-background1.png" alt="rawwar-site-background1" width="1280" height="1600" /> -->
		</div>
		
		<div id="content">
		
			<!-- BEGIN HEADER -->
			
			<div id="header">
			
				<img id="logo" src="img/rawwar-site_03.png" alt="rawwar-site_03" width="226" height="151" style="float:left;" />
				
				<span class="courier" style="margin: 30px 14px;">Browse the online archive of artwork submitted by users and our organization.</span>
				<br />
				
				<div style="margin-top:30px;">
					<img src="img/rawwar-site_06.png" alt="rawwar-site_06" width="101" height="46" />
					<a href="submit.html">
						<img src="img/rawwar-site_down_07.png" alt="rawwar-site_down_07" width="90" height="46" />
					</a>
					<a href="#">
						<img src="img/rawwar-site_down_08.png" alt="rawwar-site_down_08" width="72" height="46" />
					</a>
					<a href="events.html">
						<img src="img/rawwar-site_down_09.png" alt="rawwar-site_down_09" width="95" height="46" />
					</a>
					<a href="#">
						<img src="img/rawwar-site_down_10.png" alt="rawwar-site_down_10" width="223" height="46" />
					</a>
					<a href="#">
						<img src="img/rawwar-site_down_11.png" alt="rawwar-site_down_11" width="89" height="45" />
					</a>
				</div>
			</div>
			
			<div style="clear:both;"></div>
			
			<!-- BEGIN SUBMISSION INFO PANEL -->
			
			<div id="submit-info">
			
				<div class="hline"></div>
				<div class="close">
					<a href="javascript:closeSubmitInfo()">
						<img src="img/close.png" alt="close" width="46" height="14" />
						<img src="img/rawwar-site_60.png" alt="rawwar-site_60" width="12" height="12" />
					</a>
				</div>
				<div style="clear:both;"></div>
				
				<div style="float:left; margin-right:40px;">
				<!-- RAW/WAR is built on user contributions, with the goal of creating a history defined by the community. Submit an artwork or start by exploring the archive below. -->
					<img src="img/description.png" alt="description" width="525" height="139" />
				</div>
				
				<a href="/?page_id=298">
					<img src="img/submit_artwork.png" alt="submit_artwork" width="330" height="48" />
				</a>
				<br />
				<a href="javascript:showGettingStarted();" style="font-size: 14px;">
					<img src="img/getting-started.png" alt="getting-started" width="164" height="13" />
				</a>
				<div style="clear:both;"></div>
			</div>
			
			<br />

			<br style="clear:both;" />
			<!-- BEGIN ARCHIVE SECTION -->
			
			<div id="archive">
				
				<img style="float:left; margin-right:20px; margin-top:-1px;" src="img/rawwar-site_25.png" alt="rawwar-site_25" width="274" height="23" />
				
				<div style="margin-left:15px;">
				<span class="courier" style="font-size:12px; color:gray;">
					<span id="num-artists" class="filter-selection">768</span> Artists <span style="font-size:11px;">and</span> <span id="num-artworks-total" class="filter-selection">1,450</span> Works online
				</span>
				</div>
				
				<br />
				
				<input id="archive-search" class="courier" type="text" name="archive-search" value="Search by Keyword"></input>
				
				<a href="javascript:doSearch();" style="">
					<img id="search-icon" src="img/rawwar-site_29.png" alt="rawwar-site_29" width="13" height="17" />
				</a>
				
				<a href="" style="float:right;">
					<img src="img/rawwar-site_32.png" alt="rawwar-site_32" width="164" height="13" />
				</a>
				
				<br /><br />
				
				<!-- BEGIN SEARCH FILTER SECTION -->
				<div id="search-filter">
					<a href="javascript:toggleFilterView();" style="margin-right:70px;">
						<img id="filter-open-arrow" src="img/rawwar-site_40.png" alt="rawwar-site_40" width="16" height="10" />
						<img src="img/rawwar-site_down_12.png" alt="rawwar-site_down_12" width="187" height="17" />
					</a>
					
					<img src="img/rawwar-site_43.png" alt="rawwar-site_43" width="48" height="11"/>
					<span id="theme-selection" class="filter-selection">Multiple</span>
					
					<img src="img/rawwar-site_46.png" alt="rawwar-site_46" width="48" height="11"  style="margin-left:58px;"/>
					<span id="media-selection" class="filter-selection">All</span>
					
					<img src="img/rawwar-site_49.png" alt="rawwar-site_49" width="57" height="11"  style="margin-left:50px;"/>
					<span id="decade-selection" class="filter-selection">All</span>
					
					<img src="img/rawwar-site_51.png" alt="rawwar-site_51" width="36" height="11"  style="margin-left:40px;"/>
					<span id="tags-selection" class="filter-selection">Multiple</span>
					
					<div id="filter-collapsible" style="display:none;">
						<!-- display:none; -->
						<div id="filter-info" class="">
							<br />
							&gt; Choose your search filter(s) from the categories on the right to narrow your results.
							<br /><br />
							&gt; You may select multiple filters.
							
							<div style="background-color:#333333;height:1px; margin:13px 0px;"></div>
							<br />
							<img src="img/rawwar-site_64.png" alt="rawwar-site_64" width="205" height="16" />
						</div>
						
						<div id="filter-theme" class="filter-section">
							<input type="checkbox" name="option1" value="0" class="cb cb-theme"> All Themes<br>
							<div style="background-color:#333333;height:1px; margin:13px 0px;"></div>
							<input type="checkbox" name="option1" value="1" class="cb cb-theme"> The Body Politic<br>
							<input type="checkbox" name="option1" value="2" class="cb cb-theme"> Consciousness Raising<br>
							<input type="checkbox" name="option1" value="3" class="cb cb-theme"> Identity<br>
							<input type="checkbox" name="option1" value="4" class="cb cb-theme"> Media<br>
							<input type="checkbox" name="option1" value="5" class="cb cb-theme"> Activism & Social Protest<br>
						</div>
						
						<div id="filter-media" class="filter-section">
							<input type="checkbox" name="option1" value="0" class="cb cb-media"> All Media<br>
							<div style="background-color:#333333;height:1px; margin:13px 0px;"></div>
							<input type="checkbox" name="option1" value="1" class="cb cb-media"> Video<br>
							<input type="checkbox" name="option1" value="2" class="cb cb-media"> Image<br>
						</div>
						
						<div id="filter-decade" class="filter-section">
							<input type="checkbox" name="option1" value="0" class="cb cb-decade"> All<br>
							<div style="background-color:#333333;height:1px; margin:13px 0px;"></div>
							<input type="checkbox" name="option1" value="1" class="cb cb-decade"> 1960s<br>
							<input type="checkbox" name="option1" value="2" class="cb cb-decade"> 1970s<br>
							<input type="checkbox" name="option1" value="3" class="cb cb-decade"> 1980s<br>
							<input type="checkbox" name="option1" value="4" class="cb cb-decade"> 1990s<br>
							<input type="checkbox" name="option1" value="5" class="cb cb-decade"> 2000s<br>
							<input type="checkbox" name="option1" value="6" class="cb cb-decade"> 2010+<br>
						</div>
						
						<div id="filter-tags" class="filter-section">
							<input type="checkbox" name="option1" value="0" class="cb cb-tags"> All<br>
							<div style="background-color:#333333;height:1px; margin:13px 0px;"></div>
							<input type="checkbox" name="option1" value="1" class="cb cb-tags"> 1960s<br>
							<input type="checkbox" name="option1" value="2" class="cb cb-tags"> 1970s<br>
							<input type="checkbox" name="option1" value="3" class="cb cb-tags"> 1980s<br>
							<input type="checkbox" name="option1" value="4" class="cb cb-tags"> 1990s<br>
							<input type="checkbox" name="option1" value="5" class="cb cb-tags"> 2000s<br>
							<input type="checkbox" name="option1" value="6" class="cb cb-tags"> 2010+<br>
						</div>
					</div>
					<br style="clear:both;" />
				</div>
				
				
				<!-- BEGIN ARCHIVE LIST SECTION -->
				
				<div id="archive-nav" class="courier">
					<span id="num-items">178</span> Artworks
					<img src="img/rawwar-site_68.png" alt="rawwar-site_68" width="76" height="11" />
					<select id="sort-menu">
						<option value="0">Most Recent</option>
						<option value="1">Most Popular</option>
						<option value="2">Location of Contributor</option>
					</select>
					
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

				<div id="footer">
					<a href="#"><img src="img/rawwar-site_72.png" alt="rawwar-site_72" width="49" height="11" /></a>
<a href="#"><img src="img/rawwar-site_74.png" alt="rawwar-site_74" width="67" height="11" /></a>
<a href="#"><img src="img/rawwar-site_77.png" alt="rawwar-site_77" width="52" height="11" /></a>
<a href="#"><img src="img/rawwar-site_80.png" alt="rawwar-site_80" width="58" height="11" /></a>
<img src="img/rawwar-site_83.png" alt="rawwar-site_83" width="87" height="11" />
<a href="#"><img src="img/rawwar-site_85.png" alt="rawwar-site_85" width="78" height="11" /></a>
<a href="#"><img src="img/rawwar-site_87.png" alt="rawwar-site_87" width="64" height="11" /></a>
				</div>
			</div>
		</div>
	</div>
	
	</body>
</html>