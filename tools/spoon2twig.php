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
    // filters start
    $filedata = preg_replace_sprintf('/\|date:(.*?):(.*?)}}/', '|spoon_date(%1$s, %2$s}})', $filedata);
    $filedata = preg_replace_sprintf('/\|sprintf:(.*?)}}/', '|sprintf(%s}})', $filedata);
    $filedata = preg_replace_sprintf('/\|getnavigation:(.*?):(.*?):(.*?)}/', '|getnavigation(%1$s, %2$s, %3$s)|raw}', $filedata);

    $filedata = str_replace('/\|getmainnavigation}/', '|getmainnavigation|raw}', $filedata);
    $filedata = preg_replace_sprintf('/|truncate:(.*?)}/', '|truncate(%1$s)}', $filedata);
    $filedata = preg_replace_sprintf('/|geturl:(.*?):(.*?)}/', '|geturl(%1$s, %2$s)}', $filedata);
    $filedata = preg_replace_sprintf('/|geturl:(.*?)}/', '|geturl(%1$s)}', $filedata);
    $filedata = str_replace('/Grid}/', 'Grid|raw}', $filedata);

    // filter endfor
    $filedata = preg_replace_sprintf('/{\$(.*?)\)}/', '{{ %s ) }}', $filedata);

    $filedata = preg_replace_sprintf('/:{\$(.*?)}/', ':{{ %s }}', $filedata); // for variables {$variable }
    // 2x sometimes he skips an inner var in function

    $filedata = preg_replace_sprintf('/{\$([a-zA-Z0-9_|.]+)}/i', '{{ %s }}', $filedata); // 2x sometimes he skips an inner var in function

    // string replacers in the last part
    $filedata = str_replace('*', '#', $filedata); // comments
    $filedata = str_replace('*}', '#}', $filedata); // comments
    $filedata = str_replace('|ucfirst', '|capitalize', $filedata);
    $filedata = str_replace('.tpl', '.twig', $filedata);

    // includes
    $filedata = preg_replace_sprintf('/{include:(.*)}/i', '{%% include "%s" %%}', $filedata); // for includes

    // operators
    $filedata = preg_replace_sprintf('/{option:!(.*?)}/i', '{%% if not %s %%}', $filedata);
    $filedata = preg_replace_sprintf('/{\/option:(.*?)}/i', '{%% endif %%}', $filedata); // for {option: variable }
    $filedata = preg_replace_sprintf('/{option:(.*?)}/i', '{%% if %s %%}', $filedata);
    $filedata = preg_replace_sprintf('/{\/iteration:(.*?)}/i', '{%% endfor %%}', $filedata); // for {option: variable }
    //$filedata = preg_replace_sprintf('/{iteration:(.*?)}/', '{%% set %sSpoonIter = %1$s %%}{%% for %1$s in %1$sSpoonIter %%}', $filedata);
    $filedata = preg_replace_sprintf('/{iteration:(.*?)}/', '{%% for xxx in %1$s %%}', $filedata);

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
    $BasePath = array('Backend/Modules', 'Frontend/Themes', 'Frontend/Modules');
    $templates = array('/Layout/Templates', '/Layout/Widgets');

    // checking what version
    $version = getFile('../VERSION.md');
    switch (true)
    {
        case (strpos($version, '3.9.') !== false):
            $source = '../src/';
            break;

        default:
            $source = '../src/';
            break;
    }

    // collects template Paths
    $templatePaths = array();
    foreach ($BasePath as $BPath)
    {
        $possiblePath = realpath(dirname(__FILE__)) . '/' . $source . $BPath;
        if (is_dir($possiblePath))
        {
            $tpls = array_diff(scandir($possiblePath), array('.', '..', '.DS_Store'));

            foreach ($tpls as $tpl)
            {
                foreach ($templates as $template)
                {
                    $possibletpl = $possiblePath . '/' . $tpl . $template;
                    if (is_dir($possibletpl))
                    {
                        $tplsz = array_diff(scandir($possibletpl), array('.', '..', '.DS_Store'));
                        if (!empty($tplsz))
                        {
                            // append full path
                            foreach ($tplsz as $tpl_Z)
                            {
                                $templatePaths[] = $possibletpl . '/' . $tpl_Z;
                            }
                        }
                    }
                }
            }
        }
    }
    if (!empty($templatePaths))
    {
        $errors = array();
        foreach ($templatePaths as $templatePath)
        {
            if ($force === true) {
                write($templatePath, convert(getFile($templatePath)));
            }
            else {
                if (!isFile(str_replace('.tpl', '.twig', $templatePath)))
                {
                    write($templatePath, convert(getFile($templatePath)));
                }
                else {
                    $errors[] = $templatePath;
                }
            }
        }
        if (!empty($errors))
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
