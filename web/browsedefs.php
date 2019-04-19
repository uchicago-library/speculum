<?php

/*
 * BROWSE FUNCTIONS 
 */

/*
 * Process Browse.
 * input:
 * clean assoc. array - we'll pull the 
 * browse name from this and open an xml
 * file with that name.
 *
 * output:
 * domDocument of that xml file.
 */

function processBrowse($clean) {
	$domOUT = new DOMDocument();
	switch ($clean['browse']) {
		case 'date':
		case 'number':
		case 'huelsen':
		case 'city':
		case 'collection':
		case 'engraver':
		case 'printmaker':
		case 'publisher':
		case 'subject':
			$domOUT->load("browses/" . $clean['browse'] . ".browse.xml");
			break;
		default:
			break;
	}
	return $domOUT;
}

function getBrowse2Title($in, $group) {
	$xp = new DOMXPath($in);
	$q = '/browse/group[' . $group . ']/@name';
	$nl = $xp->query($q);
	$name = '';
	if ($nl->length > 0) {
		$name = $nl->item(0)->nodeValue;
	}
	return $name;
}

/* 
 * Browse, step 2 works by cutting down the input xml file from this:
 * #document > browse > group[n] > findaid[n] > work
 * to this:
 * #document > browse > group > findaid[n] > work
 */
function prepBrowse2($in, $group) {
	$xp = new DOMXPath($in);
	
	$nl = $xp->query('/browse/group[' . $group . ']');
	$groupNode = $nl->item(0);

	$nl = $xp->query('findaid', $groupNode);
	$i = 0;
	while ($i < $nl->length) {
		if ($nl->item($i) != $group) 
			$groupNode->removeChild($nl->item($i));
		$i++;
	}
	return $in;
}

?>
