<?php

mkdir ('newindexes', 0777);

foreach (array(
"agent.cdb",
"agent.text.cdb",
"all.cdb",
"all.text.cdb",
"city.cdb",
"city.text.cdb",
"date.cdb",
"date.text.cdb",
"engraver.cdb",
"engraver.text.cdb",
"huelsen.cdb",
"huelsen.text.cdb",
"inscription.cdb",
"inscription.text.cdb",
"number.cdb",
"number.text.cdb",
"printmaker.cdb",
"publisher.cdb",
"publisher.text.cdb",
"subject.cdb",
"subject.text.cdb",
"title.cdb",
"title.text.cdb"
) as $file) {
    copy($file, 'newindexes/' . $file);
    rename('newindexes/' . $file, $file);
    chown($file, 'www');
    chmod($file, 0777);
    
}

?>
