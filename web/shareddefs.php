<?php

function replace_accents($s) {
    $s = htmlentities($s, ENT_COMPAT, "UTF-8");
    $s = preg_replace ('/&([a-zA-Z])(uml|acute|grave|circ|tilde|cedil|ring);/', '$1', $s);
    $s = html_entity_decode($s);
    return $s;
}

function getmicrotime() {
	$timeparts = explode(' ', microtime());
	return (float)$timeparts[1].substr($timeparts[0],1);
}

?>
