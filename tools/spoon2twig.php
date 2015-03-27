<?php

// My Time Stamps trick
define('TIME', microtime(true));
function timestamp($i = null)
{
  return (float)substr(microtime(true) - TIME ,0,(int)$i+5) * 1000;
}
// our preg_match_all_and_sprinf_replace function
function preg_replace_sprintf($regex, $format, $filedata)
{
    preg_match_all($regex, $filedata, $match);
    if  (isset($match[3]))
    {
        $values = array();
        foreach ($match[1] as $key => $value)
        {
               $values[] = sprintf($format, $value, $match[2][$key], $match[3][$key]);
        }
        return str_replace($match[0], $values , $filedata);
    }
    elseif (isset($match[2]))
    {
        $values = array();
        foreach ($match[1] as $key => $value)
        {
            $values[] = sprintf($format, $value, $match[2][$key]);
        }
        return str_replace($match[0], $values , $filedata);
    }
    elseif (isset($match[1])) {
        $values = array();
        foreach ($match[1] as $value)
        {
            $values[] = sprintf($format, $value);
        }
        return str_replace($match[0], $values , $filedata);
    }
    else echo 'no match found on the ' . $regex . ' line';exit;
}
/** STRING CONVERSIONS START HERE **/
function convert($filedata)
{
    // 2x sometimes he skips an inner var in function

    // filters start
    $filedata = preg_replace_sprintf('/{\$now\|date:(.*?)}/i', '{{ now|date(%1$s) }}', $filedata);
    $filedata = preg_replace_sprintf('/\|date:(.*?):(.*?)}}/', '|spoon_date(%1$s, %2$s}})', $filedata);
    $filedata = preg_replace_sprintf('/\|sprintf:(.*?)}}/', '|sprintf(%s}})', $filedata);
    $filedata = preg_replace_sprintf('/\|getnavigation:(.*?):(.*?):(.*?)}/', '|getnavigation(%1$s, %2$s, %3$s)|raw}', $filedata);
    $filedata = preg_replace_sprintf('/\|getsubnavigation:(.*?):(.*?):(.*?)}/', '|getsubnavigation(%1$s, %2$s, %3$s)|raw }}', $filedata);

    $filedata = str_replace('/\|getmainnavigation}/', '|getmainnavigation|raw}', $filedata);
    $filedata = preg_replace_sprintf('/\|truncate:(.*?)}/', '|truncate(%1$s)}', $filedata);
    $filedata = preg_replace_sprintf('/\|geturl:(.*?):(.*?)}/', '|geturl(%1$s, %2$s)}', $filedata);
    $filedata = preg_replace_sprintf('/\|geturl:(.*?)}/', '|geturl(%1$s)}', $filedata);
    $filedata = str_replace('/Grid}/', 'Grid|raw}', $filedata);

    // filter endfor
    $filedata = preg_replace_sprintf('/{\$(.*?)\)}/', '{{ %s ) }}', $filedata);
    $filedata = preg_replace_sprintf('/{\$(.*?)}/', '{{ %s }}', $filedata); // for variables {$variable }

    $filedata = preg_replace_sprintf('/{\$([a-zA-Z0-9_|.]+)}/i', '{{ %s }}', $filedata); // 2x sometimes he skips an inner var in function

    // string replacers in the last part
    $filedata = str_replace('*', '#', $filedata); // comments
    $filedata = str_replace('*}', '#}', $filedata); // comments
    $filedata = str_replace('|ucfirst', '|capitalize', $filedata);
    $filedata = str_replace('.tpl', '.twig', $filedata);

    // raw
    $filedata = str_replace('siteHTMLHeader', 'siteHTMLHeader|raw', $filedata);
    $filedata = str_replace('siteHTMLFooter', 'siteHTMLFooter|raw', $filedata);
    $filedata = str_replace(' metaCustom ', ' metaCustom|raw ', $filedata);
    $filedata = str_replace(' meta ', ' meta|raw ', $filedata);
    $filedata = str_replace('blockContent', 'blockContent|raw', $filedata);

    // includes
    $filedata = preg_replace_sprintf('/{include:(.*)}/i', '{%% include "%s" %%}', $filedata); // for includes

    // operators
    $filedata = preg_replace_sprintf('/{option:!(.*?)}/i', '{%% if not %s %%}', $filedata);
    $filedata = preg_replace_sprintf('/{\/option:(.*?)}/i', '{%% endif %%}', $filedata); // for {option: variable }
    $filedata = preg_replace_sprintf('/{option:(.*?)}/i', '{%% if %s %%}', $filedata);
    $filedata = preg_replace_sprintf('/{\/iteration:(.*?)}/i', '{%% endfor %%}', $filedata); // for {option: variable }
    //$filedata = preg_replace_sprintf('/{iteration:(.*?)}/', '{%% set %sSpoonIter = %1$s %%}{%% for %1$s in %1$sSpoonIter %%}', $filedata);
    $filedata = preg_replace_sprintf('/{iteration:(.*?)}/', '{%% for %1$s in %1$s %%}', $filedata);

    //form values
    $filedata = preg_replace_sprintf('/{\/form:(.*?)}/i', '{%% endform %%}', $filedata); // for {form:add}
    $filedata = preg_replace_sprintf('/{form:(.*?)}/i', '{%% form %s %%}', $filedata);
    $filedata = preg_replace_sprintf('/{{ txt(.*?) }}/i', '{%% form_field %s %%}', $filedata);
    $filedata = preg_replace_sprintf('/{{ file(.*?) }}/i', '{%% form_field %s %%}', $filedata);
    $filedata = preg_replace_sprintf('/{{ ddm(.*?) }}/i', '{%% form_field %s %%}', $filedata);
    $filedata = preg_replace_sprintf('/{{ chk(.*?) }}/i', '{%% form_field %s %%}', $filedata);
    $filedata = preg_replace_sprintf('/form_field (.*?)Error/i', 'form_field_error %s', $filedata);

    $filedata = preg_replace_sprintf('/{{ lbl(.*?) }}/i', '{{ lbl.%s }}', $filedata);
    $filedata = preg_replace_sprintf('/{{ msg(.*?) }}/i', '{{ msg.%s }}', $filedata);
    $filedata = preg_replace_sprintf('/{{ err(.*?) }}/i', '{{ err.%s }}', $filedata);
    $filedata = preg_replace_sprintf('/{{ act(.*?) }}/i', '{{ act.%s }}', $filedata);

    return $filedata;
}
function write($input, $filedata)
{
    // OUR OUTPUT CODE
    $input = str_replace('.tpl', '.twig', $input);
    $file = realpath(dirname(__FILE__)) . '/' . $input;

    file_put_contents($file, $filedata);
    echo 'done in ' . timestamp(2) . ' milliseconds' . PHP_EOL;
}
function getFile($input)
{
    if (isFile($input)) {
        // grab file from command line parameter
        $file = realpath(dirname(__FILE__)) . '/' . $input;
        $stream = fopen($file, 'r');
        $filedata = stream_get_contents($stream);
        fclose($stream);
        return $filedata;
    }
}
function isFile($file)
{
    if (file_exists($file)) {
        return true;
    }
    exit('Could not open input file: ' . $file . PHP_EOL);
}

