<?php if (!is_search()) {
	// Default search text
	$search_text = "Search All About STDs";

} else { $search_text = "$s"; }
?>

<div style="float:left; margin-left:210px;">Questions? <b><?php echo affPhoneNumber();?></b> | <a href="https://secure.stdtesting.com/my-result">My Results</a> |</div>
<div id="search">
	<div>
	<form action="<? echo $url = site_url('/stdtesting-search/', 'http');?>" method="post" name="stdtestsearchform">
	<input type="text" name="q" />
	<input type="submit" name="search" value="Search" />
	</form>
</div>

</div>
