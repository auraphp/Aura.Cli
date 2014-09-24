<?php
namespace Aura\Cli;

class TableTest extends \PHPUnit_Framework_TestCase
{
    public function testSetHeaders()
    {
        $table = new Table();
        $table->setHeaders(array('foo', 'bar'));

$expected = <<<EOT
+-----+-----+
| foo | bar |
+-----+-----+
|     |     |
+-----+-----+

EOT;

        ob_start();
        echo $table->getTable();
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertSame($expected, $output);
    }
}
