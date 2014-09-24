<?php
/**
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * o Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * o Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 * o The names of the authors may not be used to endorse or promote products
 *   derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Console
 * @package   Console_Table
 * @author    Richard Heyes <richard@phpguru.org>
 * @author    Jan Schneider <jan@horde.org>
 * @copyright 2002-2005 Richard Heyes
 * @copyright 2006-2008 Jan Schneider
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/Console_Table
 */

/**
 * The main class.
 *
 * @category Console
 * @package  Console_Table
 * @author   Jan Schneider <jan@horde.org>
 * @license  http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link     http://pear.php.net/package/Console_Table
 */

namespace Aura\Cli;

use Aura\Cli\Stdio\Formatter;

define('CONSOLE_TABLE_HORIZONTAL_RULE', 1);
define('CONSOLE_TABLE_ALIGN_LEFT', -1);
define('CONSOLE_TABLE_ALIGN_CENTER', 0);
define('CONSOLE_TABLE_ALIGN_RIGHT', 1);
define('CONSOLE_TABLE_BORDER_ASCII', -1);

class Table
{
    /**
     * The table headers.
     *
     * @var array
     */
    protected $headers = array();

    /**
     * The data of the table.
     *
     * @var array
     */
    protected $data = array();

    /**
     * The maximum number of columns in a row.
     *
     * @var integer
     */
    protected $max_cols = 0;

    /**
     * The maximum number of rows in the table.
     *
     * @var integer
     */
    protected $max_rows = 0;

    /**
     * Lengths of the columns, calculated when rows are added to the table.
     *
     * @var array
     */
    protected $cell_lengths = array();

    /**
     * Heights of the rows.
     *
     * @var array
     */
    protected $row_heights = array();

    /**
     * How many spaces to use to pad the table.
     *
     * @var integer
     */
    protected $padding = 1;

    /**
     * Column filters.
     *
     * @var array
     */
    protected $filters = array();

    /**
     * Columns to calculate totals for.
     *
     * @var array
     */
    protected $calculate_totals;

    /**
     * Alignment of the columns.
     *
     * @var array
     */
    protected $col_align = array();

    /**
     * Default alignment of columns.
     *
     * @var integer
     */
    protected $default_align;

    /**
     * Character set of the data.
     *
     * @var string
     */
    protected $charset = 'utf-8';

    /**
     * Border characters.
     * Allowed keys:
     * - intersection - intersection ("+")
     * - horizontal - horizontal rule character ("-")
     * - vertical - vertical rule character ("|")
     *
     * @var array
     */
    protected $border = array(
        'intersection' => '+',
        'horizontal' => '-',
        'vertical' => '|',
    );

    /**
     * If borders are shown or not
     * Allowed keys: top, right, bottom, left, inner: true and false
     *
     * @var array
     */
    protected $border_visibility = array(
        'top'    => true,
        'right'  => true,
        'bottom' => true,
        'left'   => true,
        'inner'  => true
    );


    protected $formatter;

    /**
     * Constructor.
     *
     * @param integer $align   Default alignment. One of
     *                         CONSOLE_TABLE_ALIGN_LEFT,
     *                         CONSOLE_TABLE_ALIGN_CENTER or
     *                         CONSOLE_TABLE_ALIGN_RIGHT.
     * @param string  $border  The character used for table borders or
     *                         CONSOLE_TABLE_BORDER_ASCII.
     * @param integer $padding How many spaces to use to pad the table.
     * @param string  $charset A charset supported by the mbstring PHP
     *                         extension.
     */
    public function __construct(
        Formatter $formatter,
        $align = CONSOLE_TABLE_ALIGN_LEFT,
        $border = CONSOLE_TABLE_BORDER_ASCII,
        $padding = 1,
        $charset = null
    ) {
        $this->default_align = $align;
        $this->setBorder($border);
        $this->padding      = $padding;
        if (!empty($charset)) {
            $this->setCharset($charset);
        }
        $this->formatter = $formatter;
    }

