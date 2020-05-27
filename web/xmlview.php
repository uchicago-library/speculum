<?php
require_once('searchdefs.php');

function cleanPid($get) {
	$parts = explode('-', $get);

	// 'A1', '994', '0500'
	if (count($parts) == 1) {
		$numstr = ltrim($parts[0], " a..zA..Z");
		if ((int)$numstr > 994 || (int)$numstr < 1) {
			return 'speculum-0001-001';
		}
		if (!(strlen($numstr) <= 4 && ctype_digit($numstr))) {
			return 'speculum-0001-001';
		}
		$numpadded = "speculum-" . str_pad($numstr, 4, "0", STR_PAD_LEFT) . "-001";
		return $numpadded;
	}

	// 'speculum-0295-003'
	if (count($parts) != 3) {
		return 'speculum-0001-001';
	}
	if ($parts[0] != 'speculum') {
		return 'speculum-0001-001';
	}
	if (!(strlen($parts[1]) == 4 && ctype_digit($parts[1]))) {
		return 'speculum-0001-001';
	}
	if (!(strlen($parts[2]) == 3 && ctype_digit($parts[2]))) {
		return 'speculum-0001-001';
	}
	return implode('-',$parts);
}

$clean = array();
$clean['id'] = 'speculum-0001-001';
if (isset($_GET['id'])) {
	$clean['id'] = cleanPid($_GET['id']);
}

$tmp = explode('-', $clean['id']);
$clean['refid'] = getChicagoNumber($tmp[1]);

$xml = buildFindingAid(array($clean['refid']));

echo $xml->saveXML();
?>
