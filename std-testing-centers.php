<?php

require_once './wp-load.php';
//require_once ABSPATH . 'includes/classes/Debug.class.php';

// init db connection
global $wpdb;

// somethign like the base url
$urlForLinks = $_SERVER['SVR_NAME'].'/std-testing-centers/';

//Remove request parameters:
list($path) = explode('?', $_SERVER['REQUEST_URI']);

//Explode path to directories and remove empty items:
$pathInfo = array();
foreach (explode('/', $path) as $dir) {
  if (!empty($dir)) {
    $pathInfo[] = urldecode($dir);
  }
}

if (count($pathInfo) > 0) {
  //Remove file extension from the last element:
  $last = $pathInfo[count($pathInfo)-1];
  list($last) = explode('.', $last);
  $pathInfo[count($pathInfo)-1] = $last;
}

$state = $pathInfo[1];
$city = $pathInfo[2];
$address = $pathInfo[3];
if(is_numeric($city)) {
	$zip = $city;
}

// init search type  
$searchType = 'none';
global $wp_head_tags_stc;

if($address) {
	// prep the string
	$tempPos = strrpos($address, '-');
	$address = substr($address, 0, $tempPos);
	$tempURL = $address;  //Use this later on.
	$address = str_replace('-', ' ', $address);
	$searchType = 'address';
	$sql = $wpdb->prepare("SELECT id, id as address1, city, state, zip, name, stateFull, lat, lng FROM nationallocations WHERE id = %d AND type = 'normal'", $address );
	$locationsData = $wpdb->get_results($sql, ARRAY_A);
	$tempZip = $locationsData[0]['zip'];
	$name = $locationsData[0]['name'];
	$_SESSION['addressInput'] = $tempZip;
	$tempCity = $locationsData[0]['city'];
	$sql = $wpdb->prepare("SELECT id, id as address1, city, state, zip, name, stateFull, lat, lng from nationallocations where (zip = %d OR city = %s) AND id != %s AND type = 'normal'", $tempZip, $tempCity, $address);
	$locationsData2 = $wpdb->get_results($sql, ARRAY_A);
	$centersURL = "loadCenters.php?id=$address&zip=$tempZip&city=$tempCity";
	$wp_head_tags_stc[] = '<title>' . $name . ' | Get STD Testing in ' . $city . '</title>' . "\n";
	$wp_head_tags_stc[] = '<meta name="description" content="Easy, Accurate, and 100% Confidential STD Testing in '. $city . ' at this clinic: ' . $name . '! We have this location and more in or near your zip code ' . $zip . ' . Order a customized, comprehensive STD Testing package on this site, and you\'ll get instant access to testing. Walk in today, and your results will be available in our online system in 1-5 days." />';
	$wp_head_tags_stc[] = '<meta name="keywords" content="STD Testing, HIV testing, STD Clinic, STD Lab, STD Lab at ' . $address . ', STD Testing in ' . $zip . ',' . $state . ' STD Testing Center , ' . $city . ' STD Clinic, STD Testing ' . $city . ', ' . $address . ',STD Clinic", ' . $city . ' , ' . $address . ', ' . $state. ',' . $zip . '" />';
} elseif($zip) {
	$searchType = 'zip';
	$_SESSION['addressInput'] = $zip;
	$sql = $wpdb->prepare("SELECT id, id as address1, city, state, zip, name, stateFull, lat, lng from nationallocations where zip = %d AND type = 'normal'", $zip);
	$locationsData = $wpdb->get_results( $sql, ARRAY_A);
	$locationsData2 = $locationsData;
	$wp_head_tags_stc[] = '<title> STD Clinics in '.$zip.' | '.$zip.' STD Testing Centers & Labs | Getting an STD Test in '.$zip.'</title>';
	$wp_head_tags_stc[] = '<meta name="description" content="All residents in '.$zip.' are eligible to be tested for Sexually Transmitted Diseases at one of the following facilities. Simply choose your location online, order your customized STD Testing package for HIV, Chlamydia, Gonorrhea, Herpes, Syphilis, and more. If you test today, you will be able to view your results in our secure online system in 1-5 business days." />';
	$wp_head_tags_stc[] = '<meta name="keywords" content="HIV Testing, testing for STDs, STD Lab near me, STD lab nearby, STD testing in '.$zip.',  '.$zip.' STD Testing,  Get Tested for STD\'s in '.$zip.', HIV Testing in '.$zip.', AIDS testing in '.$zip.', Chlamydia Testing in '.$zip.', Herpes Testing in '.$zip.' , Syphilis Testing in '.$zip.',  Gonorrhea Testing in '.$zip.', Hepatitis Testing in '.$zip.'" />';
} elseif($city) {
	$searchType = 'city';
	$cityNoDashes = str_replace('-', ' ', $city);
	if($state) {
		$statePieces = explode('-', $state);
		$statePieces = array_reverse($statePieces);
		$stateAbbrev = stateToAbb($state);
		$sql = $wpdb->prepare("SELECT id, id as address1, city, state, zip, name, stateFull, lat, lng from nationallocations where city = %s AND state = %s AND type = 'normal'", $cityNoDashes, $stateAbbrev);
	} else {
		$sql = $wpdb->prepare("SELECT id, id as address1, city, state, zip, name, stateFull, lat, lng from nationallocations where city = %s AND type = 'normal'", $cityNoDashes);
	}
	$locationsData = $wpdb->get_results( $sql, ARRAY_A );
	$locationsData2 = $locationsData;
	$wp_head_tags_stc[] = '<title> STD Labs in '.$city.' | STD Clinics & Labs in '.$city.' | Getting an STD Test in '.$city.'</title>';
	$wp_head_tags_stc[] = '<meta name="description" content="All residents of '.$city.' have access to full STD Testing at these labs. Simply choose your preferred STD lab on this site, order your customized STD Test selection and you can walk in to the clinic at any time during normal business hours." />';
	$wp_head_tags_stc[] = '<meta name="keywords" content="STD Test, STD Testing, HIV Test, HIV Testing, STD testing in '.$city.',  '.$city.' STD Testing,  Getting Tested '.$city.', '.$city.' HIV Test, AIDS test '.$city.', Chlamydia Test '.$city.', Herpes Testing in '.$city.' , Syphilis Test in '.$city.',  Gonorrhea Test in '.$city.', Hepatitis Test in '.$city.', STD Testing '.$city.' '.$state.'" />';
} elseif($state) {
	$searchType = 'state';
	$statePieces = explode('-', $state);
	$statePieces = array_reverse($statePieces);
	$stateAbbrev = stateToAbb($state);
	$sql = $wpdb->prepare( "SELECT distinct(city) as city, state, stateFull, zip from nationallocations where state=%s AND type = 'normal' order by city", $stateAbbrev);
	$locationsData = $wpdb->get_results($sql, ARRAY_A);
	$wp_head_tags_stc[] = '<title>'.$state.' STD Testing Centers | '.$state.' STD Clinics | Get Tested for STD\'s in '.$state.'</title>';
	$wp_head_tags_stc[] = '<meta name="description" content="Get tested for STD\'s in '.$state.' : Simply choose your location online, order your customized STD Testing package, and you\'ll get instant access to testing. Test today, get results online in 2-3 business days." />';
	$wp_head_tags_stc[] = '<meta name="keywords" content="STD testing in '.$state.', STD Testing in '.$state.', '.$state.' STD Testing, '.$state.' STD Testing, Get Tested for STD\'s in '.$state.','.$state.' HIV Testing ,'.$state.' AIDS testing,'.$state.' Chlamydia Testing,'.$state.' Herpes Testing, '.$state.' Syphilis Tests, '.$state.' Gonorrhea Tests, '.$state.' Hepatitis Tests" />';
} else {
	$searchType = 'default';
	$sql = "SELECT distinct(state) as state, stateFull from nationallocations WHERE type = 'normal' order by state";
	$locationsData = $wpdb->get_results($wpdb->prepare($sql), ARRAY_A );
	$wp_head_tags_stc[] = '<title>National STD Testing Clinic Database | US STD Labs</title>';
	$wp_head_tags_stc[] = '<meta name="description" content="Get tested for STD\'s anywhere in the U.S. . Use this incredibly easy online ordering tool to choose your STD tests, then you will have the ability to walk in to a lab of your choice. Once your STD testing is complete, your test results will be available in our secure online ordering system." />';
    $wp_head_tags_stc[] = '<meta name="keywords" content=" STDs, Testing, STD Labs in the US,  , National STD Labs, STD Testing in US, STD Clinic Database, STD Clinic , STD Tests, STD Testing , STD Clinics" />';
}

