<?php namespace Wireshell\Helpers;

use Symfony\Component\Console\Helper\FormatterHelper as Formatter;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

/**
 * Class WsTools
 *
 * Contains common methods that could be used in every command
 *
 * @package Wireshell
 * @author Camilo Castro
 * @author Tabea David
 */

Class WsTools {

    protected $output;
    protected $formatter;

    protected static $types = array('error', 'success', 'info', 'comment', 
                                    'link', 'header', 'mark');

    public function __construct(OutputInterface $output, Formatter $formatter = null) {
        
        $this->output = $output;

        $this->formatter = $formatter;

        if (is_null($formatter) || !($formatter instanceof Formatter)) {
            $this->formatter = new Formatter();
        }


        $style = new OutputFormatterStyle('cyan', null, array('bold', 'underscore'));
        $output->getFormatter()->setStyle('success', $style);

        $style = new OutputFormatterStyle('magenta');
        $output->getFormatter()->setStyle('info', $style);

        $style = new OutputFormatterStyle('blue');
        $output->getFormatter()->setStyle('comment', $style);

        $style = new OutputFormatterStyle('magenta', null, array('underscore'));
        $output->getFormatter()->setStyle('link', $style);

        $style = new OutputFormatterStyle('cyan', null, array('reverse'));
        $output->getFormatter()->setStyle('header', $style);

        $style = new OutputFormatterStyle('blue', 'white', array('reverse'));
        $output->getFormatter()->setStyle('mark', $style);
    }


    /**
     * Simple method for coloring string
     * Possible Types: 
     * 'error', 'success', 'info', 'comment', 
     * 'link', 'header', 'mark'
     *
     * @param string $string
     * @param string $type
     * @return tinted string
     */
    public static function tint($string, $type = 'info') {

        if (in_array($type, self::$types)) {
            $string = "<{$type}>{$string}</{$type}>";  
        }

        return $string;
    }

    /**
     * Simple method for coloring output
     * Possible Types: error, info, comment, success, link
     *
     * @param string $string
     * @param string $type
     * @param boolean $write
     * @return tinted string
     */
    public function write($string, $type = 'info', $write = true) {

        $string = $this->tint($string, $type);

        if ($write) $this->output->writeln($string);

        return $string;
    }

    /**
     * Simple method for coloring link output
     *
     * @param string $string
     * @param boolean $write
     * @return tinted string
     */
    public function writeLink($string, $write = true) {
        return $this->write($string, 'link', $write);
    }

    /**
     * Simple method for coloring mark output
     *
     * @param string $string
     * @param boolean $write
     * @return tinted string
     */
    public function writeMark($string, $write = true) {
        return $this->write($string, 'mark', $write);
    }

    /**
     * Simple method for coloring header output
     *
     * @param string $string
     * @param boolean $write
     * @return tinted string
     */
    public function writeHeader($string, $write = true) {
        return $this->write(' ' . ucfirst($string) . ' ', 'header', $write);
    }

    /**
     * Simple method for coloring success output
     *
     * @param string $string
     * @param boolean $write
     * @return tinted string
     */
    public function writeSuccess($string, $write = true) {
        return $this->write($string, 'success', $write);
    }

    /**
     * Simple method for coloring error output
     *
     * @param string $string
     * @param boolean $write
     * @return tinted string
     */
    public function writeError($string, $write = true) {
        return $this->write($string, 'error', $write);
    }

    /**
     * Simple method for coloring comment output
     *
     * @param string $string
     * @param boolean $write
     * @return tinted string
     */
    public function writeComment($string, $write = true) {
        return $this->write($string, 'comment', $write);
    }

    /**
     * Simple method for coloring info output
     *
     * @param string $string
     * @param boolean $write
     * @return tinted string
     */
    public function writeInfo($string, $write = true) {
        return $this->write($string, 'info', $write);
    }

    /**
     * Simple method for listing output
     * one column
     *
     * @param string $header
     * @param array $items
     * @param OutputInterface $output
     */
    public function renderList($header, $items, $output) {
        $output->writeln('<fg=yellow;options=underscore>' . ucfirst($header) . "</>\n");

        if (count($items) > 0) {
            foreach ($items as $item) {
                $output->writeln(" - $item");
            }
        }

        $output->writeln("\n" . self::tint('(' . count($items) . ' in set)', 'comment'));
    }

    /**
     * Get question green text, white brackets/semicolon, yellow default
     *
     * @param string $question
     * @param string $default
     * @param string $sep
     * @return string
     */
    public function getQuestion($question, $default = null, $sep = ':') {
        $que = $this->tint($question, 'info');
        $def = ' [' . $this->tint($default, 'comment') . ']';

        return $default ? "{$que}{$def}{$sep} " : "{$que}{$sep} ";
    }

    /**
     * Write header section
     *
     * @param string $text
     * @param boolean $write
     */
    public function writeBlock($text, $write = true) {
        $out = $this->formatter->formatBlock($text, 'bg=blue;fg=white', true);
        if ($write) $this->output->writeln(array($out, ''));
        return $out;
    }

    /**
     * Write header section for comment
     *
     * @param string $text
     * @param boolean $write
     */
    public function writeBlockCommand($text, $write = true) {
        return $this->writeBlock(ucfirst($text), $write);
    }

    /**
     * Write header section
     *
     * @param string $section
     * @param string $text
     * @param boolean $write
     */
    public function writeSection($section, $text, $write = true) {
        $out = $this->formatter->formatSection($section, $text);
        if ($write) $this->output->writeln($out);
        return $out;
    }

    /**
     * Output new line / break
     *
     */
    public function nl() {
        $this->output->writeln('');
    }

    /**
     * Output new line / break
     * Alias of nl
     */
    public function writeNewline() {
        $this->nl();
    }

    /**
    * Output \r (carriage return) char in a new line
    */
    public function r() {
        $this->output->writeln("\r");
    }

    /**
    * Output \r (carriage return) char in a new line
    * Alias of r
    */
    public function writeCarriageReturn() {
        $this->r();
    }

}