    /**
     * Converts an array to a table.
     *
     * @param array   $headers      Headers for the table.
     * @param array   $data         A two dimensional array with the table
     *                              data.
     * @param boolean $returnObject Whether to return the Console_Table object
     *                              instead of the rendered table.
     *
     * @static
     *
     * @return Console_Table|string  A Console_Table object or the generated
     *                               table.
     */
    public static function fromArray($headers, $data)
    {
        if (!is_array($headers) || !is_array($data)) {
            return false;
        }

        $instance = new static();
        $instance->setHeaders($headers);

        foreach ($data as $row) {
            $instance->addRow($row);
        }

        return $instance;
    }

    /**
     * Adds a filter to a column.
     *
     * Filters are standard PHP callbacks which are run on the data before
     * table generation is performed. Filters are applied in the order they
     * are added. The callback function must accept a single argument, which
     * is a single table cell.
     *
     * @param integer $col       Column to apply filter to.
     * @param mixed   &$callback PHP callback to apply.
     *
     * @return void
     */
    public function addFilter($col, &$callback)
    {
        $this->filters[] = array($col, &$callback);
    }

    /**
     * Sets the charset of the provided table data.
     *
     * @param string $charset A charset supported by the mbstring PHP
     *                        extension.
     *
     * @return void
     */
    public function setCharset($charset)
    {
        $locale = setlocale(LC_CTYPE, 0);
        setlocale(LC_CTYPE, 'en_US');
        $this->charset = strtolower($charset);
        setlocale(LC_CTYPE, $locale);
    }

    /**
     * Set the table border settings
     *
     * Border definition modes:
     * - CONSOLE_TABLE_BORDER_ASCII: Default border with +, - and |
     * - array with keys "intersection", "horizontal" and "vertical"
     * - single character string that sets all three of the array keys
     *
     * @param mixed $border Border definition
     *
     * @return void
     * @see $border
     */
    public function setBorder($border)
    {
        if ($border === CONSOLE_TABLE_BORDER_ASCII) {
            $intersection = '+';
            $horizontal = '-';
            $vertical = '|';
        } else if (is_string($border)) {
            $intersection = $horizontal = $vertical = $border;
        } else if ($border == '') {
            $intersection = $horizontal = $vertical = '';
        } else {
            extract($border);
        }

        $this->border = array(
            'intersection' => $intersection,
            'horizontal' => $horizontal,
            'vertical' => $vertical,
        );
    }

    /**
     * Set which borders shall be shown.
     *
     * @param array $visibility Visibility settings.
     *                          Allowed keys: left, right, top, bottom, inner
     *
     * @return void
     * @see    $borderVisibility
     */
    public function setBorderVisibility($visibility)
    {
        $this->border_visibility = array_merge(
            $this->border_visibility,
            array_intersect_key(
                $visibility,
                $this->border_visibility
            )
        );
    }

    /**
     * Sets the alignment for the columns.
     *
     * @param integer $col_id The column number.
     * @param integer $align  Alignment to set for this column. One of
     *                        CONSOLE_TABLE_ALIGN_LEFT
     *                        CONSOLE_TABLE_ALIGN_CENTER
     *                        CONSOLE_TABLE_ALIGN_RIGHT.
     *
     * @return void
     */
    public function setAlign($col_id, $align = CONSOLE_TABLE_ALIGN_LEFT)
    {
        switch ($align) {
            case CONSOLE_TABLE_ALIGN_CENTER:
                $pad = STR_PAD_BOTH;
                break;
            case CONSOLE_TABLE_ALIGN_RIGHT:
                $pad = STR_PAD_LEFT;
                break;
            default:
                $pad = STR_PAD_RIGHT;
                break;
        }
        $this->col_align[$col_id] = $pad;
    }