$centersURL = "/centers-map.php?address=$address&zip=$zip&city=$city&state=$state";
$tempArray = explode("?",$_SERVER["REQUEST_URI"]);
$tempURL = str_replace(".php","",$tempArray[0]);
$wp_head_tags_stc[] = '
<link rel="canonical" href=" '.$_SERVER['SVR_NAME'].$tempURL.' " />

<style media="screen" type="text/css">
  #center-map { width: 315px; height: 200px; border:solid thin #000; }
  #map-sideinfo { float:right; }
  p { text-align:left; }
  #maincontent h2 { font-size:17px; font-weight:normal; border:0px; line-height:19px; }
  h2 { font-size:17px; font-weight:normal; line-height:18px; }
  h3 { font-size:18px; font-weight:normal; }
  h4 { font-size:16px; font-weight:normal; color:#056360; }
  h5 { font-size:14px; font-weight:normal; color:#056360; }
</style>';


$wp_head_tags_stc[] = '<style media="screen" type="text/css">
#orderButton a {background: url(http://c0001470.cdn1.cloudfiles.rackspacecloud.com/get-your-test-today.png) no-repeat top left; display: block; width: 234px; height: 46px; text-indent: -9999px; font-size: 0; text-align:center}
#orderButton a:hover {background-position: bottom left;}

#findZip a {background: url(http://c189814.r14.cf1.rackcdn.com/btn-find-std-center-zip-code.png) no-repeat top left; display: block; width: 71px; height: 23px; text-indent: -9999px; font-size: 0; text-align:center}
#findZip a:hover {background-position: bottom left;}

#btnSeeTestsAndPrices a {font-size: 0em; line-height: 0em; background: url(http://c189814.r14.cf1.rackcdn.com/btn-see-std-tests-prices.png) no-repeat top left; display: block; width: 225px; height: 37px; text-indent: -9999px;}
#btnSeeTestsAndPrices a:hover {background-position: bottom left;}

#btnFindTestCenter a {font-size: 0em; line-height: 0em; background: url(http://c189814.r14.cf1.rackcdn.com/btn-find-std-testing-center.png) no-repeat top left; display: block; width: 225px; height: 37px; text-indent: -9999px;}
#btnFindTestCenter a:hover {background-position: bottom left;}

</style>';

/**
 * header_callback()
 * wp_head callback function
 * @global $wp_head_tags_stc
 */
 
function header_callback()
{
	global $wp_head_tags_stc;
	echo '<!-- Header Callback -->' . "\n";
	echo implode("\n", $wp_head_tags_stc) . "\n";
	echo '<!-- Header Callback End -->' . "\n";
}

function stateToAbb($input){
    $input = str_replace("-", " ", $input);
    
    //reset found
    $found = 0;
    //list states
    $states = "Alaska,Alabama,Arkansas,Arizona,California,Colorado,Connecticut,Delaware,Florida,Georgia,Hawaii,Iowa,Idaho,Illinois,Indiana,Kansas,Kentucky,Louisiana,Massachusetts,Maryland,Maine,Michigan,Minnesota,Missouri,Mississippi,Montana,North Carolina,North Dakota,Nebraska,New Hampshire,New Jersey,New Mexico,Nevada,New York,Ohio,Oklahoma,Oregon,Pennsylvania,Rhode Island,South Carolina,South Dakota,Tennessee,Texas,Utah,Virginia,Vermont,Washington,Wisconsin,West Virginia,Wyoming";
    //list abbreviations
    $abb = "AK,AL,AR,AZ,CA,CO,CT,DE,FL,GA,HI,IA,ID,IL,IN,KS,KY,LA,MA,MD,ME,MI,MN,MO,MS,MT,NC,ND,NE,NH,NJ,NM,NV,NY,OH,OK,OR,PA,RI,SC,SD,TN,TX,UT,VA,VT,WA,WI,WV,WY";
    //create arrays
    $states_array = explode(",", $states);
    $abb_array = explode(",", $abb);
    
    //run test
    for ($i = 0; $i < count($states_array); $i++){
        if (strtolower($input) == strtolower($states_array[$i])){
            $found = 1;
            $output = $abb_array[$i];
            return $output;
        }
    }
    if ($found != 1){
        $output = $input;
        return $output;
    }
    return $output;
} 

add_action('wp_head', 'header_callback', 12);
$_SESSION['cssIncText'] = 'no-title';
ob_start('ppmdTitleCallback');
get_header();

$docRoot = getenv("DOCUMENT_ROOT");
?>



<div id="page"><!-- START DIV PAGE --> 


<!-- *********************************** START MAIN CONTENT *********************************** -->                
 

	<div class="column span-10 first" id="maincontent"><!-- START DIV CONTENT2 -->  
    
		<div class="content2"><!-- START DIV CONTENT2 -->  
                                  
			<div>     
				<?if($searchType=='address'){ ?>
					<div class="clinic-info" id="<?=$tempURL?>">
					<?
                        $stateDashes = str_replace(' ', '-', $locationsData[0]['stateFull']);
                        $linkState = $stateDashes."-".$locationsData[0]['state'];
                        echo "<a href=\"/std-testing-centers\">All STD Testing Centers</a> >> <a href=\"".$urlForLinks.$stateDashes."\" title=\"".$locationsData[0]['stateFull']." STD Testing\">".$locationsData[0]['stateFull']." STD Clinics</a> >> <a href=\"".$urlForLinks.$stateDashes."/".$locationsData[0]['city']." \" title=\"".$locationsData[0]['city']." STD Testing\" title=\"".$locationsData[0]['city']." STD Testing\">". $locationsData[0]['city'] ." STD Clinics</a> >>";
						?>
						<h1><?=$locationsData[0][name]?></h1>
						<div style="height:445px; margin-bottom:20px;">
						<div style="float:right; width:345px; height:445px; background-color:#FFDFD4; border:solid 1px #E67240; margin-left:20px; margin-bottom:15px;">
							<div style="padding:15px; text-align:center;">
								<h2>Locate a STD Testing Center in <? echo $locationsData[0]['city']; ?> and get tested today.</h2>
                                <div style="text-align:left;">
									<? 	echo "<a href=\"".$urlForLinks.$linkState."/".$locationsData[0]['city']." \" title=\"STD Testing Centers in ".$locationsData[0]['city']." \" title=\"STD Testing Centers in ".$locationsData[0]['city']." \">See All ". $locationsData[0]['city'] ." STD Centers</a>";
										if($searchType=='city' || $searchType=='zip' || $searchType=='address'){?>    
											<div id="center-map"></div>
										<? } ?>
                                	<div class="address">
										Location Number <?=$locationsData[0][address1]?>
										<br /><?=$locationsData[0]['city']?>, <?=$locationsData[0]['state']?> <?=$locationsData[0][zip]?>
									</div>
									<div class="extra-links">
										<b><?$_SESSION['affPhone'];?></b>
										<!-- <a href="/std-testing-options" title="Order STD Tests Online"><font color="red">Order Online</font></a> -->
										<!-- <a href="#" class="show_on_map">Map It</a> -->
									</div>
									<div style="text-align:center;">
                                		<div id="btnSeeTestsAndPrices" style="margin-top:10px; margin-left:45px; margin-bottom:10px;"><a href="/order">Find A STD Testing Center</a></div>
										Have questions? We're here to talk:<br /><span style="font-size:22px;">866-749-6269</span>
                                	</div>
                                </div>
							</div>
						</div>
					<? echo
					"<div class=\"address\" style=\"font-size:16px; margin-bottom:20px;\">
						Location Number ".$locationsData[0][address1]."
						<br />".$locationsData[0]['city'].", ".$locationsData[0]['state']." ".$locationsData[0][zip]."
					</div>
					<p>The ".$locationsData[0]['name']." in <b>".$locationsData[0]['city'].", ".$locationsData[0]['state']."</b> tests for a variety of Sexually Transmitted Diseases. This is the same lab that most Doctors in ".$locationsData[0]['city']." use when they need to get their patients tested, so you can expect to get the exact same accuracy of testing that you would from a Doctor, at a fraction of the actual cost.</p> 
					<p>Feel comfortable and safe during your visit with informative, expertly trained, and friendly staff. After you order your STD test or create your custom testing package on this website, you will be able to walk in to ".$locationsData[0]['name']." facility and get tested today at any time during normal business hours.</p> 
					<p>The results of your STD Test will then be available in your secure online account on our website. It&rsquo;s very fast, painless, and 100% confidential.</p>";
					echo "<br /></div>";
							$sticky = get_option('sticky_posts');
							rsort( $sticky );
							$sticky = array_slice( $sticky, 1, 2);
							query_posts( array( 'post__in' => $sticky, 'caller_get_posts' => 1 ) );
						
							while (have_posts()) : the_post();
								$img = get_post_meta($post->ID, 'Featured Thumbnail', true);
						?>
						<div style="width:237px; float:left; margin-right:20px;" <?php post_class(); ?> id="post-<?php the_ID(); ?>">
							<div class="entry">
								<img style="margin-bottom:10px;" src="<?php echo $img; ?>"/><br />
								<div style="margin-bottom:5px;"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title('<h5>', '</h5>'); ?></a></div>
								<?php
								//the_excerpt();
									$lwexcerpt = limit_words(get_the_excerpt(), '40');
                                    echo $lwexcerpt;
									echo '...<br /><strong><a href="'. get_permalink($post->ID) . '">LEARN MORE</a></strong>';
								?>
							</div>
						</div>
						<?php
							endwhile;
						?>
						<?
						echo "
						<div style=\"background-color:#EAFFFE; height:300px; width:164px; float:left; border:solid 1px #A8F6F3;\">
							<div style=\"padding:15px; text-align:center;\">";
						?>
							<h3>Trusted &amp; Accredited</h3>
                            <br />
                            <center>
							<a target="_blank" id="bbblink" class="sevtbum" href="http://www.bbb.org/chicago/business-reviews/medical-testing-companies/healthplace-in-schaumburg-il-88387750#bbblogo" title="Healthplace, LLC is a BBB Accredited Medical Testing Company in Schaumburg, IL" style="display: block;position: relative;overflow: hidden; width: 60px; height: 98px; margin: 0px; padding: 0px;"><img style="padding: 0px; border: none;" id="bbblinkimg" src="http://seal-chicago.bbb.org/logo/sevtbum/healthplace-88387750.png" width="120" height="98" alt="Healthplace, LLC is a BBB Accredited Medical Testing Company in Schaumburg, IL" /></a><script type="text/javascript">var bbbprotocol = ( ("https:" == document.location.protocol) ? "https://" : "http://" ); document.write(unescape("%3Cscript src='" + bbbprotocol + 'seal-chicago.bbb.org' + unescape('%2Flogo%2Fhealthplace-88387750.js') + "' type='text/javascript'%3E%3C/script%3E"));</script>
                            <br />
							<img src="http://c189814.r14.cf1.rackcdn.com/asha-logo.jpg" width="60" height="58">
        					</center>
        			<?
					echo "
					</div>
					</div>
					</div>";
					} 
				
				
				
				elseif($searchType=='zip'){
					echo "$zip STD Testing".
					"<p>Our testing centers in <b>$zip</b> provides convenient, affordable and confidential HIV and Sexually Transmitted Disease testing. We have worked hard to open as many testing centers near <b>$zip</b> as possible. Use our map to find the closest location near you. We keep the testing process accurate with a staff trained and informed, to help you before and after your test. Call us to understand whether you should be tested and to understand your results. Some STDs don't have visible symptoms. Never assume that you or your partner are STD free. Stop Worrying and Test in <b>$zip</b> Today!</p>";
					$stateDashes = str_replace(' ', '-', $locationsData[0]['stateFull']);
					$linkState = $stateDashes."-".$locationsData[0]['state'];
					echo "<div><a href=\"".$urlForLinks.$stateDashes."\" title=\"".$locationsData[0]['stateFull']." STD Testing\">See All ".$locationsData[0]['stateFull']." STD Centers</a></div>";
					?>
					<? }
					
					
					
				elseif($searchType=='city'){
					$stateDashes = str_replace(' ', '-', $locationsData[0]['stateFull']);
					$linkState = $stateDashes."-".$locationsData[0]['state'];
					$_SESSION['addressInput'] = $locationsData[0]['zip'];
					echo "<a href=\"/std-testing-centers\" title=\"All STD Testing Centers\">All STD Testing Centers</a> >> <a href=\"".$urlForLinks.$stateDashes."\" title=\"".$locationsData[0]['stateFull']." STD Testing\">".$locationsData[0]['stateFull']." STD Testing</a> >> ".$locationsData[0]['city']." STD Testing
					<h1>STD Testing in ".$locationsData[0]['city']."</h1>";?>
					
					<div style="height:590px; margin-bottom:20px;">
						<div style="float:right; width:345px; height:410px; background-color:#FFDFD4; border:solid 1px #E67240; margin-left:20px; margin-bottom:15px;">
							<div style="padding:15px; text-align:center;">
								<h2>Locate a STD Testing Center in <? echo $locationsData[0]['city']; ?> and get tested today.</h2>
                                <div style="text-align:left;">
									<? 	echo "<a href=\"".$urlForLinks.$stateDashes."\" title=\"".$locationsData[0]['stateFull']." STD Testing\">See All ".$locationsData[0]['stateFull']." STD Centers</a>";
										if($searchType=='city' || $searchType=='zip' || $searchType=='address'){?>    
                                        <div id="center-map"></div>
                                    <? } ?>
									<div style="text-align:center;">
                                		<div id="btnSeeTestsAndPrices" style="margin-top:10px; margin-left:45px; margin-bottom:10px;"><a href="/order">Find A STD Testing Center</a></div>
										Have questions? We're here to talk:<br /><span style="font-size:22px;">866-749-6269</span>
                                	</div>
                                </div>
							</div>
						</div>

					
					
					<? echo
					"<p>We have a number of labs available in ".$locationsData[0]['city']." that provide STD Testing. The labs listed here provide testing for a number of conditions including Chlamydia, gonorrhea, hepatitis b, hepatitis c , oral herpe, genital herpes, syphilis, and HIV. You won't find a more comprehensive package anywhere else.</p> 

					<p>The facilities in ".$locationsData[0]['city'].", ".$locationsData[0]['state']." are highly specialized labs with expertly trained, friendly technicians.</p>

  					<p><b>Here&rsquo;s how the process works:</b></p>
					
					<p><b>1. Choose Your STD Test Package (simply click on <a href=\"/order\">&quot;SEE TESTS &amp; PRICES&quot;</a>).</b>  If you are more comfortable talking to a real person or need some additional help determining which test you need, you call us 866-749-6269 during normal business hours. Expert STD counselors are standing by to answer questions.</p>

					<p><b>2. Choose Your STD Lab in ".$locationsData[0]['city'].", ".$locationsData[0]['state']."</b> closest to your home or office. With over 4,000 labs in our network, there's likely a lab around the corner.</p>

					<p><b>3. Visit the lab of your choice</b> with the form we email to you. You can visit the lab at any time during business hours.  You can simply walk-in, and show the form and the technician will walk you through the process. These clinics see patients for a variety reasons, so no one will know why you&rsquo;re there or what you are getting tested for.</p>

					<p><b>4. Get STD Test Results Online</b> via our private, web-based patient portal within 1 to 5 business days.</p>

					<p><b>5. Get Answers & Treatement</b> for the conditions you test for. If you need help, we are here. For no extra charge, you can talk with our in-network physicians to get a prescription for a curable STD or help interpreting your STD test results.</p>";
					echo "<br /></div>";
							$sticky = get_option('sticky_posts');
							rsort( $sticky );
							$sticky = array_slice( $sticky, 1, 2);
							query_posts( array( 'post__in' => $sticky, 'caller_get_posts' => 1 ) );
						
							while (have_posts()) : the_post();
								$img = get_post_meta($post->ID, 'Featured Thumbnail', true);
						?>
						<div style="width:237px; float:left; margin-right:20px;" <?php post_class(); ?> id="post-<?php the_ID(); ?>">
							<div class="entry">
								<img style="margin-bottom:10px;" src="<?php echo $img; ?>"/><br />
								<div style="margin-bottom:5px;"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title('<h5>', '</h5>'); ?></a></div>
								<?php
								//the_excerpt();
									$lwexcerpt = limit_words(get_the_excerpt(), '40');
                                    echo $lwexcerpt;
									echo '...<br /><strong><a href="'. get_permalink($post->ID) . '">LEARN MORE</a></strong>';
								?>
							</div>
						</div>
						<?php
							endwhile;
						?>
						<?
						echo "
						<div style=\"background-color:#EAFFFE; height:300px; width:164px; float:left; border:solid 1px #A8F6F3;\">
							<div style=\"padding:15px; text-align:center;\">";
						?>
							<h3>Trusted &amp; Accredited</h3>
                            <br />
                            <center>
							<a target="_blank" id="bbblink" class="sevtbum" href="http://www.bbb.org/chicago/business-reviews/medical-testing-companies/healthplace-in-schaumburg-il-88387750#bbblogo" title="Healthplace, LLC is a BBB Accredited Medical Testing Company in Schaumburg, IL" style="display: block;position: relative;overflow: hidden; width: 60px; height: 98px; margin: 0px; padding: 0px;"><img style="padding: 0px; border: none;" id="bbblinkimg" src="http://seal-chicago.bbb.org/logo/sevtbum/healthplace-88387750.png" width="120" height="98" alt="Healthplace, LLC is a BBB Accredited Medical Testing Company in Schaumburg, IL" /></a><script type="text/javascript">var bbbprotocol = ( ("https:" == document.location.protocol) ? "https://" : "http://" ); document.write(unescape("%3Cscript src='" + bbbprotocol + 'seal-chicago.bbb.org' + unescape('%2Flogo%2Fhealthplace-88387750.js') + "' type='text/javascript'%3E%3C/script%3E"));</script>
                            <br />
							<img src="http://c189814.r14.cf1.rackcdn.com/asha-logo.jpg" width="60" height="58">
        					</center>
        			<?
					echo "
					</div>
					</div>";
					} 



				elseif($searchType=='state'){
				
		       ?>
						<a href="/std-testing-centers">All STD Testing Centers</a> >> <?=$locationsData[0]['stateFull']?> STD Testing
						<h1><?=$locationsData[0]['stateFull']?> STD Testing Locations</h1>
						<div style="float:right; width:345px; height:410px; background-color:#FFDFD4; border:solid 1px #E67240; margin-left:20px; margin-bottom:15px;">
							<div style="padding:15px; text-align:center;">
								<h2>Locate a STD Testing Center in <?=$locationsData[0]['stateFull']?> and get tested today.</h2>
								<form style="text-align:right; margin-right:70px;" id="zipcodeFormState" name="zipcodeFormState" method="post" action="/find-a-test-center">
									<label for="city-locations-picker">City</label>
									<select style="width:150px;" name="zipcode" id="zipcode">
									<?	
									foreach($locationsData as $key=>$value)
                  {
      							$cityTmp = str_replace(' ', '-', $value['city']);
      							$zipTmp = $value['zip'];
      							echo "<option value='$zipTmp'>$cityTmp";
      						}
									?>	
									</select>
								</form>
								<div id="btnFindTestCenter" style="margin-top:10px; margin-left:45px; margin-bottom:10px;"><a href="javascript:void(0);" onclick="document.zipcodeFormState.submit();">Find A STD Testing Center</a></div>
								Have questions? We're here to talk:<br /><span style="font-size:22px;">866-749-6269</span><br /><br />
								<p>STDTesting.com offers a variety of STD tests to choose from. Get tested for any one of the following or a combination of tests including:</p>
								<div style="text-align:left; width:130px; float:left; margin-left:15px;">
									<ul>
										<li><h5>&bull; Chlamydia</h5></li>
										<li><h5>&bull; Hepatitis B</h5></li>
										<li><h5>&bull; Syphilis</h5></li>
										<li><h5>&bull; Oral Herpes</h5></li>
									</ul>
								</div>
								<div style="text-align:left; width:130px; float:left;">
									<ul>
										<li><h5>&bull; Gonorrhea</h5></li>
										<li><h5>&bull; Hepatitis C</h5></li>
										<li><h5>&bull; HIV</h5></li>
										<li><h5>&bull; Genital Herpes</h5></li>
									</ul>
								</div>
							</div>
						</div>
						<p>STDtesting.com - a division of the Institute of Sexual Health - offers convenient and completely anonymous <?=$locationsData[0]['stateFull']?> STD testing: Chlamydia testing, gonorrhea testing, hepatitis b testing, hepatitis c testing, oral herpes testing, genital herpes testing, syphilis testing, and HIV testing.</p>
						<p>1. Select the STDs you&rsquo;d like to be tested for online by clicking <a href="/order">&quot;SEE TESTS &amp; PRICES&quot;</a>.  Alternately, order via phone by calling 866-749-6269. Trained counselors are standing by to answer questions.</p>
						<p>2. Choose a private testing lab in ".$locationsData[0]['stateFull']." closest to your home or office. With over 4,000 labs in our network, there's likely a lab around the corner.</p>
						<p>3. Get tested for STDs today or at your earliest convenience at the local, private lab of your choice. All labs operate on a walk-in basis and see patients for a variety reasons. No one will know why you&rsquo;re there, what you&rsquo;re getting tested for,  and no appointment is necessary. The STD testing process is simple and requires less than 20 minutes of your time.</p>
						<p>4. Get STD results via private, online patient portal within 1 to 5 business days.</p>
						<p>5. Get answers.  Speak with our in-network physicians to get a prescription for a curable STD or help interpreting your STD test results.</p>
				
				<?
							$sticky = get_option('sticky_posts');
							rsort( $sticky );
							$sticky = array_slice( $sticky, 1, 2);
							query_posts( array( 'post__in' => $sticky, 'caller_get_posts' => 1 ) );
						
							while (have_posts()) : the_post();
								$img = get_post_meta($post->ID, 'Featured Thumbnail', true);
					?>
                        
						
						<div style="width:237px; float:left; margin-right:20px;" <?php post_class(); ?> id="post-<?php the_ID(); ?>">
							<div class="entry">
								<img style="margin-bottom:10px;" src="<?php echo $img; ?>"/><br />
								<div style="margin-bottom:5px;"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title('<h5>', '</h5>'); ?></a></div>
								<?php
								//the_excerpt();
									$lwexcerpt = limit_words(get_the_excerpt(), '40');
                                    echo $lwexcerpt;
									echo '...<br /><strong><a href="'. get_permalink($post->ID) . '">LEARN MORE</a></strong>';
								?>
							</div>
						</div>
						
						<?php
							endwhile;
						?>
						
						
						<?
						echo "
						<div style=\"background-color:#EAFFFE; height:300px; width:164px; float:left; border:solid 1px #A8F6F3;\">
							<div style=\"padding:15px; text-align:center;\">";
						?>
						
						
							<h3>Trusted &amp; Accredited</h3>
                            <br />
                            <center>
							<a target="_blank" id="bbblink" class="sevtbum" href="http://www.bbb.org/chicago/business-reviews/medical-testing-companies/healthplace-in-schaumburg-il-88387750#bbblogo" title="Healthplace, LLC is a BBB Accredited Medical Testing Company in Schaumburg, IL" style="display: block;position: relative;overflow: hidden; width: 60px; height: 98px; margin: 0px; padding: 0px;"><img style="padding: 0px; border: none;" id="bbblinkimg" src="http://seal-chicago.bbb.org/logo/sevtbum/healthplace-88387750.png" width="120" height="98" alt="Healthplace, LLC is a BBB Accredited Medical Testing Company in Schaumburg, IL" /></a><script type="text/javascript">var bbbprotocol = ( ("https:" == document.location.protocol) ? "https://" : "http://" ); document.write(unescape("%3Cscript src='" + bbbprotocol + 'seal-chicago.bbb.org' + unescape('%2Flogo%2Fhealthplace-88387750.js') + "' type='text/javascript'%3E%3C/script%3E"));</script>
                            <br />
							<img src="http://c189814.r14.cf1.rackcdn.com/asha-logo.jpg" width="60" height="58">
        					</center>
        
        			<?
					echo "
					</div>
					</div>".
					"<p><h3>STD Testing Centers in ".$locationsData[0]['stateFull']."</h3></p><br />";
					$lengthOfCol = sizeof($locationsData)/3;
					echo "<div style=\"float:left;\">";
						foreach($locationsData as $key=>$value){
							$cityDashes = str_replace(' ', '-', $value['city']);
							echo "<p style=\"width:205px; margin-right:20px;\"><a href=\"".$urlForLinks.$state."/".$cityDashes."\" title=\"STD Testing Centers in ".$value['city']." , ".$statePieces[0]." \" >" . $value['city'] . ", $statePieces[0] STD Testing Centers</a></p>";
							if($key==0){$key++;}
							if($key % $lengthOfCol==0){
								echo "</div><div style=\"float:left;\">";
							}
						}
						echo "</div>";
				}
				
				
				else{
				?>
					<div style="height:225px; background-color:#FFDFD4; border:solid 1px #E67240; margin-bottom:20px;">
						<div style="padding:20px;">
							<div style="float:left; width:345px;">
							<h1>National STD Testing Directory</h1>
								Looking for comprehensive, convenient, and accurate STD Testing? Browse our database of nearly 4,000 STD clinics across the United States. Simply choose an STD testing lab near you, order your STD test online, and you will be able to walk-in to the lab at your convenience. After you complete your lab visit, your samples will be tested, and your std test results will be available for you to view in our convenient online results system.
							</div>
							<div style="float:right; width:280px; text-align:center;"><h2>Locate a STD Testing Center in your city and get tested today.</h2>
								<form style="text-align:right; margin-right:50px;" id="form1" name="form1" method="post" action="/order">
									<label for="state-locations-picker">State</label>
									<select style="width:150px;" name="statePicker" id="statePicker" >
										<option value="">Select A State</option>
										<?
                     foreach($locationsData as $key=>$value)
                      {
                          $stateVal=$value['state'];
                          $stateValFull=$value['stateFull'];
                          echo "<option value=\"$stateVal\">$stateValFull</option>\r\n"; 
                      }
                    ?>
									</select>
									<br />
									<div style="margin-top:10px;">
									<label for="city-locations-picker">City</label>
									<select style="width:150px;" name="cityPicker" id="cityPicker">
									<option value="">Select A State</option>
									</select>
									</div>
								</form>
								<div id="btnFindTestCenter" style="margin-top:10px; margin-left:25px; margin-bottom:10px;"><a href="javascript:void()" onclick="document.dropLocFinder.zipcode.value = document.form1.cityPicker.options[document.form1.cityPicker.selectedIndex].value;document.dropLocFinder.submit();">Find A STD Testing Center</a></div>
								Have questions? We're here to talk:<br />866-749-6269
							</div>
						</div>
					</div>
					<div style="margin-bottom:20px;">
					<?	
						
							$sticky = get_option('sticky_posts');
							rsort( $sticky );
							$sticky = array_slice( $sticky, 1, 2);
							query_posts( array( 'post__in' => $sticky, 'caller_get_posts' => 1 ) );
						
							while (have_posts()) : the_post();
								$img = get_post_meta($post->ID, 'Featured Thumbnail', true);
						?>
                        
						
						<div style="width:237px; float:left; margin-right:20px;" <?php post_class(); ?> id="post-<?php the_ID(); ?>">
							<div class="entry">
								<img style="margin-bottom:10px;" src="<?php echo $img; ?>"/><br />
								<div style="margin-bottom:5px;"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title('<h5>', '</h5>'); ?></a></div>
								<?php
								//the_excerpt();
									$lwexcerpt = limit_words(get_the_excerpt(), '40');
                                    echo $lwexcerpt;
									echo '...<br /><strong><a href="'. get_permalink($post->ID) . '">LEARN MORE</a></strong>';
								?>
							</div>
						</div>
						
						<?php
							endwhile;
						?>
						
						
						<?
						echo "
						<div style=\"background-color:#EAFFFE; height:300px; width:164px; float:left; border:solid 1px #A8F6F3;\">
							<div style=\"padding:15px; text-align:center;\">"
						?>
							<h3>Trusted &amp; Accredited</h3>
                            <br />
                            <center>
							<a target="_blank" id="bbblink" class="sevtbum" href="http://www.bbb.org/chicago/business-reviews/medical-testing-companies/healthplace-in-schaumburg-il-88387750#bbblogo" title="Healthplace, LLC is a BBB Accredited Medical Testing Company in Schaumburg, IL" style="display: block;position: relative;overflow: hidden; width: 60px; height: 98px; margin: 0px; padding: 0px;"><img style="padding: 0px; border: none;" id="bbblinkimg" src="http://seal-chicago.bbb.org/logo/sevtbum/healthplace-88387750.png" width="120" height="98" alt="Healthplace, LLC is a BBB Accredited Medical Testing Company in Schaumburg, IL" /></a><script type="text/javascript">var bbbprotocol = ( ("https:" == document.location.protocol) ? "https://" : "http://" ); document.write(unescape("%3Cscript src='" + bbbprotocol + 'seal-chicago.bbb.org' + unescape('%2Flogo%2Fhealthplace-88387750.js') + "' type='text/javascript'%3E%3C/script%3E"));</script>
                            <br />
							<img src="http://c189814.r14.cf1.rackcdn.com/asha-logo.jpg" width="60" height="58">
        					</center>
        			<?
					echo "
						</div>
					</div>
					</div>".
					"<h3>STD Testing Locations by State</h3>".
					"<p>Choose from more than 4,000 local testing centers in the U.S. that provide convenient, affordable and confidential HIV and Sexually Transmitted Disease testing. If you choose our at-home testing option, you can test in the comfort of your home. In either case, results are ready within days after the sample is received and are made available securely online. Testing is always 100% private and confidential.</p>";
					$lengthOfCol = sizeof($locationsData)/3;
					echo "<div style=\"float:left;\">";
						foreach($locationsData as $key=>$value){
							$stateDashes = str_replace(' ', '-', $value['stateFull']);
							echo "<p style=\"width:205px; margin-right:20px;\"><a href=\"".$urlForLinks.$stateDashes."\" title=\"STD Testing Centers in " . $value['state'] . "\" >" . $value['stateFull'] . " STD Testing</a></p>";
							if($key==0){$key++;}
							if($key % $lengthOfCol==0){
								echo "</div><div style=\"float:left;\">";
							}
						}
					echo "</div>";  
				}?>

			<br />


<!--
			<?if($searchType=='city' || $searchType=='zip' || $searchType=='address'){?>    
				<div id="center-map"></div>
			<?}?>
-->			
            
            
            </div>    
                        
		</div><!-- END DIV CONTENT2 -->  
        
	</div><!-- END DIV MAINCONTENT -->  
    
    
<!-- *********************************** END MAIN CONTENT *********************************** -->                
                
                
<!-- *********************************** START SIDEBAR CITY/LOCATION *********************************** -->                
                
        
        <? if($searchType=='address' || $searchType=='city' || $searchType=='zip'){ ?>
        
        <div class="column span-4 last">
            <div style="background-color:#F9FBD6; border:solid 1px #E1E5A3; margin-top:20px; margin-bottom:20px;">
                <div style="padding:15px;">
                    <strong>STD Testing Centers</strong><br />
                    *More Details Available During Ordering<br /><br />
                    <?
                    foreach($locationsData2 as $value){
                        $addressDashes = str_replace(' ', '-', $value[address1])."-".$value[zip];
                        $cityDashes = str_replace(' ', '-', $value['city']);
                        $stateDashes = str_replace(' ', '-', $locationsData[0]['stateFull']);
                    ?>
                        <div class="clinic-info" id="<?php echo 'x'.$addressDashes ?>">
                            <?$linkState = $stateDashes."-".$locationsData[0]['state'];?>
                            <a href="<?php echo $urlForLinks.$linkState."/".$cityDashes."/".$addressDashes;?>" title="<?=$value['name']?>"><?=$value['name']?></a>
                            <div class="address">
                                Location Number <?=$value['address1']?>
                                <br />
                                <?=$value['city']?>, <?=$value['state']?> <?=$value['zip']?>
                            </div>
                            <div class="extra-links">
                                <b><?=$_SESSION['affPhone']?></b> 
                                <a href="javascript:void(0);" onclick="document.sidebarOrderFrm.zipcode.value='<?=$value['zip']?>';document.sidebarOrderFrm.submit();" title="Click to order at this location"><div style="margin-bottom:15px; font-weight:bold;">Order Online</div></a>
                            </div>
                        </div>
                    <? } ?>
                </div>
            </div>
        </div>  
                          
		<? } ?>
                    
                    
<!-- *********************************** END SIDEBAR CITY/LOCATION *********************************** -->                
                    

<!-- *********************************** START SIDEBAR NATIONAL/STATE *********************************** -->                

                 
		<? if($searchType=='state' || $searchType=='default'){ ?>

		<div class="column span-4 last">
			<div style="height:100px; background-color:#F9FBD6; border:solid 1px #E1E5A3; margin-top:20px; margin-bottom:20px;">
				<div style="padding:15px;">
					<h2>STD Testing Centers Near You</h2>
					<div style="margin-top:10px; float:left;">
                    	<form id="zipcodeForm" name="zipcodeForm" method="post" action="/find-a-test-center">
							<input style="height:20px;" name="zipcode" type="zipcode" id="zipcode" value="Enter Your Zip Code" size="20" onfocus="this.value=''"/>
						</form>
                    </div>
					<div id="findZip" style="margin-top:10px; margin-left:155px; margin-bottom:10px;"><a href="javascript:void(0);" onclick="validateZipcode();">Find A STD Testing Center</a></div>
				</div>
			</div>
			<div style="width:260px; margin-bottom:20px;">
				<div style="float:left; margin-right:10px; margin-bottom:10px;">
                	<img src="http://c189814.r14.cf1.rackcdn.com/doctor-std-testing-medical-board.jpg" width="95" height="95" alt="Doctor Handsfield STD Testing Medical Board Expert" />
                </div>
				<span style="font-style:oblique;">&quot;The CDC estimates 19 million people contract STDs each year; only 50% are aware. This is a significant finding given many STDs can be cured with a simple prescription and all STDs are better managed if caught early.&quot;</span>
				<p style="margin-top:10px;"><b>H. Hunter Handsfield, M.D.</b><br />
				Medical Advisory Board<br />
				<span style="font-size:10px;">STDtesting.com, a division of<br />the Institute of Sexual Health</span></p>
			</div>
            <div style="width:260px;">
                <h4>Test Today</h4>
                No wait. Order and test for STDs today.<br /><br />

                <h4>Fast Results</h4>
                STD test results available within 1-5 business days through confidential online patient portal.<br /><br />
                
                <h4>Treatment Options</h4>
                STDs are better managed with early detection; some STDs are even curable with a simple prescription prescribed by our in-house physician network.<br /><br />
                
                <h4>Free Physician Consult</h4>
                Ask questions and get help interpreting personal STD test results through a complimentary consultation with our in-house physician network.<br /><br />
                
                <h4>Highest Accuracy Available</h4>
                We partner with the same trusted labs used by doctors and hospitals nationwide, offering the highest accuracy STD testing available on the market; 99.9% STD test accuracy.<br /><br />
                
                <h4>Convenient</h4>
                With partnerships at over 4,000+ nationwide labs, select a private lab close to your work or home in (INSERT STATE)<br /><br />
                
                <h4>No insurance records</h4>
                Test results are completely private.
            </div>
		</div>                    

		<? }?>
                    
                    
<!-- *********************************** END SIDEBAR NATIONAL/STATE *********************************** -->                

    
</div><!-- END DIV PAGE -->                
    
    
    <? 
								$stateDashes = str_replace(' ', '-', $locationsData[0]['stateFull']);
								$linkState = $stateDashes."-".$locationsData[0]['state'];
								echo "<div>People arrived here looking for: ".$locationsData[0]['city']." STD Centers, ".$locationsData[0]['city']." STD Clinics, STD Testing in ".$locationsData[0]['city'].", ".$locationsData[0]['city']." STD testing, ".$locationsData[0]['city']." S.T.D. Testing, ".$locationsData[0]['city']." HIV Testing, Free STD Clinics in ".$locationsData[0]['city'].", Free STD Testing in ".$locationsData[0]['city'].", H.I.V. Testing ".$locationsData[0]['city'].", STD Testing ".$locationsData[0]['state'].", STD Testing in ".$locationsData[0][zip]."</div>";
						 ?>
    
    
    
    
<form name="sidebarOrderFrm" method="POST" action="/order">
<input type="hidden" name="zipcode">
</form>

<form name="dropLocFinder" method="POST" action="/find-a-test-center">
<input type="hidden" name="zipcode">
</form>    
    

<input type="hidden" size="10" id="addressInput" name="addressInput" value="<?=$_SESSION['addressInput']?>"/>     
<!-- Google Maps and some Javascript magic... -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.js"></script>
<?if($searchType=='city' || $searchType=='zip' || $searchType=='address'){?>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>

<script>
function createXmlHttpRequest() {
 try {
   if (typeof ActiveXObject != 'undefined') {
     return new ActiveXObject('Microsoft.XMLHTTP');
   } else if (window["XMLHttpRequest"]) {
     return new XMLHttpRequest();
   }
 } catch (e) {
   changeStatus(e);
 }
 return null;
};

function downloadUrl(url, callback) {
 var status = -1;
 var request = createXmlHttpRequest();
 if (!request) {
   return false;
 }

 request.onreadystatechange = function() {
   if (request.readyState == 4) {
     try {
       status = request.status;
     } catch (e) {
       // Usually indicates request timed out in FF.
     }
     if (status == 200) {
       callback(request.responseXML, request.status);
       request.onreadystatechange = function() {};
     }
   }
 }
 request.open('GET', url, true);
 try {
   request.send(null);
 } catch (e) {
   changeStatus(e);
 }
};

function xmlParse(str) {
  if (typeof ActiveXObject != 'undefined' && typeof GetObject != 'undefined') {
    var doc = new ActiveXObject('Microsoft.XMLDOM');
    doc.loadXML(str);
    return doc;
  }

  if (typeof DOMParser != 'undefined') {
    return (new DOMParser()).parseFromString(str, 'text/xml');
  }

  return createElement('div', null);
}

function downloadScript(url) {
  var script = document.createElement('script');
  script.src = url;
  document.body.appendChild(script);
}

var map;
var zipvalue;
var geocoder;

  function initialize() {
    var myLatlng = new google.maps.LatLng(40, -100);
    var zipvalue = $('#addressInput').val();
    geocoder = new google.maps.Geocoder();
    var myOptions = {
      zoom: 3,
      disableDefaultUI: true,
      navigationControl: true,
      center: myLatlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(document.getElementById("center-map"), myOptions);
    if (zipvalue) searchLocations('');
  }
  
  function codeAddress() { 
  var address = jQuery('#addressInput').val(); 
  geocoder.geocode( { 'address': address}, function(results, status) { 
  if (status == google.maps.GeocoderStatus.OK) { 
  map.setCenter(results[0].geometry.location); 
  var marker = new google.maps.Marker({ 
  map: map, 
  position: results[0].geometry.location 
  });
  map.setZoom(11); 
  } 
  }); 
  }
  
  function searchLocations(affPhone) 
	{
    if (!affPhone || affPhone=='') affPhone='866-749-6269';
    zipvalue = $('#addressInput').val();
		var zipEntry = document.getElementById('addressInput').value;
		if(!zipEntry || zipEntry=='Zip Code')
		{
			alert('Please enter valid Zipcode');
			$('#addressInput').focus();
			return false;
		}
		else
		{
          searchLocationsNear(affPhone);
          return false;
		}
	}
	
	function searchLocationsNear(affPhone) 
	{
      var docRoot = '<?=$docRoot?>';
      
      downloadUrl("/phpsqlsearch_genxml2.php?zip="+zipvalue, function(data) {
      var markers = data.documentElement.getElementsByTagName("marker");
      for (var i = 1; i < markers.length; i++) 
      {
        if (i==1) {markerLetter = 'A';classNum='one';}
        if (i==2) {markerLetter = 'B';classNum='two';}
        if (i==3) {markerLetter = 'C';classNum='three';}
        if (i==4) {markerLetter = 'D';classNum='four';}
        if (i==5) {markerLetter = 'E';classNum='five';}
        if (i==6){markerLetter = 'F';classNum='six';}
        if (i==7) {markerLetter = 'G';classNum='seven';}
        if (i==8) {markerLetter = 'H';classNum='eight';}
        if (i==9) {markerLetter = 'I';classNum='nine';}
        if (i==10) {markerLetter = 'J';classNum='ten';}
        if (i==11) {markerLetter = 'K';classNum='eleven';}
        if (i==12) {markerLetter = 'L';classNum='twelve';}
        if (i==13) {markerLetter = 'M';classNum='thirteen';}
        if (i==14) {markerLetter = 'N';classNum='fourteen';}
        if (i==15) {markerLetter = 'O';classNum='fifteen';}  
        
        var image = new google.maps.MarkerImage('http://www.google.com/mapfiles/marker'+markerLetter+'.png', 
                      new google.maps.Size(20, 34), 
                      new google.maps.Point(0, 0), 
                      new google.maps.Point(10, 34)); 

        var markerImg =  'http://www.google.com/mapfiles/marker'+markerLetter+'.png';
        
        var latlng = new google.maps.LatLng(parseFloat(markers[i].getAttribute("lat")),
                                    parseFloat(markers[i].getAttribute("lng")));
        
        if (i==1) var latPrime = latlng;
        
          var locid = markers[i].getAttribute('id');
					var name = markers[i].getAttribute('name');
					var labAddr = markers[i].getAttribute('address');
					var labCity = markers[i].getAttribute('city');
					var labState = markers[i].getAttribute('state');
					var labZipcode = markers[i].getAttribute('zip');
					var labHours = markers[i].getAttribute('hours');
					var labPhone = markers[i].getAttribute('telephone');
					var labType = markers[i].getAttribute('lab-id');
          var distance = parseFloat(markers[i].getAttribute('distance'));
          
          var labName = "Quest Diagnostics";
          if (labType == "129") labName = "Labcorp"; 
          
          var marker = new google.maps.Marker
        ({
          position: latlng, map: map,
          icon: image,
          title: labName+' - '+labAddr
        });
       }
       map.setCenter(latPrime);
       map.setZoom(11);
     });
	}
</script>


<script language="javascript">
$(document).ready(function(){
  initialize();  
});
</script>
<?}?>


<script language="javascript">
$(document).ready(function(){
  $("#statePicker").change(function() {
  var state = $("#statePicker").val()
  $("#cityPicker").load("populate-city-dropdown.php?state="+state);
  }); 
});
</script>

<script>
function validateZipcode() {
var valid = "0123456789-";
var hyphencount = 0;
field = document.zipcodeForm.zipcode.value;

if (field.length!=5 && field.length!=10) {
alert("Please enter your 5 digit or 5 digit+4 zip code.");
return false;
}
for (var i=0; i < field.length; i++) {
temp = "" + field.substring(i, i+1);
if (temp == "-") hyphencount++;
if (valid.indexOf(temp) == "-1") {
alert("Invalid characters in your zip code.  Please try again.");
return false;
}
if ((hyphencount > 1) || ((field.length==10) && ""+field.charAt(5)!="-")) {
alert("The hyphen character should be used with a properly formatted 5 digit+four zip code, like '12345-6789'.   Please try again.");
return false;
   }
}
document.zipcodeForm.submit();
}
</script>

<?php get_footer();?>



