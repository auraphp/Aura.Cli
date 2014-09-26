<?php
namespace Aura\Cli;

use Aura\Cli\CliFactory;
use Aura\Cli\Stdio\Formatter;

class TableTest extends \PHPUnit_Framework_TestCase
{
    public function testSetHeaders()
    {
        $table = new Table(new Formatter);
        $table->setHeaders(array('foo', 'bar'));

$expected = <<<EOT
+-----+-----+
| foo | bar |
+-----+-----+
|     |     |
+-----+-----+

EOT;

        $output = $table->getTable();
        $this->assertSame($expected, $output);
    }

    public function testSetHeadersAndData()
    {
        $table = new Table(new Formatter);
        $headers = array(
            'one' => 'foo',
            'two' => 'bar'
        );

        $data = array(
            array(
                'x' => 'baz',
            )
        );

        $table->setHeaders($headers);
        $table->addData($data);

        $expected = <<<EOT
+-----+-----+
| foo | bar |
+-----+-----+
| baz |     |
+-----+-----+

EOT;

        $output = $table->getTable();
        $this->assertSame($expected, $output);
    }

    public function testBorderAscii()
    {
        $table = new Table(new Formatter, CONSOLE_TABLE_ALIGN_LEFT, CONSOLE_TABLE_BORDER_ASCII);
        $table->setHeaders(array('City', 'Mayor'));
        $table->addRow(array('Leipzig', 'Major Tom'));
        $table->addRow(array('New York', 'Towerhouse'));

        $expected = <<<EOT
+----------+------------+
| City     | Mayor      |
+----------+------------+
| Leipzig  | Major Tom  |
| New York | Towerhouse |
+----------+------------+

EOT;

        $output = $table->getTable();
        $this->assertSame($expected, $output);
    }

    public function testCustom()
    {
        $table = new Table(
            new Formatter,
            CONSOLE_TABLE_ALIGN_LEFT,
            array('horizontal' => '=', 'vertical' => '', 'intersection' => '')
        );
        $table->setHeaders(array('City', 'Mayor'));
        $table->addRow(array('Leipzig', 'Major Tom'));
        $table->addRow(array('New York', 'Towerhouse'));

        $expected = <<<EOT
======================
 City      Mayor      
======================
 Leipzig   Major Tom  
 New York  Towerhouse 
======================

EOT;

        $output = $table->getTable();
        $this->assertSame($expected, $output);
    }

    public function testCustom2()
    {
        $table = new Table(
            new Formatter,
            CONSOLE_TABLE_ALIGN_LEFT,
            array('horizontal' => '=', 'vertical' => ':', 'intersection' => '*')
        );
        $table->setHeaders(array('City', 'Mayor'));
        $table->addRow(array('Leipzig', 'Major Tom'));
        $table->addRow(array('New York', 'Towerhouse'));

        $expected = <<<EOT
*==========*============*
: City     : Mayor      :
*==========*============*
: Leipzig  : Major Tom  :
: New York : Towerhouse :
*==========*============*

EOT;

        $output = $table->getTable();
        $this->assertSame($expected, $output);
    }

    public function testDisableBorders()
    {
        $table = new Table(new Formatter);
        $table->setHeaders(array('City', 'Mayor'));
        $table->addRow(array('Leipzig', 'Major Tom'));
        $table->addRow(array('New York', 'Towerhouse'));

        $table->setBorderVisibility(
            array(
                'left'  => false,
                'right' => false,
            )
        );
        // "Horizontal borders only";
        $expected = <<<EOT
---------+-----------
City     | Mayor     
---------+-----------
Leipzig  | Major Tom 
New York | Towerhouse
---------+-----------

EOT;
        $output = $table->getTable();
        $this->assertSame($expected, $output);

        $table->setBorderVisibility(
            array(
                'top'    => false,
                'right'  => false,
                'bottom' => false,
                'left'   => false,
                'inner'  => false,
            )
        );
        // No borders
        $expected = <<<EOT
City     | Mayor     
Leipzig  | Major Tom 
New York | Towerhouse

EOT;
        $output = $table->getTable();
        $this->assertSame($expected, $output);

        $table->setBorderVisibility(
            array(
                'top'    => false,
                'right'  => true,
                'bottom' => false,
                'left'   => true,
                'inner'  => true,
            )
        );
        // Vertical and inner only
        $expected = <<<EOT
| City     | Mayor      |
+----------+------------+
| Leipzig  | Major Tom  |
| New York | Towerhouse |

EOT;
        $output = $table->getTable();
        $this->assertSame($expected, $output);
    }

