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

require_once('AstCall.php');
require_once('AstCell.php');
require_once('AstXml.php');

/**
 * Argument parser for a global variable block.
 */
class AstCallTable extends AstCall {
    /**
     * Parsed table data.
     * @var DOMDocument
     */
    protected $_doc;
    
    /**
     * @var boolean Data is in row order
     */
    public $transpose = false;

    /**
     * Symbol define routine, designed to be chained.
     * 
     * @param array Symbol dictionary.
     * @param string Symbol being defined.
     * @param string Value of the symbol.
     */
    protected function _define(&$dictionary, $symbol, $value) {
        return parent::_define($dictionary, $symbol, $value);
    }

    protected function _parseArgs($args) {
        $args = trim($args);
        $refs = explode(' ', $args);
        foreach ($refs as $tid) {
            if ($tid == '') {
                continue;
            }
            if ($tid == 'transpose') {
                $this -> transpose = true;
            }
        }
    }

    protected function _parseStart($text) {
        // see if we can extract a table...
        if (!preg_match('#<table.*</table>#si', $text, $tableMatch, PREG_OFFSET_CAPTURE)) {
            $this -> error = 'Table not found.';
            return;
        }
        $text = substr_replace(
            $text, '',
            $tableMatch[0][AstArticle::REGEX_OFFSET],
            strlen($tableMatch[0][AstArticle::REGEX_DATA])
        );
        $xmlParse = new AstXml();
        $this -> _doc = $xmlParse -> processString($tableMatch[0][AstArticle::REGEX_DATA]);
        if (!$this -> _doc) {
            $this -> error = implode(chr(10), $xmlParse -> getErrors());
            return;
        }
        return $text;
    }

    /**
     * Extract cells from the DOM representation of the table set into a data set matrix.
     */
    function execute() {
        $xpath = new DOMXpath($this -> _doc);
        // Extract rows from the table
        $entries = $xpath -> query('//*/tr');
        $this -> dataset = array();
        for ($ind = 0; $ind < $entries -> length; ++$ind) {
            $row = $entries -> item($ind) -> childNodes;
            for ($col = -1, $item = 0; $item < $row -> length; ++$item) {
                $node = $row -> item($item);
                if (!$node instanceof DOMElement) {
                    continue;
                }
                ++$col;
                $cellInfo = new AstCell($this -> trigger, AstXml::extractXml($node));
                if ($this -> transpose) {
                    if (!isset($this -> dataset[$ind])) {
                        $this -> dataset[$ind] = array();
                    }
                    $this -> dataset[$ind][$col] = $cellInfo;
                } else {
                    if (!isset($this -> dataset[$col])) {
                        $this -> dataset[$col] = array();
                    }
                    $this -> dataset[$col][$ind] = $cellInfo;
                }
            }
        }
    }

}
