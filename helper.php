<?php
/**
 * Helper class for jbibtexpublications module
 * 
 * @license        GNU/GPL, see LICENSE.php
 * mod_jbibtexpublications is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
require_once dirname(__FILE__) . '/lib/bibtexParse/PARSEENTRIES.php';
require_once dirname(__FILE__) . '/lib/bibtexParse/PARSECREATORS.php';

class ModBibTexPublicationsHelper
{
    /**
     * Retrieves the publications oncoded in the bibtex format
     *
     * @param   array  $params An object containing the module parameters
     *
     * @access public
     */    
    public static function getPublications($params)
    {
        $parse = NEW PARSEENTRIES();
		$parse->expandMacro = FALSE;
		$parse->removeDelimit = TRUE;
		$parse->fieldExtract = TRUE;
		$parse->loadBibtexString($params->get('bibtexsource', ''));
		$parse->extractEntries();
		list($preamble, $strings, $entries, $undefinedStrings) = $parse->returnArrays();
		$creatorparser = new PARSECREATORS();
		foreach($entries as &$entry) {
			$entry['author'] = $creatorparser->parse($entry["author"]);
		}
		return $entries;
    }
}