    /**
     * Specifies which columns are to have totals calculated for them and
     * added as a new row at the bottom.
     *
     * @param array $cols Array of column numbers (starting with 0).
     *
     * @return void
     */
    public function calculateTotalsFor($cols)
    {
        $this->calculate_totals = $cols;
    }

    /**
     * Sets the headers for the columns.
     *
     * @param array $headers The column headers.
     *
     * @return void
     */
    public function setHeaders($headers)
    {
        $this->headers = array(array_values($headers));
        $this->updateRowsCols($headers);
    }

    /**
     * Adds a row to the table.
     *
     * @param array   $row    The row data to add.
     * @param boolean $append Whether to append or prepend the row.
     *
     * @return void
     */
    public function addRow($row, $append = true)
    {
        if ($append) {
            $this->data[] = array_values($row);
        } else {
            array_unshift($this->data, array_values($row));
        }

        $this->updateRowsCols($row);
    }

    /**
     * Inserts a row after a given row number in the table.
     *
     * If $row_id is not given it will prepend the row.
     *
     * @param array   $row    The data to insert.
     * @param integer $row_id Row number to insert before.
     *
     * @return void
     */
    public function insertRow($row, $row_id = 0)
    {
        array_splice($this->data, $row_id, 0, array($row));

        $this->updateRowsCols($row);
    }

    /**
     * Adds a column to the table.
     *
     * @param array   $col_data The data of the column.
     * @param integer $col_id   The column index to populate.
     * @param integer $row_id   If starting row is not zero, specify it here.
     *
     * @return void
     */
    public function addCol($col_data, $col_id = 0, $row_id = 0)
    {
        foreach ($col_data as $col_cell) {
            $this->data[$row_id++][$col_id] = $col_cell;
        }

        $this->updateRowsCols();
        $this->max_cols = max($this->max_cols, $col_id + 1);
    }

    /**
     * Adds data to the table.
     *
     * @param array   $data   A two dimensional array with the table data.
     * @param integer $col_id Starting column number.
     * @param integer $row_id Starting row number.
     *
     * @return void
     */
    public function addData($data, $col_id = 0, $row_id = 0)
    {
        foreach ($data as $row) {
            if ($row === CONSOLE_TABLE_HORIZONTAL_RULE) {
                $this->data[$row_id] = CONSOLE_TABLE_HORIZONTAL_RULE;
                $row_id++;
                continue;
            }
            $starting_col = $col_id;
            foreach ($row as $cell) {
                $this->data[$row_id][$starting_col++] = $cell;
            }
            $this->updateRowsCols();
            $this->max_cols = max($this->max_cols, $starting_col);
            $row_id++;
        }
    }

    /**
     * Adds a horizontal seperator to the table.
     *
     * @return void
     */
    public function addSeparator()
    {
        $this->data[] = CONSOLE_TABLE_HORIZONTAL_RULE;
    }

    /**
     * Returns the generated table.
     *
     * @return string  The generated table.
     */
    public function getTable()
    {
        $this->applyFilters();
        $this->calculateTotals();
        $this->validateTable();

        return $this->buildTable();
    }

    /**
     * Returns the generated table.
     *
     * @return string  The generated table.
     */
    public function __toString()
    {
        return $this->getTable();
    }

    /**
     * Calculates totals for columns.
     *
     * @return void
     */
    protected function calculateTotals()
    {
        if (empty($this->calculate_totals)) {
            return;
        }

        $this->addSeparator();

        $totals = array();
        foreach ($this->data as $row) {
            if (is_array($row)) {
                foreach ($this->calculate_totals as $column_id) {
                    $totals[$column_id] += $row[$column_id];
                }
            }
        }

        $this->data[] = $totals;
        $this->updateRowsCols();
    }

    /**
     * Applies any column filters to the data.
     *
     * @return void
     */
    protected function applyFilters()
    {
        if (empty($this->filters)) {
            return;
        }

        foreach ($this->filters as $filter) {
            $column   = $filter[0];
            $callback = $filter[1];

            foreach ($this->data as $row_id => $row_data) {
                if ($row_data !== CONSOLE_TABLE_HORIZONTAL_RULE) {
                    $this->data[$row_id][$column] =
                        call_user_func($callback, $row_data[$column]);
                }
            }
        }
    }

