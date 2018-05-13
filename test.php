<?php
	//Author: Sander Hansen, Sander.Hansen@gmail.com
	include('parser.php');
	
	$parser = new PHPTemplateParser();
	
	//It's important to realize that in order to optimize all of this, it's best to save the output of this
	//As either a hardcoded string or in a database. For the sake of clarity of use, I will recreate the data
	//every time the script is loaded though.
	$replacementObject = new StdClass();
	
	//Proof that we can change "meta data". Check page title to see the effect of this.
	$parser -> AddReplacementData($replacementObject, "{title_of_page}", "My Title");
	//Prrof that we can set up a query that searches for multiple strings at once.".
	$parser -> AddReplacementData($replacementObject, ["{page_header_1}", "{page_header_2}"], "My Header or Title");
	//Proof that we can use regex to do general search implementations.
	$parser -> AddRegexReplacementData($replacementObject, "/{.*}/", "[NOT YET IMPLEMENTED]");
		
	
	echo $parser -> ProcessHTMLFile("testTarget.php", $replacementObject);
?>