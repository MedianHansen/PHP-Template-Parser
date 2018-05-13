<?php
	//Author: Sander Hansen, Sander.Hansen@gmail.com
	//This class will be used for parsing different data sets. The focus of this iteration of
	//The class will be parsing an html file.
	class PHPTemplateParser
	{
		//here we load an html file, process it and sends it right back, so the caller can use it for whatever they need.
		//Path: The path to the file to be processed.
		//Replacement Data: See ParseTemplateString.
		public function ProcessHTMLFile($path, $replacementData)
		{
			//Load the HTML file.
			$html = file_get_contents($path);
			
			$html = $this -> ParseTemplateString($html, $replacementData);
			
			//Return the output.
			return $html;
		}
		
		//This is it's own function as that helps us scale better, maybe we want to use the parser in the future
		//on targets other than HTML pages, so this makes for an easy hook in.
		//Input: The data to parse.
		//Replacement Data: The data telling the parser which data to replace.
		public function ParseTemplateString($input, $replacementData)
		{
			//First replace all given strings with their values. Strings takes precedens
			//because these are usually more hand picked and less likely to make a false
			//positive.
			if ($replacementData -> entries)
			{
				foreach ($replacementData -> entries as $entry)
				{
					foreach ($entry -> keys as $key)
					{
						$input = str_replace($key, $entry -> replacementValue, $input);
					}
				}
			}
			
			//Now replace all occourences found by our regex. 
			if ($replacementData -> regexEntries)
			{
				foreach ($replacementData -> regexEntries as $entry)
				{
					$input = preg_replace($entry -> regex, $entry -> replacementValue, $input);
				}
			}
			
			return $input;
		}
		
		//This function is used to create the replacement data in a human way.
		//It's important to realize that in order to optimize all of this, it's best to save the output of this
		//As either a hardcoded string or in a database.
		//Object: The object to save the data to (Type should be stdClass).
		//Keys: Either an array of keys or a single key, to be replaced. You might want to use an array if you want multiple
			//keys to be replaced by the same value.
		//Replacement Value: The value to replace the key(s) with.
		public function AddReplacementData($object, $keys, $replacementValue)
		{
			//If the argument is not delivered as an array auto convert it. (This makes for faster lazy typing).
			if (!is_array($keys))
			{
				$keys = [$keys];
			}
			
			//Create a new entry for the replacementObject.
			$entry = new stdClass();
			$entry -> keys = $keys;
			$entry -> replacementValue = $replacementValue;
			
			//Add the entry to the replacementObject
			$object -> entries[] = $entry;
		}
		
		//This function is used to create the replacement regex data in a human way.
		//It's important to realize that in order to optimize all of this, it's best to save the output of this
		//As either a hardcoded string or in a database.
		//Object: The object to save the data to (Type should be stdClass).
		//Regex: the regex expression to be replaced. You might want to use an array if you want multiple
			//regexes to be replaced by the same value.
		//Replacement Value: The value to replace the key(s) with.
		public function AddRegexReplacementData($object, $regex, $replacementValue)
		{
			//If the argument is not delivered as an array auto convert it. (This makes for faster lazy typing).
			if (!is_array($regex))
			{
				$regex = [$regex];
			}
			
			foreach ($regex as $key)
			{
				//This method of validating regex found at: https://stackoverflow.com/questions/10778318/test-if-a-string-is-regex
				//It might seem wierd to actually test against false rather than use the ! operator, but the ! operator will consider
				//0 as false as well, which is not acceptable in our case, we need a test against true false.
				//We do NOT add invalidated regex to the parser data, it will not run.
				//TODO: Throw an error into whatever bug catching system this should be hooked into.
				if(@preg_match($key, null) !== false)
				{
					//Add as a special case instead of as a normal case.
					//Create a new entry for the replacementObject.
					$entry = new stdClass();
					$entry -> regex = $key;
					$entry -> replacementValue = $replacementValue;
					
					//Add the entry to the replacementObject
					$object -> regexEntries[] = $entry;
					
					//Return, rest of code is not necessary when a regex is provided
					return;
				}
			}
		}
	}
?>