    /**
     * Ensures that column and row counts are correct.
     *
     * @return void
     */
    protected function validateTable()
    {
        if (!empty($this->headers)) {
            $this->calculateRowHeight(-1, $this->headers[0]);
        }

        for ($i = 0; $i < $this->max_rows; $i++) {
            for ($j = 0; $j < $this->max_cols; $j++) {
                if (!isset($this->data[$i][$j]) &&
                    (!isset($this->data[$i]) ||
                     $this->data[$i] !== CONSOLE_TABLE_HORIZONTAL_RULE)) {
                    $this->data[$i][$j] = '';
                }

            }
            $this->calculateRowHeight($i, $this->data[$i]);

            if ($this->data[$i] !== CONSOLE_TABLE_HORIZONTAL_RULE) {
                 ksort($this->data[$i]);
            }

        }

        $this->splitMultilineRows();

        // Update cell lengths.
        for ($i = 0; $i < count($this->headers); $i++) {
            $this->calculateCellLengths($this->headers[$i]);
        }
        for ($i = 0; $i < $this->max_rows; $i++) {
            $this->calculateCellLengths($this->data[$i]);
        }

        ksort($this->data);
    }

    /**
     * Splits multiline rows into many smaller one-line rows.
     *
     * @return void
     */
    protected function splitMultilineRows()
    {
        ksort($this->data);
        $sections          = array(&$this->headers, &$this->data);
        $max_rows          = array(count($this->headers), $this->max_rows);
        $row_height_offset = array(-1, 0);

        for ($s = 0; $s <= 1; $s++) {
            $inserted = 0;
            $new_data = $sections[$s];

            for ($i = 0; $i < $max_rows[$s]; $i++) {
                // Process only rows that have many lines.
                $height = $this->row_heights[$i + $row_height_offset[$s]];
                if ($height > 1) {
                    // Split column data into one-liners.
                    $split = array();
                    for ($j = 0; $j < $this->max_cols; $j++) {
                        $split[$j] = preg_split('/\r?\n|\r/',
                                                $sections[$s][$i][$j]);
                    }

                    $new_rows = array();
                    // Construct new 'virtual' rows - insert empty strings for
                    // columns that have less lines that the highest one.
                    for ($i2 = 0; $i2 < $height; $i2++) {
                        for ($j = 0; $j < $this->max_cols; $j++) {
                            $new_rows[$i2][$j] = !isset($split[$j][$i2])
                                ? ''
                                : $split[$j][$i2];
                        }
                    }

                    // Replace current row with smaller rows.  $inserted is
                    // used to take account of bigger array because of already
                    // inserted rows.
                    array_splice($new_data, $i + $inserted, 1, $new_rows);
                    $inserted += count($new_rows) - 1;
                }
            }

            // Has the data been modified?
            if ($inserted > 0) {
                $sections[$s] = $new_data;
                $this->updateRowsCols();
            }
        }
    }

