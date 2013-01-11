<?php
// Template Name: archive submit Template
?>

<?php require("art-form-functions.php"); ?>
<?php $return = check_form();

if($return){
	if(make_post() > 0){
		render_thanks();
	} else {
		render_already_have();
	}
} else {
	render_form();
}

function render_form() {
# create category array.  Could be tag array as well				

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title>Women Art Revolution | Add an Artwork</title>
		<link type="text/css" href="css/archive.css" rel="Stylesheet" />
		<link type="text/css" href="css/war.css" rel="Stylesheet" />		
		<link type="text/css" href="css/smoothness/jquery-ui-1.8.6.custom.css" rel="Stylesheet" />
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.8.6.custom.min.js"></script>
		<script type="text/javascript" src="http://184.106.93.224/js/jquery.autoclear.min.js"></script>		
		<script src="http://cdn.jquerytools.org/1.2.5/jquery.tools.min.js"></script>		
		<script type="text/javascript" src="http://184.106.93.224/js/jquery.autoclear.min.js"></script>		
		<script type="text/javascript" src="js/submit.js"></script>
	</head>
	<body>
	
	<div id="content-container">
		
			<!-- BEGIN HEADER -->
			
			<div id="header">
			
				<img id="logo" src="img/rawwar-site_03.png" alt="rawwar-site_03" width="226" height="151" style="float:left;" />
				
				<span class="courier" style="margin: 30px 14px;">Browse the online archive of artwork submitted by users and our organization.</span>
				<br />
				
				<div style="margin-top:30px;">
					<a href="index.html">
						<img src="img/rawwar-site_down_06.png" alt="rawwar-site_06" width="101" height="46" />
					</a>
					<a href="#">
						<img src="img/rawwar-site_08.png" alt="rawwar-site_down_07" width="90" height="46" />
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
			
      <div id="art">
      		<form name="post" action="http://184.106.93.224/?page_id=298" method="post" id="post">
				<input type="hidden" id="referredby" name="referredby" value="" ></input>
				<input type="hidden" id="artwork-type" name="artwork-type" value="video" ></input>
			<div id="form-top">
            	<div id="image-blurb">
               	<div id="add-art-image" ><img class="submit" src="static/img/submit_artwork.png"></div>
                  <div id="add-art-blurb"><span class="add-art-small">SUBMISSION GUIDELINES >></span></div>
               </div>
					<div id="url-url-check">
						<span id="url-error" class="error-text">&nbsp;</span><br>
						<div id="add-url"><input id="url" name="url" type="text" class="autoclear main-input url-input" value="Paste the URL of your video or image here"></input></div>
						<!-- ui-icon-check -->
						<!-- ui-icon-alert -->
						<!-- ui-icon-circle-check -->
						<div id="add-url-check"><div id="url-check" class="ui-icon ui-icon-alert"></div><br class="clearall" /></div>
					</div>
				</div>
				<div id="form-small">                              
               <div id="artist-tags">
                   <div id="add-artist"><img src="static/img/text_artist.png"><br />
                   	<span id="artist-error" class="error-text">&nbsp;</span><br>
                   	<input id="artistname" name="artistname" type="text" class="form-field autoclear main-input" value="Artist's name"></input>
                   </div>
              <div id="add-tags">
                   	<img src="static/img/text_tags.png"><span class="tag-question question-mark">
						<img width="10" src="static/img/question_unclicked.png">					
						</span><br />
                   	<span id="tag-error" class="error-text">&nbsp;</span><br>
							<div id="tag-entry">                     
                     <input id="tags-input" name="tags" type="text" class="form-field autoclear autocomplete main-input" value="Keywords seperated w/commas"></input>
							</div>                  
                   </div>
              </div>
				<!--<div id="tag-container"></div><br />-->                  
             	 <div id="title-year">
             	 	<div id="add-title">
                		<img src="static/img/text_title.png"><br />
                		<span id="title-error" class="error-text">&nbsp;</span><br>
                 		<input id="title" name="post-title" type="text" class="form-field autoclear main-input" value="Title of artwork"></input>
                	</div>
                	<div id="add-year">
                		<img src="static/img/text_year.png"><br />
                		<span id="year-error" class="error-text">&nbsp;</span><br>
                  	<input id="year" name="year" type="text" class="form-field autoclear main-input" value="Artwork creation date"></input>
                	</div>
                </div>
                <div id="email-name-geo">
                  <div id="add-email">
                  	<img src="static/img/text_email.png"><br />
                  	<span id="email-error" class="error-text">&nbsp;</span><br>
							<input id="email" name="email" type="text" class="form-field autoclear main-input" value="Your email address"></input>
						</div>
						<div id="add-name">
							<img src="static/img/text_name.png"><br />
							<span id="name-error" class="error-text">&nbsp;</span><br>
                     <input id="firstname" 	name="firstname" class="form-field autoclear main-input" type="text" value="Your first name"></input>
                   </div>
						 <div id="geotag-container">
							<div class="theme-cb-container">
								<div id="cb" class="checkBoxClear">
									<input type="hidden" name="geotag" value="1"></input>														
								</div>
								<div>
									<img src="static/img/geotag_this.png"><br>
									<h2>This will display your approximate location as<br>&nbsp;&nbsp;&nbsp;<span id="geotag-location"></span> in the archive.	
								</div>
							</div>
							</div>
						</div>			
							<input type="hidden" name="geotag_city" value="none"></input>
							<input type="hidden" name="geotag_region" value="none"></input>
							<input type="hidden" name="geotag_country" value="none"></input>
							<input type="hidden" name="geotag_country_code" value="none"></input>
							<input type="hidden" name="geotag_latitude" value="none"></input>
							<input type="hidden" name="geotag_longitude" value="none"></input>					
						<div id="description-geotag-reason">						
							<div id="add-description">
                   		<img src="static/img/description_name.png"><br />
                   		<span id="description-error" class="error-text">&nbsp;</span><br>
                     	<textarea id="description-input" name="description" type="text" class="form-field-description autoclear desc-input" value="Please enter a brief description of the artwork <br> you are submitting."></textarea>
                   	</div>						
							<div id="geotag-texts">								
								<div id="geotag-description" class="geo">
									<!--<h2>This will display the submitter's location<br> on the web and in the installation as:</h2>
									<span id="geotag-location"><h2><b>Brooklyn, NY</b></h2></span>-->					
								</div>
								<div id="geotag-reason" class="geo">
									<!--<span><h2><i>Geotagging enables us to to a map of<br> 
										where our users are and better understand<br> 
										the role of geography within the artworks<br> 
										in the database.</i></h2>
									</span>-->
								</div>
							</div>
					</div>
					</form>
				<div id="theme-top" class="themes-title">
					<div id="theme-image">
						<img src="http://www.paradisoprojects.com/dev/rawwar/site/images/themes.png"><br>
						<div class="theme-small"><h2>Select the theme(s) that best represent the artwork that you are submitting.  Use the <img width="7" src="http://paradisoprojects.com/dev/rawwar/site/images/question_unclicked.png"> to view details and examples of other pieces that share that theme.</h2></div><br>
					</div>			
				</div>			
				<div class="theme-blob">	
				<input type="hidden" class="theme-input" name="theme-body" value="0"></input>			
				<div id="body-test" class="hide-info">
					<div class="checkBox theme-check">&nbsp;</div>
					<div class="theme-image">
						<img src="http://paradisoprojects.com/dev/rawwar/site/images/the_body_politic.png">				
					</div>		
					<div class="question-mark">
						<img src="http://paradisoprojects.com/dev/rawwar/site/images/question_unclicked.png">					
					</div>		
				</div>					
				<div id="theme-body" class="show-info">
					<div id="theme-body-left">
						<div id="theme-body-left-top">						
							<div class="checkBox theme-check">&nbsp;						
							</div>
							<div id="body-image" class="theme-image">
								<img src="http://paradisoprojects.com/dev/rawwar/site/images/the_body_politic.png">				
							</div>		
							<div id="body-question" class="question-mark">
								<img src="http://paradisoprojects.com/dev/rawwar/site/images/question_clicked.png">					
							</div>
						</div>	
						<div id="theme-body-left-bottom">						
							<div id="desc-body" class="theme-description-new"> 
								<span class="theme-text">The politically inscribed body, referencing suppressive aspects of culture.</span>
							</div>
						</div>
					</div>
					<div id="theme-body-right">						
						<div id="image-body1" class="theme-thumbnail">
							<img title="Rebecca Belmore, Capture of the Beothuk, 2008" src="http://paradisoprojects.com/dev/rawwar/site/images/Belmore_TheBodyPolitic_crop.png"><br>
							 <span class="theme-text"></span>
						</div>
						<div id="image-body2" class="theme-thumbnail">
							<img title="Martha Rosler, Semiotics of the Kitchen, 1975" src="http://paradisoprojects.com/dev/rawwar/site/images/SemioticsOfTheKitchen_Identity_crop.png"><br>
							 <span class="theme-text"></span>
						</div>
						<div id="image-body3" class="theme-thumbnail">
							<img title="Liv Marsico, La Selva, 2010" src="http://paradisoprojects.com/dev/rawwar/site/images/LaSelva_TheBodyPolitic_crop.png"><br>
							 <span class="theme-text">	</span>	
						</div>				
					</div>
				</div>
				</div>
				<div class="theme-blob">
				<input type="hidden" name="theme-con" value="0"></input>					
				<div id="body-test" class="hide-info">
					<div class="checkBox theme-check">													
					</div>
					<div class="theme-image">
						<img src="http://paradisoprojects.com/dev/rawwar/site/images/consciousness.png">				
					</div>		
					<div class="question-mark">
						<img src="http://paradisoprojects.com/dev/rawwar/site/images/question_unclicked.png">					
					</div>		
				</div>					
				<div id="theme-body" class="show-info">
					<div id="theme-body-left">
						<div id="theme-body-left-top">						
							<div id="body-cb" class="checkBox theme-check">&nbsp;						
							</div>
							<div id="body-image" class="theme-image">
								<img src="http://paradisoprojects.com/dev/rawwar/site/images/consciousness.png">				
							</div>		
							<div id="body-question" class="question-mark">
								<img src="http://paradisoprojects.com/dev/rawwar/site/images/question_clicked.png">					
							</div>
						</div>	
						<div id="theme-body-left-bottom">						
							<div id="desc-body" class="theme-description-new"> 
								<span class="theme-text">Communities of individuals who share and compare experiences, with the goal of creating a dialogue for collaborative empowerment.</span>
							</div>
						</div>
					</div>
					<div id="theme-body-right">						
						<div id="image-body1" class="theme-thumbnail">
							<img title="Carolee Schneemann, Interview (posted by the Brooklyn Museum), 2008" src="http://paradisoprojects.com/dev/rawwar/site/images/CaroleeSchneemann_CR_crop.png"><br>
							 <span class="theme-text"></span>
						</div>
						<div id="image-body2" class="theme-thumbnail">
							<img title="Martha Wilson at the Brooklyn Museum, uploaded to youtube 2007 and to rawwar.org 2010" src="http://paradisoprojects.com/dev/rawwar/site/images/MarthaWilson_CR_crop.png"><br>
							 <span class="theme-text"></span>
						</div>
						<div id="image-body3" class="theme-thumbnail">
							<img title="ARTINQUIRY | Rachel Rosenthal Interview by Molly Barnes, uploaded to youtube 2007 and to rawwar.org 2010" src="http://paradisoprojects.com/dev/rawwar/site/images/RosenthalInterview_CR_crop.png"><br>
							 <span class="theme-text"></span>	
						</div>				
					</div>
				</div>
				</div>
				<div class="theme-blob">				
				<input type="hidden" name="theme-id" value="0"></input>	
				<div id="body-test" class="hide-info">
					<div class="checkBox theme-check">&nbsp;													
					</div>
					<div class="theme-image">
						<img src="http://paradisoprojects.com/dev/rawwar/site/images/identity.png">				
					</div>		
					<div class="question-mark">
						<img src="http://paradisoprojects.com/dev/rawwar/site/images/question_unclicked.png">					
					</div>		
				</div>					
				<div id="theme-body" class="show-info">
					<div id="theme-body-left">
						<div id="theme-body-left-top">						
							<div id="body-cb" class="checkBox theme-check">&nbsp;					
							</div>
							<div id="body-image" class="theme-image">
								<img src="http://paradisoprojects.com/dev/rawwar/site/images/identity.png">				
							</div>		
							<div id="body-question" class="question-mark">
								<img src="http://paradisoprojects.com/dev/rawwar/site/images/question_clicked.png">					
							</div>
						</div>	
						<div id="theme-body-left-bottom">						
							<div id="desc-body" class="theme-description-new"> 
								<span class="theme-text">A method for defining the self in a context of history where experience had been either erased or unacknowledged. This often includes questioning notions of sexuality, race and gender.</span>
							</div>
						</div>
					</div>
					<div id="theme-body-right">						
						<div id="image-body1" class="theme-thumbnail">
							<img title="Martha Rosler, Semiotics of the Kitchen, 1975" src="http://paradisoprojects.com/dev/rawwar/site/images/SemioticsOfTheKitchen_Identity_crop.png"><br>
							 <span class="theme-text"></span>
						</div>
						<div id="image-body2" class="theme-thumbnail">
							<img title="Tracy + the Plastics, 2003" src="http://paradisoprojects.com/dev/rawwar/site/images/Tracy+Plastics_Identity_crop.png"><br>
							 <span class="theme-text"></span>
						</div>
						<div id="image-body3" class="theme-thumbnail">
							<img title="art:21 | Kiki Smith, uploaded to youtube 2007 and to rawwar.org 2010" src="http://paradisoprojects.com/dev/rawwar/site/images/KikiSmith_TheBodyPolitic_crop.png"><br>
							 <span class="theme-text"></span>	
						</div>				
					</div>
				</div>
				</div>
				<div class="theme-blob">				
				<input type="hidden" name="theme-media" value="0"></input>	
				<div id="body-test" class="hide-info">
					<div class="checkBox theme-check">&nbsp;													
					</div>
					<div class="theme-image">
						<img src="http://paradisoprojects.com/dev/rawwar/site/images/media.png">				
					</div>		
					<div class="question-mark">
						<img src="http://paradisoprojects.com/dev/rawwar/site/images/question_unclicked.png">					
					</div>		
				</div>					
				<div id="theme-body" class="show-info">
					<div id="theme-body-left">
						<div id="theme-body-left-top">						
							<div id="body-cb" class="checkBox theme-check">&nbsp;						
							</div>
							<div id="body-image" class="theme-image">
								<img src="http://paradisoprojects.com/dev/rawwar/site/images/media.png">				
							</div>		
							<div id="body-question" class="question-mark">
								<img src="http://paradisoprojects.com/dev/rawwar/site/images/question_clicked.png">					
							</div>
						</div>	
						<div id="theme-body-left-bottom">						
							<div id="desc-body" class="theme-description-new"> 
								<span class="theme-text">Media refers to both a vehicle for communication that reach a broad public and use forms of film, video the internet to broaden their audience.</span>
							</div>
						</div>
					</div>
					<div id="theme-body-right">						
						<div id="image-body1" class="theme-thumbnail">
							<img title="Blonde Redhead (Miranda July), Top Ranking, 2007" src="http://paradisoprojects.com/dev/rawwar/site/images/BlondeRedhead_Media_crop.png"><br>
							 <span class="theme-text"></span>
						</div>
						<div id="image-body2" class="theme-thumbnail">
							<img title="Theresa Andersson, Birds Fly Away, documented in 2008" src="http://paradisoprojects.com/dev/rawwar/site/images/Theresa_Media_crop.png"><br>
							 <span class="theme-text"></span>
						</div>
						<div id="image-body3" class="theme-thumbnail">
							<img title="Laurie Anderson, Zero and One, documented in 1984" src="http://paradisoprojects.com/dev/rawwar/site/images/ZeroOne_Media_crop.png"><br>
							 <span class="theme-text"></span>	
						</div>				
					</div>
				</div>
				</div>
				<div class="theme-blob">
				<input type="hidden" name="theme-act" value="0"></input>				
				<div id="body-test" class="hide-info">
					<div class="checkBox theme-check">&nbsp;														
					</div>
					<div class="theme-image">
						<img src="http://paradisoprojects.com/dev/rawwar/site/images/social.png">				
					</div>		
					<div class="question-mark">
						<img src="http://paradisoprojects.com/dev/rawwar/site/images/question_unclicked.png">					
					</div>		
				</div>					
				<div id="theme-body" class="show-info">
					<div id="theme-body-left">
						<div id="theme-body-left-top">						
							<div id="body-cb" class="checkBox theme-check">&nbsp;					
							</div>
							<div id="body-image" class="theme-image">
								<img src="http://paradisoprojects.com/dev/rawwar/site/images/social.png">				
							</div>		
							<div id="body-question" class="question-mark">
								<img src="http://paradisoprojects.com/dev/rawwar/site/images/question_clicked.png">					
							</div>
						</div>	
						<div id="theme-body-left-bottom">						
							<div id="desc-body" class="theme-description-new"> 
								<span class="theme-text">Art that calls attention to issues of social justice and civil rights in an attempt to point out or correct inequality.</span>
							</div>
						</div>
					</div>
					<div id="theme-body-right">						
						<div id="image-body1" class="theme-thumbnail">
							<img title="Laurie Anderson, National Anthem, 1990" src="http://paradisoprojects.com/dev/rawwar/site/images/LaurieAnderson_ActivismSocialProtest.png"><br>
							 <span class="theme-text"></span>
						</div>
						<div id="image-body2" class="theme-thumbnail">
							<img title="Miranda July, Assignment #33: Braid someone’s hair (Learning to Love You More), 2002-2007 " src="http://paradisoprojects.com/dev/rawwar/site/images/MirandaJuly_Braid_ActivismAndSocialProtest_crop.png"><br>
							 <span class="theme-text"></span>
						</div>
						<div id="image-body3" class="theme-thumbnail">
							<img title="Howardena Pindell, Atomizing Art (contains excerpt from Free, White and 21, 1980)" src="http://paradisoprojects.com/dev/rawwar/site/images/Pindell_ActivismSocialProtest_crop.png"><br>
							 <span class="theme-text"></span>	
						</div>				
					</div>
				</div>
				</div>
				
				<div id="submit">
					<img id="submit-button" class="submit" alt="Submit Artwork" src="http://www.paradisoprojects.com/dev/rawwar/site/images/submit_artwork.png">
				</div>			
		</div>
		<div id="trace"></div>
		<div id="overlay" class="simple-overlay">
				<div id="overlay-content">
					<div id="welcome-text" class="overlay-text">					
					<p>RAW/WAR is an interactive, community-curated media archive that provides a forum in which users can come together, share their art and share their stories to collaboratively contribute to the history of women's art. The site is a democratic community space where users can post links to images and video that highlight the achievements and practices of women artists. You can include the work (or documentation of the work) of visual artists, performers, dancers, musicians, activists etc. You don't have to be a woman to add the work of female or self-identified female artists and the art doesn't have to be feminist or identify as feminist to be included.		
					</p>
                    <p>Work that you submit will remain permanently archived on this website (artists retain full rights to their work) and it will also be physically revealed using beams of light during an interactive installation that is debuting at New Frontier during this year's Sundance Film Festival.</p>		
					<p>
					A few notes to get you started: <br> 			
					</p>
                   <strong>(1) UPLOADING MEDIA:</strong>
					<ol class="overlay-ol">
 
 					<li>The archive only supports web links to Youtube videos and images at this time. You must copy and paste the URL of your video or image file into the entry 		field.
					<li>You can upload your files to Youtube to generate a link <a href="http://upload.youtube.com/my_videos_upload">here.</a></li>
					<li>Image links must end in .jpg, .jpeg, .png or .gif.</li>	</ol>
                    
                    <strong>(2) 	ADDING KEY WORDS:</strong><br>
                    Assigning tags and categories to your artwork increases the chance that visitors will see it.
                    
                    <ol class="overlay-ol"> 
                    <li>Choose words (preferably single terms) that best represent the content of your submission.  You may add multiple tags by separating them with a comma as you type.</li>
					<li>Essential tags to consider: artist name, name of piece, description of media type (i.e. documentary, interview, music video, painting, performance, sculpture, video documentation of an exhibit),  subject matter (i.e. kitchen, solo, music, bird)
</li>				
					</ol>
					</div>	
				</div>
		</div>
		<div id="tag-overlay" class="simple-overlay">
				<div id="overlay-content">
					<div id="welcome-text" class="overlay-text">					
				 <ol class="overlay-ol"><p><strong>Tips for adding key words:</strong></p>
  
                    <li>Choose words (preferably single terms) that best represent the content of your submission.  You may add multiple tags by separating them with a comma as you type.</li>
					<li>Essential tags to consider: artist name, name of piece, description of media type (i.e. documentary, interview, music video, painting, performance, sculpture, video documentation of an exhibit),  subject matter (i.e. kitchen, solo, music, bird)
</li>				
					</ol>
					</div>	
				</div>
		</div>
		<div id="error-overlay" class="simple-overlay">
				<div id="overlay-content">
                        <p><h2>Please correct the errors in this form.</h2></p>				
				</div>
		</div>
	</div>
	</body>
</html>
<?php
}

