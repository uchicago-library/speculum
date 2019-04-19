<?php

// find a refid element that is not inside the location element. 

$xml = new DOMDocument();
$xml->load('speculum.xml');

$xp = new DOMXPath($xml);

foreach ($xp->query('//refid[not(parent::location)]') as $n) {
    printf("%s\n", $n->nodeValue);
    printf("***\n");
}

?>
