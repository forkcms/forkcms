<?php

namespace Frontend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is our extended version of SpoonTemplateCompiler
 *
 * @author Matthias Mullie <forkcms@mullie.eu>
 */
class TemplateCompiler extends \SpoonTemplateCompiler
{
    /**
     * Parse the include tags.
     * This is an extended version of the Spoon Library template compiler, to allow for the passing of
     * a relative path to an include, which will then automatically choose the theme path (if available)
     * or the module's path.
     *
     * @param string $content The content that may contain the include tags.
     * @return string The updated content, containing the parsed include tags.
     */
    protected function parseIncludes($content)
    {
        // regex pattern
        // no unified restriction can be done on the allowed characters,
        // that differs from one OS to another
        // (see http://www.comentum.com/File-Systems-HFS-FAT-UFS.html)
        $pattern = '/\{include:(("[^"]*?"|\'[^\']*?\')|[^:]*?)\}/i';

        // find matches
        if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
            // loop matches
            foreach ($matches as $match) {
                // search string
                $search = $match[0];

                // inside a string
                if (in_array(substr($match[1], 0, 1), array('\'', '"'))) {
                    // strip quotes
                    $match[1] = substr($match[1], 1, -1);
                }

                $replace = '<?php $includes = array();
                ob_start();
                ?>' . $match[1] . '<?php
                $includes[] = str_replace(\'//\', \'/\', eval(\'return \\\'\' . str_replace(\'\\\'\', \'\\\\\\\'\', ob_get_clean()) .\'\\\';\'));
                ob_start();
                ?>' . $this->variables['THEME_PATH'] . '/' . $match[1] . '<?php
                $includes[] = str_replace(\'//\', \'/\', eval(\'return \\\'\' . str_replace(\'\\\'\', \'\\\\\\\'\', ob_get_clean()) .\'\\\';\'));
                ob_start();
                ?>' . $this->variables['FRONTEND_PATH'] . '/' . $match[1] . '<?php
                $includes[] = str_replace(\'//\', \'/\', eval(\'return \\\'\' . str_replace(\'\\\'\', \'\\\\\\\'\', ob_get_clean()) .\'\\\';\'));
                foreach($includes as $include) if(@file_exists($include) && is_file($include)) break;
                if($this->getForceCompile() || !file_exists($this->getCompileDirectory() .\'/\' . $this->getCompileName($include, \'' .
                           dirname(
                               realpath($this->template)
                           ) . '\'))) $this->compile(\'' . dirname(realpath($this->template)) . '\', $include);
                $return = @include $this->getCompileDirectory() .\'/\' . $this->getCompileName($include, \'' .
                           dirname(
                               realpath($this->template)
                           ) . '\');
                if($return === false && $this->compile(\'' . dirname(realpath($this->template)) . '\', $include)) {
                    $return = @include $this->getCompileDirectory() .\'/\' . $this->getCompileName($include, \'' .
                           dirname(
                               realpath($this->template)
                           ) . '\');
                }' . "\n";
                if (SPOON_DEBUG) {
                    $replace .= 'if($return === false) {
                    ?>' . $match[0] . '<?php
                }' . "\n";
                }
                $replace .= '?>';

                // replace it
                $content = str_replace($search, $replace, $content);
            }
        }

        return $content;
    }
}
