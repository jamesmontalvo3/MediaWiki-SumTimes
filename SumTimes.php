<?php
/** 
 * Please add the description of the SumTimes extension here...
 * 
 * Documentation: http://???
 * Support:       http://???
 * Source code:   http://???
 *
 * @file SumTimes.php
 * @addtogroup Extensions
 * @author Daren Welsh
 * @author James Montalvo
 * @copyright © 2013 by Daren Welsh
 * @licence GNU GPL v3+
 */

# Not a valid entry point, skip unless MEDIAWIKI is defined
/**
 * @help: This just keeps anyone from navigating to:
 * http://example.com/wiki/extensions/SumTimes/SumTimes.php
 **/
if (!defined('MEDIAWIKI')) {
	die( "SumTimes extension" );
}

/**
 * @help: This registers the extension so it shows up on [[Special:Version]]
 * I don't think it has any other purpose besides that. Basically it allows the 
 * admins/users to see what extensions are installed...otherwise you'd be 
 * executing non-core-MW code without any indication that that was happening
 **/
$wgExtensionCredits['specialpage'][] = array( // I don't think this should be of type "specialpage"....
	'path'           => __FILE__,
	'name'           => 'Sum Times',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:SumTimes',
	'author'         => array('Daren Welsh','James Montalvo'),
	'descriptionmsg' => 'sumtimes-desc',
	'version'        => '0.1.0'
);

/**
 * @help: variable for the directory of this file
 **/
$dir = dirname( __FILE__ ) . '/';

/**
 * @help: Add a "messages" file. This is where any language-specific text that the
 * user might interact with is defined. So if your extension added an edit link
 * to a new part of the page, your messages file would specify that in English the
 * link would say "edit" and Spanish would say "Editar" or something...
 **/
$wgExtensionMessagesFiles['SumTimes'] = $dir . 'SumTimes.i18n.php';

/**
 * @help: This makes it so developers can use this extension without explicitly
 * loading the code. Instead the code loads only at the time that it is actually
 * needed at the first time the developer uses it.
 **/
$wgAutoloadClasses['SumTimes'] = $dir . 'SumTimes.body.php';

/**
 * @help: This adds a function to be called when mediawiki reaches the hook 
 * "ParserFirstCallInit". The function is the method "setup" with the class 
 * "SumTimes"
 **/
// Specify the function that will initialize the parser function.
$wgHooks['ParserFirstCallInit'][] = 'SumTimes::setup';
