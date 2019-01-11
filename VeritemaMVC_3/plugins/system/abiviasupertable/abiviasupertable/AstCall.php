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

/**
 * Support for super table invokations from an article
 */
class AstCall {
    /**
     * Tag parser states.
     */
    const STATE_INTAG = 1;
    const STATE_SCAN = 0;
    
    /**
     *
     * @var JParameter Plugin parameters.
     */
    protected $_params;

    public $command;
    public $comment = false;
    
    /**
     * @var array Data set for this call. An array of column arrays of AstCell.
     */
    public $dataset;

    /**
     *
     * @var string Errors encountered during parsing.
     */
    public $error;
    
    /**
     * @var array Symbols defined by this invokation.
     */
    public $dictionary = array();
    public $nextOffset;
    public $size;
    public $offset;
    public $pattern = array();
    
    /**
     * @var string The plugin trigger word.
     */
    public $trigger;
    
    /**
     * @var boolean Set when an invokation has parsed correctly.
     */
    public $valid = false;

    function __construct($trigger, $command, $params = null) {
        $this -> trigger = $trigger;
        $this -> command = $command;
        $this -> _params = $params;
    }

    /**
     * Symbol define routine, designed to be chained.
     * 
     * @param array Symbol dictionary.
     * @param string Symbol being defined.
     * @param string Value of the symbol.
     */
    protected function _define(&$dictionary, $symbol, $value) {
        if ($symbol == 'rowheight') {
            // multiple values for rowid: rowheight rowid value
            if (!isset($dictionary['rowheight'])) {
                $dictionary['rowheight'] = array();
            }
            $index = self::_getWord($value);
            $dictionary['rowheight'][(int)$index[0]] = $index[1];
        } else {
            $dictionary[$symbol] = $value;
        }
    }

    /**
     * Get a word (contiguous non-blanks) from the start of a string.
     *
     * @param string The subject string.
     * @return array Three elements: the first word in lowercase, the remainder
     * of the string, the first word with case preserved.
     */
    static protected function _getWord($args) {
        if (($posn = strpos($args, ' ')) !== false) {
            $word = substr($args, 0, $posn);
            $args = trim(substr($args, $posn));
        } else {
            $word = $args;
            $args = '';
        }
        return array(strtolower($word), $args, $word);
    }

    protected function _parseArgs($args) {
        
    }

    /**
     * Find all the tags in the body of a calling tag and parse them as variable
     * definitions. Calls that don't define variables can override this function.
     *
     * @param string The body of a super table call.
     * @return string The body with definitions stripped out.
     */
    protected function _parseBody($body) {
        // Find all tags in the body
        preg_match_all(
            '#{\s*(?<comment>//)?\s*(?<args>.*?)}#is',
            $body,
            $defMatches,
            PREG_OFFSET_CAPTURE | PREG_SET_ORDER
        );
        // Normalize and validate syntax
        $this -> valid = true;
        foreach ($defMatches as $key => $match) {
            // Make sure the trigger word was delimited and that there are arguments.
            $args = $match['args'][AstArticle::REGEX_DATA];
            if (!strlen($args)) {
                // No arguments at all
                $this -> valid = false;
                break;
            }
            $args = ltrim($args);
            // Normalize whitespace
            $defMatches[$key]['args'][AstArticle::REGEX_DATA] = str_replace(
                array("\t", "\n", "\r"), ' ', $args
            );
        }
        if ($this -> valid) {
            $body = $this -> _parseDefs($this -> dictionary, $body, $defMatches);
        }
        return $body;
    }