// OUR INPUT AND REPLACE CODE
if (!isset($argv[1])) exit('no arguments given ' . PHP_EOL);

$force = (isset($argv[2]) && $argv[2] == 'forced');

$input = (string) $argv[1];


if ($input === 'all')
{
    $BasePath = array('Frontend/Themes', 'Backend/Modules', 'Frontend/Modules', 'Frontend');
    $templates = array('/Layout/Templates', '/Layout/Widgets', '/Core/Layout/Templates');

    // checking what version
    $version = getFile('../VERSION.md');
    switch (true)
    {
        case (strpos($version, '3.9.') !== false):
            $source = '../src/';
            break;

        case (strpos($version, '3.8.') !== false):
            $source = '../src/';
            break;

        default:
            $source = '../src/';
            break;
    }

    // collects template Paths
    $excluded = array();
    $excludes = array('.', '..', '.DS_Store');
    $templatePaths = array();
    $root = realpath(dirname(__FILE__)) . '/' ;
    foreach ($BasePath as $BPath)
    {
        $possiblePath = $source . $BPath;
        if (is_dir($root . $possiblePath))
        {
            $tpls = array_diff(scandir($root . $possiblePath), $excludes);

            foreach ($tpls as $tpl)
            {
                if ($BPath == 'Frontend/Themes')
                {
                    $themeModule = $possiblePath . '/' . $tpl . '/Modules';
                    $tplsh = array_diff(scandir($themeModule), $excludes);
                    if ($tplsh)
                    {
                        foreach ($tplsh as $themeModuleName)
                        {
                            $templates[] = '/Modules/' . $themeModuleName . '/Layout/Templates';
                            $templates[] = '/Modules/' . $themeModuleName . '/Layout/Widgets';
                        }
                    }
                }
                foreach ($templates as $template)
                {
                    //var_dump($possiblePath, $template, $tpl);exit;
                    $possibletpl = $possiblePath . '/' . $tpl . $template;
                    if (is_dir($root . $possibletpl))
                    {
                        $tplsz = array_diff(scandir($root . $possibletpl), $excludes);
                        if (!empty($tplsz))
                        {
                            // append full path
                            foreach ($tplsz as $tpl_Z)
                            {
                                if (strpos($tpl_Z, '.tpl') !== false)
                                {
                                    $templatePaths[] = $possibletpl . '/' . $tpl_Z;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    if (!empty($templatePaths))
    {
        foreach ($templatePaths as $templatePath)
        {
            if ($force === true) {
                write($templatePath, convert(getFile($templatePath)));
            }
            else {
                if (!file_exists(str_replace('.tpl', '.twig', $templatePath)))
                {
                    write($templatePath, convert(getFile($templatePath)));
                }
                else {
                    $excluded[] = $templatePath;
                }
            }
        }
        if (!empty($excluded))
        {
            exit('not all files are converted, use forced to overwrite' . PHP_EOL);
        }
    }
}

if ($force === true) {
    write($input, convert(getFile($input)));
}
else {
    if (!isFile(str_replace('.tpl', '.twig', $input)))
    {
        write($input, convert(getFile($input)));
    }
    else exit('twig version of ' . $input . ' exists, use forced to overwrite' . PHP_EOL);
}
