<?php
/**
 * Abivia Super Table Plugin.
 *
 * @package AbiviaSuperTable
 * @copyright (C) 2011 by Abivia Inc. All rights reserved.
 * @license GNU/GPL
 * @link http://www.abivia.net/
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');


class AstArticle {
    const REGEX_DATA = 0;
    const REGEX_OFFSET = 1;

    protected $_debug;

    protected $_debugText = '';

    /**
     * @var JParameter A copy of the plugin parameters.
     */
    protected $_params;

    /**
     * @var JPlugin The parent plugin instance.
     */
    protected $_parent;
    
    /**
     * @var array Performance tracking
     */
    protected $_profile = array();

    function __construct($parent, $params) {
        $this -> _params = $params;
        $this -> _parent = $parent;
    }
    
    /**
     * Format a style for appending to a list by makins sure there's a trailing semicolon.
     * 
     * @param string CSS style.
     * @return string 
     */
    protected function _appendStyle($style) {
        $style = trim($style);
        if ($style !== '' && substr($style, -1, 1) != ';') {
            $style .= ';';
        }
        return $style;
    }

    /**
     * Generate the table html
     *
     * @param object Parsed table data from the article.
     * @return string The pattern, populated with data.
     */
    protected function _generate($ref) {
        try {
            $this -> _parent -> addScriptFile($this -> _parent -> home . 'js/supertable.js');
            // Check the CSS strategy
            if (isset($ref -> dictionary['cssfile'])) {
                $cssFile = $ref -> dictionary['cssfile'];
            } else {
                $cssFile = '';
                switch ($this -> _params -> get('cssMode', 'none')) {
                    case 'custom': {
                        $cssFile = $this -> _params -> get('customFile', '');
                    }
                    break;
                    case 'preset': {
                        $cssFile =  $this -> _parent -> home . 'css/'
                            . $this -> _params -> get('presetFile', 'ahs')
                            . '/supertable.css';
                    }
                    break;
                }
            }
            if ($cssFile !== '') {
                $this -> _parent -> addCssFile($cssFile);
            }
            $borders = isset($ref -> dictionary['borders']) 
                && $ref -> dictionary['borders'];
            $headRows = isset($ref -> dictionary['headrows']) 
                ? (int) $ref -> dictionary['headrows'] : 0;
            $headCols = 0;
            $headColWidth = false;
            if (isset($ref -> dictionary['headcols'])) {
                $hcinfo = explode(' ', $ref -> dictionary['headcols']);
                $headCols = max((int) $hcinfo[0], 0);
                if (
                    $headCols && isset($hcinfo[1]) 
                    && preg_match('/[0-9]+(\.[0-9]*)?/', $hcinfo[1], $match)
                ) {
                    $headColWidth = $headCols * (float) $match[0];
                }
            }
            $nest = 0;
            $html = chr(10) . '<!-- ' . $this -> _parent -> name . ' '
                . $this -> _parent -> version . ' support: http://www.abivia.net -->' . chr(10)
                . str_repeat('  ', $nest++) . '<div class="wrapper">' . chr(10)
                . str_repeat('  ', $nest++) . '<div'
                . (
                    isset($ref -> dictionary['id'])
                    ? ' id="' . $ref -> dictionary['id'] . '"' : ''
                )
                . ' class="supertable'
                . (
                    isset($ref -> dictionary['class'])
                    ? ' ' . $ref -> dictionary['class'] : ''
                )
                . '"'
                . (
                    isset($ref -> dictionary['style'])
                    ? ' style="' . $ref -> dictionary['style'] . '"' : ''
                )
                .'>' . chr(10);
            if ($borders) {
                $html .= str_repeat('  ', $nest++) . '<div class="supertable-border">' . chr(10);
            }
            // Compute column Widths
            $cols = count($ref -> dataset);
            if ($cols) {
                $availWidth = $headColWidth === false ? 100 : 100 - $headColWidth;
                $dataCols = $cols - $headCols;
                if ($availWidth > 0) {
                    $colPct = round($availWidth / $dataCols, 2);
                    $firstColPct = ($availWidth - ($dataCols - 1) * $colPct) . '%';
                    $colPct .= '%';
                    if ($headCols) {
                        $headColWidth = round($headColWidth / $headCols, 2) . '%';
                    } else {
                        $headColWidth = '';
                    }
                } else {
                    $firstColPct = '';
                    $colPct = '';
                    $headColWidth = '';
                }
                if (isset($ref -> dictionary['active'])) {
                    // User specifies this origin 1, we convert to origin 0.
                    $activeCol = ((int) $ref -> dictionary['active']) - 1;
                    if ($activeCol < 0 || $activeCol >= $dataCols) {
                        $activeCol = -1;
                    }
                } else {
                    // Default is no active column
                    $activeCol = -1;
                }
                /*
                 * Step through each column
                 */
                foreach ($ref -> dataset as $col => $dataRows) {
                    $html .= str_repeat('  ', $nest++) . '<div class="supertable-col';
                    if ($col == 0) {
                        $html .= ' supertable-col-first';
                    } 
                    if ($col == $cols - 1) {
                        $html .= ' supertable-col-last';
                    }
                    if ($col < $headCols) {
                        $html .= ' supertable-col-rowhead';
                        $thisColPct = $headColWidth;
                        $dataCol = -1;
                    } else {
                        $dataCol = $col - $headCols;
                        $thisColPct = ($dataCol == 0 ? $firstColPct : $colPct);
                        $html .= ' supertable-col-' . ($dataCol & 1 ? 'even' : 'odd');
                        if ($dataCol == $activeCol) {
                            $html .= ' supertable-active';
                        }
                    }
                    $html .= '"';
                    if ($thisColPct) {
                        $html .= ' style="width:' . $thisColPct . ';"';
                    }
                    $html .= '>' . chr(10);
                    if ($borders) {
                        $html .= str_repeat('  ', $nest++) . '<div class="supertable-col-border">' . chr(10);
                    }
                    /*
                     * Step through each row in the column
                     */
                    $lastRow = count($dataRows) - 1;
                    foreach ($dataRows as $row => $cell) {
                        $html .= str_repeat('  ', $nest++) . '<div';
                        if (isset($cell -> dictionary['id'])) {
                            $html .= ' id="' . $cell -> dictionary['id'] . '"';
                        }
                        $html .= ' class="supertable-cell';
                        if ($row == 0) {
                            $html .= ' supertable-row-first';
                        } 
                        if ($row == $lastRow) {
                            $html .= ' supertable-row-last';
                        }
                        if ($row < $headRows) {
                            // col-head obviously wrong but we'll retain it for a while
                            $html .= ' supertable-col-head';
                            $html .= ' supertable-row-head';
                            $html .= ' supertable-row-head-' . ($row & 1 ? 'even' : 'odd');
                            $html .= ' supertable-row-head-' . ($row + 1);
                        } else {
                            $dataRow = $row - $headRows;
                            $html .= ' supertable-row-' . ($dataRow & 1 ? 'even' : 'odd');
                            $html .= ' supertable-row-' . ($dataRow + 1);
                        }
                        if (isset($cell -> dictionary['class'])) {
                            $cellClass = trim($cell -> dictionary['class']);
                            if ($cellClass != '') {
                                $html .= ' ' . $cellClass;
                            }
                        }
                        $html .= '"';
                        $cellStyle = '';
                        if (isset($cell -> dictionary['style'])) {
                            $cellStyle .= $this -> _appendStyle($cell -> dictionary['style']);
                        }
                        if (
                            isset($ref -> dictionary['rowheight']) 
                            && isset($ref -> dictionary['rowheight'][$row + 1])
                        ) {
                            $cellStyle .= $this -> _appendStyle(
                                ' height:' . $ref -> dictionary['rowheight'][$row + 1]
                            );
                        }
                        if (trim($cellStyle) != '') {
                            $html .= ' style="' . trim($cellStyle) . '"';
                        }
                        $html .= '>' . chr(10);
                        $html .= str_repeat('  ', $nest++) . '<div class="supertable-cell-inner">' . chr(10);
                        if (isset($cell -> dictionary['link'])) {
                            $linkPre = '<a href="' 
                                . $cell -> dictionary['link']
                                . '"';
                            if (isset($cell -> dictionary['target'])) {
                                $linkPre .= ' target="' . $cell -> dictionary['target'] . '"';
                            }
                            $linkPre .= '>';
                            $linkPost = '</a>';
                        } else {
                            $linkPre = '';
                            $linkPost = '';
                        }
                        $html .= str_repeat('  ', $nest) . '<div class="supertable-cell-text">'
                            . $linkPre
                            . (
                                isset($cell -> dictionary['text'])
                                ? $cell -> dictionary['text'] : '&nbsp;'
                            )
                            . $linkPost . '</div>' . chr(10);
                        if (isset($cell -> dictionary['subtext'])) {
                            $html .= str_repeat('  ', $nest) . '<div class="supertable-cell-subtext">'
                                . $linkPre
                                . $cell -> dictionary['subtext']
                                . $linkPost . '</div>' . chr(10);
                        }
                        // Inner cell
                        $html .= str_repeat('  ', --$nest) . '</div>' . chr(10);
                        // Cell
                        $html .= str_repeat('  ', --$nest) . '</div>' . chr(10);
                    }
                    // column border
                    if ($borders) {
                        $html .= str_repeat('  ', --$nest) . '</div>' . chr(10);
                    }
                    // Column
                    $html .= str_repeat('  ', --$nest) . '</div>' . chr(10);
                }
            }
            $html .= '<div class="supertable-clear"> </div>' . chr(10);
            if ($borders) {
                $html .= str_repeat('  ', --$nest) . '</div>' . chr(10);
            }
            // Supertable & wrapper
            $html .= str_repeat('  ', --$nest) . '</div>' . chr(10);
            $html .= str_repeat('  ', --$nest) . '</div>' . chr(10);
            if (isset($ref -> dictionary['clear']) && $ref -> dictionary['clear']) {
                $html .= '<div class="supertable-clear"> </div>' . chr(10);
            }
            $html .= '<!-- ' . $this -> _parent -> name . ' end -->' . chr(10);
        } catch (Exception $e) {
            //echo $e;
            if ($this -> _debug) {
                $from = strlen(dirname(dirname(__FILE__)));
                $this -> _debugText .= 'Exception: ' . $e -> getMessage() 
                    . ($this -> _debug > 2 
                        ? ' ' . substr($e -> getFile(), $from) . ':' . $e -> getLine()
                        : ''
                    )
                    . chr(10);
            }
        }
        return $html;
    }

    /**
     * Get text generated by the debug system.
     *
     * @return string
     */
    function getDebugText() {
        return $this -> _debugText;
    }

    /**
     * Parse and evaluate pattern references in an article body.
     *
     * @param string Trigger keyword for pattern expressions.
     * @param string Article body
     * @return array Matched pattern references. Each element is an object.
     * Members of the object are:
     * comment - A boolean set if the reference is marked as a comment.
     * defs - An array of definitions in the reference, indexed by name.
     * error - False if no error, string if a problem found.
     * name - Pattern reference name, if provided.
     * offset - Offset to the start of the pattern expression.
     * size - The number of characters in the pattern expression.
     * pattern - the text containing the pattern reference.
     */
    function getPatternRefs($trigger, $text) {
        $commandRegex = '#{\s*(?<comment>//)?\s*(?<trigger>' . $trigger . ')'
            . '/?\s+(?<command>[a-z][a-z0-9]*)(\s+(?<args>.*?))?}#is';
        $endRegex = '#{\s*/\s*' . $trigger . '(\s+(?<args>.*?))?}#is';
        preg_match_all(
            $commandRegex, $text, $patternMatches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE
        );
        $refs = array();
        if (empty($patternMatches)) {
            return $refs;
        }
        $endLimit = strlen($text);
        $patternMatches = array_reverse($patternMatches);
        foreach ($patternMatches as $posn => &$patternMatch) {
            // Scan from the end of the opening tag to find a close tag
            if (!preg_match(
                $endRegex, $text, $endMatch, PREG_OFFSET_CAPTURE, $patternMatch[0][1]
            )) {
                // No match, ignore this
                $endLimit = $patternMatches[$posn];
                continue;
            }
            if ($endMatch[0][self::REGEX_OFFSET] > $endLimit) {
                // End tag is later than start next tag. Ignore.
                $endLimit = $patternMatches[$posn];
                continue;                
            }
            // We have a well-formed supertable
            $bodyStart = $patternMatch[0][self::REGEX_OFFSET] 
                + strlen($patternMatch[0][self::REGEX_DATA]);
            $body = substr(
                $text, 
                $bodyStart,
                $endMatch[0][self::REGEX_OFFSET] - $bodyStart
            );
            $command = strtolower($patternMatch['command'][self::REGEX_DATA]);
            $astClass = 'AstCall' . ucfirst($command);
            @include_once($astClass . '.php');
            if (class_exists($astClass)) {
                $ref = new $astClass($trigger, $command, $this -> _params);
                $ref -> parse($patternMatch, $body);
                $ref -> offset = $patternMatch[0][self::REGEX_OFFSET];
                $ref -> size = $endMatch[0][self::REGEX_OFFSET]
                    + strlen($endMatch[0][self::REGEX_DATA]) - $ref -> offset;
            } else {
                $ref = null;
            }
            if (!$ref || !$ref -> valid) {
                // Body failed to parse
                $endLimit = $patternMatches[$posn];
                continue;
            }
            $refs[] = $ref;
        }
        return $refs;
    }

    /**
     * Process an article for supertable references, then render.
     *
     * @param array The trigger string for the article.
     * @param string The article text.
     * @return stdClass Object containing the resolved text and the acpData.
     */
    function process($triggers, $text) {
        $this -> _profile[-1] = array('start' => microtime(true));
        $this -> _debugText = '';
        // Get plugin info
        $unwrapBlocked = false;
        $result = new stdClass();
        // Look for a pattern reference
        $refs = $this -> getPatternRefs($triggers['article'], $text);
        $this -> _profile[-1]['exec'] = microtime(true);
        if ($this -> _debug >= 2) {
            $this -> _debugText .= count($refs) . ' supertable references found.' . chr(10);
        }
        $result -> astData = $refs;
        foreach ($refs as $key => $ref) {
            $this -> _profile[$key] = array('start' => microtime(true));
            $offset = $ref -> offset;
            if ($this -> _debug) {
                $this -> _debugText .= 'Ref at offset ' . $offset . chr(10);
            }
            $size = $ref -> size;
            if ($this -> _debug >= 2) {
                $this -> _debugText .= print_r($ref, true);
            }
            $html = '';
            if ($ref -> valid) {
                $ref -> execute();
            }
            $this -> _profile[$key]['exec'] = microtime(true);
            if ($ref -> valid) {
                $html = $this -> _generate($ref);
            } elseif ($this -> _debug == 1) {
                $this -> _debugText .= $ref -> error;
            }
            $this -> _profile[$key]['gen'] = microtime(true);
            if (!$unwrapBlocked) {
                // Expand the scope to include a containing HTML element
                if (($scan = strpos($text, '>', $offset + $size)) === false) {
                    $scan = $offset + $size - 1;
                }
                $uoffset = $offset;
                while ($uoffset >= 0 && $text[$uoffset] != '<') {
                    --$uoffset;
                }
                // We need to have found something, and it can't be a closing HTML tag.
                if ($uoffset >= 0 && $text[$uoffset + 1] != '/') {
                    $offset = $uoffset;
                }
                $size = $scan - $offset + 1;
                if ($this -> _debug) {
                    $this -> _debugText .= 'Unwrap: scan ' . $scan
                        . ' offset ' . $ref -> offset
                        . '=>' . $offset . ' size:' . $ref -> size
                        . '=>' . $size . chr(10);
                }
            }
            // Replace the data block with the new code
            $text = substr_replace($text, $html, $offset, $size);
        }
        $this -> _profile[-1]['gen'] = microtime(true);
        if ($this -> _debug) {
            foreach ($this -> _profile as $key => $timing) {
                $this -> _debugText .= ($key == -1 ? 'all  ' : 'ref ' . $key) 
                    . ' exec ' . sprintf('%.5f', $timing['exec'] - $timing['start'])
                    . ' gen ' . sprintf('%.5f', $timing['gen'] - $timing['exec'])
                    . ' tot ' . sprintf('%.5f', $timing['gen'] - $timing['start'])
                    . chr(10);
            }
        }
        $result -> debugText = $this -> _debugText
            ? '<!-- supertable debug output' . chr(10)
            . preg_replace(
                array('/\-\-+/', '/\{\s*password.*?\}/i'),
                array('-', '{password *****}'),
                $this -> _debugText
            ) . chr(10)
            . 'end supertable debug output -->' . chr(10)
            : ''
        ;
        $result -> text = $text;
        return $result;
    }

    function setDebug($level) {
        $this -> _debug = $level;
    }

}