<?php
/**
 * Helper file for downloading Public Suffix List and converting it to PHP array
 *
 * You can run this script to update PSL to the current version instead of
 * waiting for a new release of HTTP_Request2.
 *
 * NB: peer validation is DISABLED when downloading. If you want to enable it,
 * change ssl_verify_peer to true and provide CA file (see below)
 */

/** URL to download Public Suffix List from */
define('LIST_URL',    'https://publicsuffix.org/list/public_suffix_list.dat');
/** Name of PHP file to write */
define('OUTPUT_FILE', dirname(__FILE__) . '/public-suffix-list.php');

require_once 'HTTP/Request2.php';

function buildSubdomain(&$node, $tldParts)
{
    $part = trim(array_pop($tldParts));

    if (!array_key_exists($part, $node)) {
        $node[$part] = array();
    }

    if (0 < count($tldParts)) {
        buildSubdomain($node[$part], $tldParts);
    }
}

function writeNode($fp, $valueTree, $key = null, $indent = 0)
{
    if (is_null($key)) {
        fwrite($fp, "return ");

    } else {
        fwrite($fp, str_repeat(' ', $indent) . "'$key' => ");
    }

    if (0 == ($count = count($valueTree))) {
        fwrite($fp, 'true');
    } else {
        fwrite($fp, "array(\n");
        for ($keys = array_keys($valueTree), $i = 0; $i < $count; $i++) {
            writeNode($fp, $valueTree[$keys[$i]], $keys[$i], $indent + 1);
            if ($i + 1 != $count) {
                fwrite($fp, ",\n");
            } else {
                fwrite($fp, "\n");
            }
        }
        fwrite($fp, str_repeat(' ', $indent) . ")");
    }
}


try {
    $request  = new HTTP_Request2(LIST_URL, HTTP_Request2::METHOD_GET, array(
        // Provide path to your CA file and change 'ssl_verify_peer' to true to enable peer validation
        // 'ssl_cafile' => '... path to your Certificate Authority file ...',
        'ssl_verify_peer' => false
    ));
    $response = $request->send();
    if (200 != $response->getStatus()) {
        throw new Exception("List download URL returned status: " .
                            $response->getStatus() . ' ' . $response->getReasonPhrase());
    }
    $list     = $response->getBody();
    if (false === strpos($list, '// ===BEGIN ICANN DOMAINS===')) {
        throw new Exception("List download URL does not contain expected phrase");
    }
    if (!($fp = @fopen(OUTPUT_FILE, 'wt'))) {
        throw new Exception("Unable to open " . OUTPUT_FILE);
    }

} catch (Exception $e) {
    die($e->getMessage());
}

$tldTree = array();
$license = true;

fwrite($fp, "<?php\n");

foreach (array_filter(array_map('trim', explode("\n", $list))) as $line) {
    if ('//' != substr($line, 0, 2)) {
        buildSubdomain($tldTree, explode('.', $line));

    } elseif ($license) {
        if (0 === strpos($line, "// ===BEGIN ICANN DOMAINS===")) {
            fwrite($fp, "\n");
            $license = false;
        } else {
            fwrite($fp, $line . "\n");
        }
    }
}

writeNode($fp, $tldTree);
fwrite($fp, ";\n?>");
fclose($fp);
?>