function render_thanks(){
?>
<html>	
	<head>
		<title>Women Art Revolution | Add an Artwork</title>
		<link type="text/css" href="http://184.106.93.224/css/war.css" rel="Stylesheet" />
		<link type="text/css" href="http://184.106.93.224/css/smoothness/jquery-ui-1.8.6.custom.css" rel="Stylesheet" />
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.8.6.custom.min.js"></script>
		<script type="text/javascript" src="js/jquery.autoclear.min.js"></script>
		<script type="text/javascript" src="http://184.106.93.224/js/submit.js"></script>
	</head>
	<body>
	<div class="response">
	<div class="thanks">
		<span class="">
			<h2>			
			<p>Thank you for contributing to the RAWWAR archive.  Please encourage others to add their pieces and participate in history.</p>
			<p>Continue exploring the archive <a href="" >>></a>			
			</p></h2>		
		</span>
	</div>
	</div>
	</body>
</html>
<?php	
}

function render_already_have(){
?>
<html>	
	<head>
		<title>Women Art Revolution | Add an Artwork</title>
		<link type="text/css" href="http://184.106.93.224/css/war.css" rel="Stylesheet" />
		<link type="text/css" href="http://184.106.93.224/css/smoothness/jquery-ui-1.8.6.custom.css" rel="Stylesheet" />
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.8.6.custom.min.js"></script>
		<script type="text/javascript" src="js/jquery.autoclear.min.js"></script>
		<script type="text/javascript" src="http://184.106.93.224/js/submit.js"></script>
	</head>
	<body>
	<div class="response">	
	<div class="thanks">	
	<h2>Thanks!<h2><br>
	But we already have this work in our archive.<br>
	<a href="" >Would you to submit another one?</a>
	</h2><br>
	</a>
	</div>
	</div>
	</body>
</html>
<?php
}
?>