    /**
     * Builds the table.
     *
     * @return string  The generated table string.
     */
    protected function buildTable()
    {
        if (!count($this->data)) {
            return '';
        }

        $vertical = $this->border['vertical'];
        $separator = $this->getSeparator();

        $return = array();
        for ($i = 0; $i < count($this->data); $i++) {
            for ($j = 0; $j < count($this->data[$i]); $j++) {
                if ($this->data[$i] !== CONSOLE_TABLE_HORIZONTAL_RULE &&
                    $this->getStringLength($this->data[$i][$j]) <
                    $this->cell_lengths[$j]) {
                    $this->data[$i][$j] = $this->stringPad($this->data[$i][$j],
                                                          $this->cell_lengths[$j],
                                                          ' ',
                                                          $this->col_align[$j]);
                }
            }

            if ($this->data[$i] !== CONSOLE_TABLE_HORIZONTAL_RULE) {
                $row_begin = $this->border_visibility['left']
                    ? $vertical . str_repeat(' ', $this->padding)
                    : '';
                $row_end = $this->border_visibility['right']
                    ? str_repeat(' ', $this->padding) . $vertical
                    : '';
                $implode_char = str_repeat(' ', $this->padding) . $vertical
                    . str_repeat(' ', $this->padding);
                $return[]     = $row_begin
                    . implode($implode_char, $this->data[$i]) . $row_end;
            } elseif (!empty($separator)) {
                $return[] = $separator;
            }

        }

        $return = implode(PHP_EOL, $return);
        if (!empty($separator)) {
            if ($this->border_visibility['inner']) {
                $return = $separator . PHP_EOL . $return;
            }
            if ($this->border_visibility['bottom']) {
                $return .= PHP_EOL . $separator;
            }
        }
        $return .= PHP_EOL;

        if (!empty($this->headers)) {
            $return = $this->getHeaderLine() .  PHP_EOL . $return;
        }

        return $return;
    }

    /**
     * Creates a horizontal separator for header separation and table
     * start/end etc.
     *
     * @return string  The horizontal separator.
     */
    protected function getSeparator()
    {
        if (!$this->border) {
            return;
        }

        $horizontal = $this->border['horizontal'];
        $intersection = $this->border['intersection'];

        $return = array();
        foreach ($this->cell_lengths as $cl) {
            $return[] = str_repeat($horizontal, $cl);
        }

        $row_begin = $this->border_visibility['left']
            ? $intersection . str_repeat($horizontal, $this->padding)
            : '';
        $row_end = $this->border_visibility['right']
            ? str_repeat($horizontal, $this->padding) . $intersection
            : '';
        $implode_char = str_repeat($horizontal, $this->padding) . $intersection
            . str_repeat($horizontal, $this->padding);

        return $row_begin . implode($implode_char, $return) . $row_end;
    }

    /**
     * Returns the header line for the table.
     *
     * @return string  The header line of the table.
     */
    protected function getHeaderLine()
    {
        // Make sure column count is correct
        for ($j = 0; $j < count($this->headers); $j++) {
            for ($i = 0; $i < $this->max_cols; $i++) {
                if (!isset($this->headers[$j][$i])) {
                    $this->headers[$j][$i] = '';
                }
            }
        }

        for ($j = 0; $j < count($this->headers); $j++) {
            for ($i = 0; $i < count($this->headers[$j]); $i++) {
                if ($this->getStringLength($this->headers[$j][$i]) <
                    $this->cell_lengths[$i]) {
                    $this->headers[$j][$i] =
                        $this->stringPad($this->headers[$j][$i],
                                       $this->cell_lengths[$i],
                                       ' ',
                                       $this->col_align[$i]);
                }
            }
        }

        $vertical = $this->border['vertical'];
        $row_begin = $this->border_visibility['left']
            ? $vertical . str_repeat(' ', $this->padding)
            : '';
        $row_end = $this->border_visibility['right']
            ? str_repeat(' ', $this->padding) . $vertical
            : '';
        $implode_char = str_repeat(' ', $this->padding) . $vertical
            . str_repeat(' ', $this->padding);

        $separator = $this->getSeparator();
        if (!empty($separator) && $this->border_visibility['top']) {
            $return[] = $separator;
        }
        for ($j = 0; $j < count($this->headers); $j++) {
            $return[] = $row_begin
                . implode($implode_char, $this->headers[$j]) . $row_end;
        }

        return implode(PHP_EOL, $return);
    }