    public function testBorderDot()
    {
        $table = new Table(
            new Formatter,
            CONSOLE_TABLE_ALIGN_LEFT,
            '.'
        );
        $table->setHeaders(array('City', 'Mayor'));
        $table->addRow(array('Leipzig', 'Major Tom'));
        $table->addRow(array('New York', 'Towerhouse'));

        $expected = <<<EOT
.........................
. City     . Mayor      .
.........................
. Leipzig  . Major Tom  .
. New York . Towerhouse .
.........................

EOT;

        $output = $table->getTable();
        $this->assertSame($expected, $output);
    }

    public function testBorderEmpty()
    {
        $table = new Table(
            new Formatter,
            CONSOLE_TABLE_ALIGN_LEFT,
            ''
        );
        $table->setHeaders(array('City', 'Mayor'));
        $table->addRow(array('Leipzig', 'Major Tom'));
        $table->addRow(array('New York', 'Towerhouse'));

        $expected = <<<EOT
 City      Mayor      
 Leipzig   Major Tom  
 New York  Towerhouse 

EOT;

        $output = $table->getTable();
        $this->assertSame($expected, $output);
    }

    public function testBug20181()
    {
        $table = new Table(new Formatter);
        $table->setAlign(1, CONSOLE_TABLE_ALIGN_RIGHT);
        $table->setHeaders(array('f', 'bar'));
        $table->addRow(array('baz', 'b'));
        $expected = <<<EOT
+-----+-----+
| f   | bar |
+-----+-----+
| baz |   b |
+-----+-----+

EOT;

        $output = $table->getTable();
        $this->assertSame($expected, $output);
    }

