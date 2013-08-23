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
	 * @help: This is the method[1] called when 
	 *
	 * [1] a method is what you call a function that is part of a class. A class
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

		// SFH_OBJECT_ARGS not defined
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
	 *
	 *
	 *	I HAVEN'T GOTTEN HERE YET...
	 *
	 *
	 **/



	static function renderCopyWatchersNonObj (&$parser, $pagesToCopyWatchers='', $showOutput=false) {
		
		$pagesToCopyWatchers = explode(',', $pagesToCopyWatchers);
		
		if ( $showOutput == 'true' )
			$showOutput = true;
		
		return self::renderCopyWatchers( $parser, $pagesToCopyWatchers, $showOutput );
		
	}
	
	static function renderCopyWatchersObj ( &$parser, $frame, $args ) {
		
		$pagesToCopyWatchers = explode(',', $frame->expand( $args[0] ) );
	
		if ( isset( $args[1] ) && trim( $frame->expand( $args[1] ) ) == 'true' )
			$showOutput = true;
		else
			$showOutput = false;
	
		return self::renderCopyWatchers( $parser, $pagesToCopyWatchers, $showOutput );
	
	}
	
	static function renderCopyWatchers ( &$parser, $pagesToCopyArray, $showOutput ) {
		global $wgCanonicalNamespaceNames;

		$newWatchers = array();
		
		$output = "Copied watchers from:\n\n";
		
		foreach( $pagesToCopyArray as $page ) {
			
			$output .= "* $page";

			// returns Title object
			$titleObj = self::getNamespaceAndTitle( trim($page) );
			
			if ( $titleObj->isRedirect() ) {
				$redirectArticle = new Article( $titleObj );
				
				// FIXME: thought newFromRedirectRecurse() would find the ultimate page
				// but it doesn't appear to be doing that
				$titleObj = Title::newFromRedirectRecurse( $redirectArticle->getContent() );
				$output .= " (redirects to " . $titleObj->getFullText() . ")";
				
				// FIXME: Do this for MW 1.19+ ???
				// $wp = new WikiPage( $titleObj );
				// $titleObj = $wp->followRedirect();
				
				// FIXME: Do one of these for MW 1.21+ ???
				// WikiPage::followRedirect()
				// Content::getUltimateRedirectTarget()

			}
			
			$ns_num = $titleObj->getNamespace();
			$title  = $titleObj->getDBkey();			

			unset( $titleObj ); // prob not necessary since it will be reset shortly.
			
			$watchers = self::getPageWatchers( $ns_num, $title );
			$num_watchers = count($watchers);
			
			if ($num_watchers == 1)
				$output .= " (" . count($watchers) . " watcher)\n";
			else
				$output .= " (" . count($watchers) . " watchers)\n";

			foreach ( $watchers as $userID => $dummy ) {
				$newWatchers[$userID] = 0; // only care about $userID, and want unique.
			}

		}
		
		// add list of usernames as watchers to this Title
		foreach ($newWatchers as $userID => $dummy) {
			$u = User::newFromId($userID);
			$u->addWatch( $parser->getTitle() );
		}
		
		if ( $showOutput )
			return $output;
		else
			return "";
			
	}
	
	static function getNamespaceAndTitle ( $pageName ) {
	
		// defaults
		$ns_num = NS_MAIN;
		$title = $pageName;

		$colonPosition = strpos( $pageName, ':' ); // location of colon if exists
		
		// this won't test for a leading colon...but shouldn't use parser function that way anyway...
		if ( $colonPosition ) {
			$test_ns = self::getNamespaceNumber( 
				substr( $pageName, 0, $colonPosition )
			);
			
			// only reset $ns and $title if has colon, and pre-colon text actually is a namespace
			if ( $test_ns !== false ) {
				$ns_num = $test_ns;
				$title = substr( $pageName, $colonPosition+1 );
			}
		}
		
		return Title::makeTitle( $ns_num, $title );
		//return (object)array("ns_num"=>$ns_num, "title"=>$title);
	
	}
	
	// returns number of namespace (can be zero) or false. Use ===.
	static function getNamespaceNumber ( $ns ) {
		global $wgCanonicalNamespaceNames;
		
		foreach ( $wgCanonicalNamespaceNames as $i => $text ) {
			if (preg_match("/$ns/i", $text)) {
				return $i;
			}
		}
	
		return false; // if $ns not found above, does not exist
	}
	
	static function getPageWatchers ($ns, $title) {
		
		// code adapted from Extension:WhoIsWatching
		$dbr = wfGetDB( DB_SLAVE );
		$watchingUserIDs = array();
		
		
		$res = $dbr->select(
			'watchlist',
			'wl_user', 
			array('wl_namespace'=>$ns, 'wl_title'=>$title),
			__METHOD__
		);
		foreach ( $res as $row ) {
			$watchingUserIDs[ $row->wl_user ] = 0; // only care about the user ID, and want unique
		}

		return $watchingUserIDs;
			
	}
}