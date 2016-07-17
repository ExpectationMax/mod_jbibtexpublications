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

class Filter {
        private $filterfield;
		private $filtervalue;
		
        function __construct($field, $value) {
                $this->filterfield = $field;
				$this->filtervalue = $value;
				echo "inited Filter with: ".$field.' '.$value;
        }

        function keep($i) {
			if(is_array($i))
				$keep = in_array($this->filtervalue, $i[$this->filterfield]);
			else
                $keep = $i[$this->filterfield] == $this->filtervalue;
				
			echo 'Keep '.$i.': '.$keep;
			return $keep;
        }
}

function startsWith($haystack, $needle) {
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
}

function endsWith($haystack, $needle) {
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
}


function parse_arrays($publ) {
	foreach($publ as $key => $value) {
		if(is_string($value) && startsWith($value, '[') && endsWith($value, ']')) {
			$striped = trim($value, '[]');
			$elements = array_map('trim', explode(',', $striped));
			$publ[$key] = $elements;
		}
	}
	return $publ;
}

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
			$entry = parse_arrays($entry);
		}
		
		if($params->get("filterfield", "") != "" && $params->get("filtervalue", "") != "") {
			$entries = array_filter($entries, array( new Filter( $params->get("filterfield"), $params->get("filtervalue")), "keep"));
			echo "Filtering!";
		} else {echo "Not filtering!";}
		return $entries;
    }
}