    public function testColor()
    {
        $table = new Table(
            new Formatter,
            CONSOLE_TABLE_ALIGN_LEFT,
            CONSOLE_TABLE_BORDER_ASCII,
            1,
            null
        );
        $table->setHeaders(array('foo', 'bar'));
        $table->addRow(array('baz', '<<blue>>blue<<reset>>'));

        $expected = <<<EOT
+-----+------+
| foo | bar  |
+-----+------+
| baz | [34mblue[0m |
+-----+------+

EOT;
        $cli_factory = new CliFactory;
        $stdio = $cli_factory->newStdio();
/*
        ob_start();
        $stdio->out($table->getTable());
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertSame($expected, $output);
*/
    }

    public function testFilters()
    {
        $data = array(
            array('one', 'two'),
            array('three', 'four'),
            CONSOLE_TABLE_HORIZONTAL_RULE,
            array('five', 'six'),
            array('seven', 'eight'),
        );
        $filter = 'strtoupper';

        $table = new Table(new Formatter);
        $table->setHeaders(array('foo', 'bar'));
        $table->addData($data);
        $table->addFilter(0, $filter);

        $expected = <<<EOT
+-------+-------+
| foo   | bar   |
+-------+-------+
| ONE   | two   |
| THREE | four  |
+-------+-------+
| FIVE  | six   |
| SEVEN | eight |
+-------+-------+

EOT;
        $output = $table->getTable();
        $this->assertSame($expected, $output);
    }

    public function testMultibyte()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
        $table = new Table(new Formatter);
        $table->setHeaders(array('Schön', 'Häßlich'));
        $table->addData(array(array('Ich', 'Du'), array('Ä', 'Ü')));

        $expected = <<<EOT
+-------+---------+
| Schön | Häßlich |
+-------+---------+
| Ich   | Du      |
| Ä     | Ü       |
+-------+---------+

EOT;
        $output = $table->getTable();
        $this->assertSame($expected, $output);

        $table = new Table(new Formatter);
        $table->addRow(array("I'm from 中国"));
        $table->addRow(array("我是中国人"));
        $table->addRow(array("I'm from China"));

        $expected = <<<EOT
+----------------+
| I'm from 中国  |
| 我是中国人     |
| I'm from China |
+----------------+

EOT;
        $output = $table->getTable();
        $this->assertSame($expected, $output);
    }

    public function testMultiline()
    {
        $table = new Table(new Formatter);
        $data = array(
            array('col1', '0', "col3\nmultiline", 'col4'),
            array('r2col1', 'r2col2', "r2col3\nmultiline", 'r2col4'),
            array('r3col1', 'r3col2', "r3col3\nmultiline\r\nverymuch", 'r3col4'),
            array('r4col1', 'r4col2', "r4col3", 'r4col4'),
            array('r5col1', 'r5col2', "r5col3", 'r5col4'),
        );

        $table->setHeaders(array("h1\nmultiline", 'h2', "h3", 'h4'));
        $table->addData($data);

        $expected = <<<EOT
+-----------+--------+-----------+--------+
| h1        | h2     | h3        | h4     |
| multiline |        |           |        |
+-----------+--------+-----------+--------+
| col1      | 0      | col3      | col4   |
|           |        | multiline |        |
| r2col1    | r2col2 | r2col3    | r2col4 |
|           |        | multiline |        |
| r3col1    | r3col2 | r3col3    | r3col4 |
|           |        | multiline |        |
|           |        | verymuch  |        |
| r4col1    | r4col2 | r4col3    | r4col4 |
| r5col1    | r5col2 | r5col3    | r5col4 |
+-----------+--------+-----------+--------+

EOT;
        $output = $table->getTable();
        $this->assertSame($expected, $output);
    }

    public function testFromArray()
    {
        $table = Table::fromArray(
            array('one line header'),
            array(
                array("multiple\nlines"),
                array('one line')
            )
        );
        $expected = <<<EOT
+-----------------+
| one line header |
+-----------------+
| multiple        |
| lines           |
| one line        |
+-----------------+

EOT;
        $output = $table->getTable();
        $this->assertSame($expected, $output);
    }

    public function testNoHeader()
    {
        $table = new Table(new Formatter);
        $table->addData(array(array('foo', 'bar')));

        $expected = <<<EOT
+-----+-----+
| foo | bar |
+-----+-----+

EOT;
        $output = $table->getTable();
        $this->assertSame($expected, $output);
    }

    public function testNoRows()
    {
        $table = new Table(new Formatter);
        $table->setHeaders(array('foo', 'bar'));

        $expected = <<<EOT
+-----+-----+
| foo | bar |
+-----+-----+
|     |     |
+-----+-----+

EOT;
        $output = $table->getTable();
        $this->assertSame($expected, $output);
    }

    public function testRules1()
    {
        $table = new Table(new Formatter);
        $data = array(
            array('one', 'two'),
            CONSOLE_TABLE_HORIZONTAL_RULE,
            array('three', 'four'),
            CONSOLE_TABLE_HORIZONTAL_RULE,
            CONSOLE_TABLE_HORIZONTAL_RULE,
            array('five', 'six'),
            array('seven', 'eight'),
        );
        $table->setHeaders(array('foo', 'bar'));
        $table->addData($data);
        $table->addSeparator();

        $expected = <<<EOT
+-------+-------+
| foo   | bar   |
+-------+-------+
| one   | two   |
+-------+-------+
| three | four  |
+-------+-------+
+-------+-------+
| five  | six   |
| seven | eight |
+-------+-------+
+-------+-------+

EOT;
        $output = $table->getTable();
        $this->assertSame($expected, $output);
    }

    public function testRules2()
    {
        $table = new Table(new Formatter, CONSOLE_TABLE_ALIGN_LEFT, '');
        $data = array(
            array('one', 'two'),
            CONSOLE_TABLE_HORIZONTAL_RULE,
            array('three', 'four'),
            CONSOLE_TABLE_HORIZONTAL_RULE,
            CONSOLE_TABLE_HORIZONTAL_RULE,
            array('five', 'six'),
            array('seven', 'eight'),
        );
        $table->setHeaders(array('foo', 'bar'));
        $table->addData($data);
        $table->addSeparator();

        $expected = <<<EOT
 foo    bar   
 one    two   
 three  four  
 five   six   
 seven  eight 

EOT;
        $output = $table->getTable();
        $this->assertSame($expected, $output);
    }

    public function testRules3()
    {
        $table = new Table(new Formatter, CONSOLE_TABLE_ALIGN_LEFT, '#', 0);
        $data = array(
            array('one', 'two'),
            CONSOLE_TABLE_HORIZONTAL_RULE,
            array('three', 'four'),
            CONSOLE_TABLE_HORIZONTAL_RULE,
            CONSOLE_TABLE_HORIZONTAL_RULE,
            array('five', 'six'),
            array('seven', 'eight'),
        );
        $table->setHeaders(array('foo', 'bar'));
        $table->addData($data);
        $table->addSeparator();

        $expected = <<<EOT
#############
#foo  #bar  #
#############
#one  #two  #
#############
#three#four #
#############
#############
#five #six  #
#seven#eight#
#############
#############

EOT;
        $output = $table->getTable();
        $this->assertSame($expected, $output);
    }
}
