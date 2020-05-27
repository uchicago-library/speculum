<?php

// view.php?id=speculum-0295-002

$clean = array();

/*
 * CLEAN SPECULUM PIDS. THE PID WILL EITHER HAVE DASHES OR IT WON'T.
 * IF IT HAS NO DASHES, IT CAN BE A 'A1' OR '994' OR '0500' STYLE
 * PID. IF IT HAS DASHES, IT MUST BE 'SPECULUM-0295-003' STYLE.
 */

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

$clean['id'] = 'speculum-0001-001';
if (isset($_GET['id'])) {
	$clean['id'] = cleanPid($_GET['id']);
}
$imagepath = 'images/zoomify/' . $clean['id'];

$document_number = explode('-', $clean['id'])[1];
if (preg_match('/^[0-9]{4}$/', $document_number) !== 1) {
    die();
}

$manifest_uri = sprintf(
    'https://iiif-manifest.lib.uchicago.edu/speculum/%s/speculum-%s.json',
    $document_number,
    $document_number
);

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" type="text/css" href="uv/uv.css"/>
        <script src="https://unpkg.com/resize-observer-polyfill@1.5.1/dist/ResizeObserver.js"></script>
        <script type="text/javascript" src="uv/uv-assets/js/bundle.js"></script>
        <script type="text/javascript" src="uv/uv-helpers.js"></script>
        <script type="text/javascript" src="uv/uv-dist-umd/UV.js"></script>
        <title>The Speculum Romanae Magnificentiae</title>
        <style>
            html, body {
                 height: 100%;
                 margin: 0;
            }   
            #uv {
                width: 100%;
                height: 100%;
            }   
        </style>
    </head>
    <body>

        <div id="uv"></div>

        <script type="text/javascript">
            urlDataProvider = new UV.URLDataProvider();

            var manifest_uri = "<?php echo $manifest_uri; ?>";

            var uv = createUV("uv", {
                manifestUri: manifest_uri,
                assetsDir: "uv/uv-assets",
                configUri: "uv-config.json"
            }, new UV.URLDataProvider());
        </script>

    </body>
</html>