    /**
     * Updates values for maximum columns and rows.
     *
     * @param array $rowdata Data array of a single row.
     *
     * @return void
     */
    protected function updateRowsCols($rowdata = null)
    {
        // Update maximum columns.
        $this->max_cols = max($this->max_cols, count($rowdata));

        // Update maximum rows.
        ksort($this->data);
        $keys            = array_keys($this->data);
        $this->max_rows = end($keys) + 1;

        switch ($this->default_align) {
            case CONSOLE_TABLE_ALIGN_CENTER:
                $pad = STR_PAD_BOTH;
                break;
            case CONSOLE_TABLE_ALIGN_RIGHT:
                $pad = STR_PAD_LEFT;
                break;
            default:
                $pad = STR_PAD_RIGHT;
                break;
        }

        // Set default column alignments
        for ($i = 0; $i < $this->max_cols; $i++) {
            if (!isset($this->col_align[$i])) {
                $this->col_align[$i] = $pad;
            }
        }
    }

    /**
     * Calculates the maximum length for each column of a row.
     *
     * @param array $row The row data.
     *
     * @return void
     */
    public function calculateCellLengths($row)
    {
        for ($i = 0; $i < count($row); $i++) {
            if (!isset($this->cell_lengths[$i])) {
                $this->cell_lengths[$i] = 0;
            }
            $this->cell_lengths[$i] = max($this->cell_lengths[$i],
                                           $this->getStringLength($row[$i]));
        }
    }

    /**
     * Calculates the maximum height for all columns of a row.
     *
     * @param integer $row_number The row number.
     * @param array   $row        The row data.
     *
     * @return void
     */
    public function calculateRowHeight($row_number, $row)
    {
        if (!isset($this->row_heights[$row_number])) {
            $this->row_heights[$row_number] = 1;
        }

        // Do not process horizontal rule rows.
        if ($row === CONSOLE_TABLE_HORIZONTAL_RULE) {
            return;
        }

        for ($i = 0, $c = count($row); $i < $c; ++$i) {
            $lines                           = preg_split('/\r?\n|\r/', $row[$i]);
            $this->row_heights[$row_number] = max($this->row_heights[$row_number],
                                                   count($lines));
        }
    }

    /**
     * Returns a string padded to a certain length with another string.
     *
     * This method behaves exactly like str_pad but is multibyte safe.
     *
     * @param string  $input  The string to be padded.
     * @param integer $length The length of the resulting string.
     * @param string  $pad    The string to pad the input string with. Must
     *                        be in the same charset like the input string.
     * @param const   $type   The padding type. One of STR_PAD_LEFT,
     *                        STR_PAD_RIGHT, or STR_PAD_BOTH.
     *
     * @return string  The padded string.
     */
    protected function stringPad($input, $length, $pad = ' ', $type = STR_PAD_RIGHT)
    {
        $mb_length  = $this->getStringLength($input);
        $sb_length  = strlen($input);
        $pad_length = $this->getStringLength($pad);

        /* Return if we already have the length. */
        if ($mb_length >= $length) {
            return $input;
        }

        /* Shortcut for single byte strings. */
        if ($mb_length == $sb_length && $pad_length == strlen($pad)) {
            return str_pad($input, $length, $pad, $type);
        }

        switch ($type) {
            case STR_PAD_LEFT:
                $left   = $length - $mb_length;
                $output = substr(str_repeat($pad, ceil($left / $pad_length)),
                                         0, $left, $this->charset) . $input;
                break;
            case STR_PAD_BOTH:
                $left   = floor(($length - $mb_length) / 2);
                $right  = ceil(($length - $mb_length) / 2);
                $output = substr(str_repeat($pad, ceil($left / $pad_length)),
                                         0, $left, $this->charset) .
                    $input .
                    substr(str_repeat($pad, ceil($right / $pad_length)),
                                   0, $right, $this->charset);
                break;
            case STR_PAD_RIGHT:
                $right  = $length - $mb_length;
                $output = $input .
                    substr(str_repeat($pad, ceil($right / $pad_length)),
                                   0, $right, $this->charset);
                break;
        }

        return $output;
    }

    protected function getStringLength($string)
    {
        return strlen($this->formatter->removeColors($string));
    }
}
