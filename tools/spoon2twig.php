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
    else echo 'no match found on the ' . $regex . ' line';
}

/** STRING CONVERSIONS START HERE **/
function convert($filedata)
{

    // $filedata = preg_replace_sprintf('/:{\$(.*?)}/', ':{{ %s }}', $filedata); // for variables {$variable }
    // // 2x sometimes he skips an inner var in function

    $filedata = preg_replace_sprintf('/{\$([a-zA-Z0-9_|.]+)}/i', '{{ %s }}', $filedata); // 2x sometimes he skips an inner var in function

    // filter start
    $filedata = preg_replace_sprintf('/\|date:(.*?):(.*?)}}/', '|spoon_date(%1$s, %2$s}})', $filedata);
    $filedata = preg_replace_sprintf('/\|sprintf:(.*?)}}/', '|sprintf(%s}})', $filedata);
    $filedata = preg_replace_sprintf('/\|getnavigation:(.*?):(.*?):(.*?)}/', '|getnavigation(%1$s, %2$s, %3$s) | raw}', $filedata);

    // filter end
    $filedata = preg_replace_sprintf('/{\$(.*?)\)}/', '{{ %s ) }}', $filedata);

    // string replacers in the last part
    $filedata = str_replace('*', '#', $filedata); // comments
    $filedata = str_replace('*}', '#}', $filedata); // comments
    $filedata = str_replace('|ucfirst', '|capitalize', $filedata);
    $filedata = str_replace('.tpl', '.twig', $filedata);

    // includes
    $filedata = preg_replace_sprintf('/{include:(.*)}/i', '{%% include %s %%}', $filedata); // for includes

    // positions & for

    // operators
    $filedata = preg_replace_sprintf('/{option:!(.*?)}/i', '{%% if not %s %%}', $filedata);
    $filedata = preg_replace_sprintf('/{\/option:(.*?)}/i', '{%% endif %%}', $filedata); // for {option: variable }
    $filedata = preg_replace_sprintf('/{option:(.*?)}/i', '{%% if %s %%}', $filedata);
    $filedata = preg_replace_sprintf('/{\/iteration:(.*?)}/i', '{%% endfor %%}', $filedata); // for {option: variable }
    $filedata = preg_replace_sprintf('/{iteration:(.*?)}/i', '{%% for xxx in %s %%}', $filedata);

    //form values
    $filedata = preg_replace_sprintf('/{\/form:(.*?)}/i', '{{ form.%s.end }}', $filedata); // for {form:add}
    $filedata = preg_replace_sprintf('/{form:(.*?)}/i', '{{ form.%s.form }}', $filedata);
    $filedata = preg_replace_sprintf('/{{ txt(.*?) }}/i', '{{ form.xxx.txt.%s | raw }}', $filedata);
    $filedata = preg_replace_sprintf('/{{ file(.*?) }}/i', '{{ form.xxx.file.%s | raw }}', $filedata);
    $filedata = preg_replace_sprintf('/{{ ddm(.*?) }}/i', '{{ form.xxx.ddm.%s | raw }}', $filedata);
    $filedata = preg_replace_sprintf('/{{ chk(.*?) }}/i', '{{ form.xxx.chk.%s | raw }}', $filedata);

    $filedata = preg_replace_sprintf('/{{ lbl(.*?) }}/i', '{{ lbl.%s }}', $filedata);
    $filedata = preg_replace_sprintf('/{{ msg(.*?) }}/i', '{{ msg.%s }}', $filedata);
    $filedata = preg_replace_sprintf('/{{ err(.*?) }}/i', '{{ err.%s }}', $filedata);
    $filedata = preg_replace_sprintf('/{{ act(.*?) }}/i', '{{ act.%s }}', $filedata);

    // swap form.xxx to closest encounter of form.%s.form

    return $filedata;
}

function write($input, $filedata)
{
    // OUR OUTPUT CODE
    $input = str_replace('.tpl', '.twig', $input);
    $file = realpath(dirname(__FILE__)) . '/../' . $input;

    file_put_contents($file, $filedata);
    echo 'done in ' . timestamp(2) . ' milliseconds' . PHP_EOL;
}

// OUR INPUT AND REPLACE CODE
if (!isset($argv[1])) exit('no arguments given ' . PHP_EOL);

$input = (string) $argv[1];

$themePath = '../src/Frontend/Themes/';
$modulePath = '../src/Frontend/Modules/';

// grab file from command line parameter
$file = realpath(dirname(__FILE__)) . '/../' . $input;
if (file_exists($file))
{
    $stream = fopen($file, 'r');
    $filedata = stream_get_contents($stream);
    fclose($stream);
}
else exit('Could not open input file: ' . $input . PHP_EOL);

write($input, convert($filedata));