    /*
     * Extract variable definitions in a text block into a variable dictionary.
     *
     * @param array The dictionary object.
     * @param string The text containing variable definitions.
     * @param array A list of definition regex matches
     * @return string The text with variables removed.
     */
    protected function _parseDefs(&$dictionary, $body, &$defMatches, $from = null, $to = null) {
        $kills = array();
        if ($from === null) {
            $from = 0;
        }
        if ($to === null) {
            $to = count($defMatches);
        }
        $inTag = '';
        $inTagStart = 0;
        for ($key = $from; $key < $to; ++$key) {
            $match = $defMatches[$key];
            $isComment = $match['comment'][AstArticle::REGEX_DATA] == '//';
            // Now extract the first set of nonblanks.
            list($symbol, $args) = self::_getWord($match['args'][AstArticle::REGEX_DATA]);
            // It's possible to use the trigger word to avoid plugin conflicts.
            if ($symbol == $this -> trigger) {
                list($symbol, $args) = self::_getWord($args);
            }
            if ($symbol == '') {
                continue;
            }
            // See if we have an opening tag
            if (substr($symbol, -1) == '/') {
                $inTag = substr($symbol, 0, -1);
                $inMatch = $match;
                // Scan for a closing tag that matches
                $closeAt = -1;
                for ($scan = $key + 1; $scan < count($defMatches); ++$scan) {
                    $closeMatch = &$defMatches[$scan];
                    $closeComment = $closeMatch['comment'][AstArticle::REGEX_DATA] == '//';
                    list($closeTag, $closeArgs) =
                        self::_getWord($closeMatch['args'][AstArticle::REGEX_DATA]);
                    if (!$closeComment && $closeTag == '/' . $inTag) {
                        $closeAt = $scan;
                        break;
                    }
                }
                if ($closeAt == -1) {
                    // No closing tag. There's pretty much nothing we can do to save this.
                    break;
                }
                $subDictionary = array();
                $body = $this -> _parseDefs($subDictionary, $body, $defMatches, $key + 1, $closeAt);
                $kills[] = array(
                    $inMatch[0][AstArticle::REGEX_OFFSET],
                    $closeMatch[0][AstArticle::REGEX_OFFSET]
                        + strlen($closeMatch[0][AstArticle::REGEX_DATA])
                        - $inMatch[0][AstArticle::REGEX_OFFSET]
                );
                if (!$isComment) {
                    $subDictionary['.args'] = $args;
                    if (!isset($subDictionary['text'])) {
                        $textStart = $inMatch[0][AstArticle::REGEX_OFFSET]
                            + strlen($inMatch[0][AstArticle::REGEX_DATA]);
                        $textSize = $defMatches[$closeAt][0][AstArticle::REGEX_OFFSET]
                            - $textStart;
                        if ($textSize) {
                            $subDictionary['text'] = substr(
                                $body, $textStart, $textSize
                            );
                        }
                    }
                    $this -> _define($dictionary, $inTag, $subDictionary);
                }
                $key = $closeAt;
            } elseif ($symbol[0] == '/') {
                // Ignore closing tag with no matching open
            } else {
                if (!$isComment) {
                    // Simple value inside braces case.
                    $this -> _define($dictionary, $symbol, $args);
                }
                $kills[] = array(
                    $match[0][AstArticle::REGEX_OFFSET],
                    strlen($match[0][AstArticle::REGEX_DATA])
                );
            }

        }
        // Remove the definitions from the text
        $kills = array_reverse($kills);
        $delta = 0;
        foreach ($kills as $kill) {
            $body = substr_replace($body, '', $kill[0], $kill[1]);
            $delta += $kill[1];
        }
        // Adjust remaining offsets
        if ($delta && $to > 0) {
            for ($key = $to - 1; $key < count($defMatches); ++$key) {
                foreach ($defMatches[$key] as &$pattern) {
                    $pattern[AstArticle::REGEX_OFFSET] -= $delta;
                }
            }
        }
        return $body;
    }

    protected function _parseStart($text) {
        return $text;
    }
    
    /**
     * Perform any functions required to turn the parsed data into a data set.
     */
    function execute() {
        // Typical to override this
    }

    /**
     * Parse a pattern reference.
     * 
     * @param array Match results from the scanner.
     * @param string The page text.
     * @return string Text after data extraction.
     */
    function parse($patternMatch, $body) {
        if ($patternMatch) {
            $this -> comment = $patternMatch['comment'][AstArticle::REGEX_DATA] == '//';
            if (isset($patternMatch['args'])) {
                $this -> _parseArgs($patternMatch['args'][AstArticle::REGEX_DATA]);
            }
            $this -> offset = $patternMatch[0][AstArticle::REGEX_OFFSET];
            $scan = $this -> offset + strlen($patternMatch[0][AstArticle::REGEX_DATA]);
            // If the block is commented, we're done
            if ($this -> comment) {
                return $this;
            }
        } else {
            $this -> comment = false;
        }
        // Give derived classes a chance to work on the body before we pull variables.
        $body = $this -> _parseStart($body);
        if ($body === false) {
            $this -> valid = false;
            return false;
        }
        $body = $this -> _parseBody($body);
        return $body;
    }

}

