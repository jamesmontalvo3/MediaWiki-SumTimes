<?php
/**
 * Add description of SumTimes extension
 *
 * Some code adapted from Extension:WhoIsWatching
 * 
 * Documentation: http://???
 * Support:       http://???
 * Source code:   http://???
 *
 * @file SumTimes.body.php
 * @addtogroup Extensions
 * @author Daren Welsh
 * @author James Montalvo
 * @copyright © 2013 by Daren Welsh
 * @licence GNU GPL v3+
 */

class SumTimes
{

	/**
	 * @help: "setup" is the method called when MediaWiki reaches the 
	 * "ParserFirstCallInit" hook. 
	 *
	 * A method is what you call a function that is part of a class. A class
	 * is the definition of an object. So you create a class, then in code you
	 * create instances of that class...those instances are called objects.
	 *
	 * This method is defined as "static", which means you don't actually have to
	 * create an object (an instance of the class) to call the method. 
	 *
	 * The way this class "SumTimes" is used, I don't believe you'll ever actually
	 * create an object. You'll purely be using static methods. The reason for
	 * creating a class in this case is to compartmentalize the code...so you can
	 * have a method called "setup" in SumTimes, and I can have a method called 
	 * the same thing in CopyWatchers
	 *
	 * The only argument (parameter) passed to "setup" is $parser. This is passed to all
	 * functions using the "ParserFirstCallInit" hook, and it is a reference to the parser
	 * object which you can read more about here:
	 * https://doc.wikimedia.org/mediawiki-core/master/php/html/classParser.html
	 *
	 * Right before the $parser argument there is an "&". This means the $parser
	 * object is being "passed by reference" as opposed to "passed by value". 
	 * You can look up those terms to find out what they mean, but passed
	 * by reference means that any changes you make to the $parser object will
	 * affect the $parser object throughout the entire application. 
	 **/
	static function setup ( &$parser ) {
		
		// I'm not really sure what the benefits of two ways of calling setFunctionHook
		// but I've implemented them both here. They both essentially end up calling
		// the same method: renderCopyWatchers().

		/**
		 * @help: $parser is an object, and all objects are defined by classes.
		 * "defined()" is checking if the class of $parser has the property 
		 * "SFH_OBJECT_ARGS" defined.
		 *
		 * I personally haven't figured out what this does. I think that if 
		 * SFH_OBJECT_ARGS is defined, then the MW version is newer, and you can 
		 * used a "better" way of coding.
		 **/
		if ( defined( get_class( $parser ) . '::SFH_OBJECT_ARGS' ) ) {

			// Call the setFunctionHook method of the $parser object, explained here:
			// https://doc.wikimedia.org/mediawiki-core/master/php/html/classParser.html#a4979a4906f0cb0c1823974a47d5bd12f
			$parser->setFunctionHook( 

				// @help: argument #1 of setFunctionHook:
				// The "magic word" that your parser function uses. So the word following
				// the hash in a parser function, like: {{#myfunction: ... }}
				'copywatchers', 

				// @help: argument #2 of setFunctionHook:
				// This defines the function that the data within {{#myparserfunc: MY | Data | here }}
				// gets passed to. So every time you call {{#myparserfunc: ... }} it get
				array(
					'CopyWatchers',
					'renderCopyWatchersObj' 
				), 

				// @help: argument #3 of setFunctionHook:
				// since SFH_OBJECT_ARGS is defined, this tells the setFunctionHook
				// method that you'll be using the "better" syntax
				SFH_OBJECT_ARGS 
			);

		// SFH_OBJECT_ARGS not defined. Basically the same as above, with a different
		// method used instead
		} else {
			
			$parser->setFunctionHook(
				'copywatchers',
				array(
					'CopyWatchers',
					'renderCopyWatchersNonObj'
				) 
			);
		}

		return true;
		
	}

	/**
	 *	@param &$parser : reference to the $parser object.
	 *  @param $timeString : list of times, separated by commas. Another parameter
	 *		could be added to change what the separator is.
	 *  @param $format : Used to specify what format the time strings are in. So
	 *		whether they are hour:minute or minute:second. For now I'd just leave
	 *		this out of the function and focus on the hour:minute version
	 *
	 *	Basically this method just passes the data to the doTimeAddition() method
	 **/
	static function renderSumTimesNonObj (&$parser, $timeString='', $format=false) {
		
		// break apart the time string on commas
		$timeArray = explode(',', $timeString);
				
		return self::doTimeAddition( $parser, $pagesToCopyWatchers, $format );
		
	}
	
	/**
	 *	Similar to the above method, but varied for the "SFH_OBJECT_ARGS" version.
	 *	Again, this basically just passes the data on to the doTimeAddition() method.
	 **/
	static function renderSumTimesObj ( &$parser, $frame, $args ) {
		
		// I have no idea why $frame->expand( $args[0] ) is better than the other version
		// I'm guessing it gives you more control over the data somehow.
		$timeArray = explode(',', $frame->expand( $args[0] ) );
	

		if ( isset( $args[1] ) )
			$format = trim( $frame->expand( $args[1] ) );
		else
			$format = false;
	
		return self::doTimeAddition( $parser, $pagesToCopyWatchers, $format );
	
	}
	
	/**
	 *	Here's where you'll do your math
	 **/
	static function doTimeAddition ( &$parser, $times, $format ) {
				
		foreach( $times as $time ) {
			
			$t = explode(":", $time);
			$hour = $t[0];
			$min  = $t[1];

			// add hours and minutes and such

			// I'm not sure how much you'll need to handle cases of 05:07 versus 5:07

		}
		
		return $output;

	}
	
}