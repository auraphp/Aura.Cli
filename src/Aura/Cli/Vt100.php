<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Cli;

/**
 * 
 * Text formatting for VT100 terminals.
 * 
 * @package Aura.Cli
 * 
 * @author Clay Loveless <clay@killersoft.com>
 * 
 * @author Paul M. Jones <pmjones@paul-m-jones.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
class Vt100
{
    /**
     * 
     * Array of format conversions for use on a variety of pre-set console
     * style combinations.
     * 
     * Based on `ANSI VT100 Color/Style Codes` according to the
     * [VT100 User Guide](http://vt100.net/docs/vt100-ug) and the
     * [ANSI/VT100 Terminal Control reference](http://www.termsys.demon.co.uk/vtansi.htm).
     * Inspired by [PEAR Console_Color](http://pear.php.net/Console_Color).
     * 
     * @var array
     * 
     */
    protected $format = array(
        
        // literal percent sign
        '%%'    => '%',             // percent-sign
        
        // color, normal weight
        '%k'    => "\033[30m",      // black
        '%r'    => "\033[31m",      // red
        '%g'    => "\033[32m",      // green
        '%y'    => "\033[33m",      // yellow
        '%b'    => "\033[34m",      // blue
        '%m'    => "\033[35m",      // magenta/purple
        '%p'    => "\033[35m",      // magenta/purple
        '%c'    => "\033[36m",      // cyan/light blue
        '%w'    => "\033[37m",      // white
        '%n'    => "\033[0m",       // reset to terminal default
        
        // color, bold
        '%K'    => "\033[30;1m",    // black, bold
        '%R'    => "\033[31;1m",    // red, bold
        '%G'    => "\033[32;1m",    // green, bold
        '%Y'    => "\033[33;1m",    // yellow, bold
        '%B'    => "\033[34;1m",    // blue, bold
        '%M'    => "\033[35;1m",    // magenta/purple, bold
        '%P'    => "\033[35;1m",    // magenta/purple, bold
        '%C'    => "\033[36;1m",    // cyan/light blue, bold
        '%W'    => "\033[37;1m",    // white, bold
        '%N'    => "\033[0;1m",     // terminal default, bold
        
        // background color
        '%0'    => "\033[40m",      // black background
        '%1'    => "\033[41m",      // red background
        '%2'    => "\033[42m",      // green background
        '%3'    => "\033[43m",      // yellow background
        '%4'    => "\033[44m",      // blue background
        '%5'    => "\033[45m",      // magenta/purple background
        '%6'    => "\033[46m",      // cyan/light blue background
        '%7'    => "\033[47m",      // white background
        
        // assorted style shortcuts
        '%F'    => "\033[5m",       // blink/flash
        '%_'    => "\033[5m",       // blink/flash
        '%U'    => "\033[4m",       // underline
        '%I'    => "\033[7m",       // reverse/inverse
        '%*'    => "\033[1m",       // bold
        '%d'    => "\033[2m",       // dim        
    );
    
    /**
     * 
     * The POSIX terminal flag.
     * 
     * @var bool
     * 
     * @see setPosix()
     * 
     * @see getPosix()
     * 
     */
    protected $posix = null;
    
    protected $php_os = PHP_OS;
    
    /**
     * 
     * Forces output to format for POSIX terminals, or to strip for non-POSIX
     * terminals; when null, will auto-determine if the terminal is POSIX.
     * 
     * @param bool $flag True to force formatting, false to force stripping,
     * or null to auto-determine.
     * 
     * @return void
     * 
     */
    public function setPosix($flag)
    {
        if ($flag === null) {
            $this->posix = null;
        } else {
            $this->posix = (bool) $flag;
        }
    }
    
    /**
     * 
     * Gets the value of the POSIX terminal flag.
     * 
     * @param mixed $flag True to force formatting, false to force stripping,
     * or null to auto-determine.
     * 
     */
    public function getPosix()
    {
        return $this->posix;
    }
    
    public function setPhpOs($php_os)
    {
        $this->php_os = $php_os;
    }
    
    public function getPhpOs()
    {
        return $this->php_os;
    }
    
    /**
     * 
     * Converts VT100 %-markup in text to control codes.
     * 
     * @param string $text The text to format.
     * 
     * @return string The formatted text.
     * 
     */
    public function format($text)
    {
        return strtr($text, $this->format);
    }
    
    /**
     * 
     * Strips VT100 %-markup from text.
     * 
     * @param string $text The text to strip %-markup from.
     * 
     * @return string The plain text.
     * 
     */
    public function strip($text)
    {
        static $strip = null;
        if ($strip === null) {
            foreach ($this->format as $key => $val) {
                $strip[$key] = '';
            }
            $strip['%%'] = '%';
        }
        
        return strtr($text, $strip);
    }
    
    /**
     * 
     * Writes text to a file handle, converting to control codes if the handle
     * is a posix TTY, or to plain text if not.
     * 
     * @param resource $handle The file handle.
     * 
     * @param string $text The text to write to the file handle, converting
     * %-markup if the handle is a posix TTY, or stripping markup if not.
     * 
     * @return void
     * 
     * @see writeln()
     * 
     */
    public function write($handle, $text)
    {
        if ($this->isPosix($handle)) {
            // it's a tty. use formatted text.
            fwrite($handle, $this->format($text));
        } else {
            // not a tty, or a non-standard handle. use plain text.
            fwrite($handle, $this->strip($text));
        }
    }
    
    /**
     * 
     * Writes text to a file handle, converting to control codes if the handle
     * is a posix TTY, or to plain text if not, and then appends a newline.
     * 
     * @param resource $handle The file handle.
     * 
     * @param string $text The text to write to the file handle, converting
     * %-markup if the handle is a posix TTY, or stripping markup if not.
     * 
     * @return void
     * 
     * @see write()
     * 
     */
    public function writeln($handle, $text)
    {
        $this->write($handle, $text);
        fwrite($handle, PHP_EOL);
    }
    
    /**
     * 
     * Determines if a stream handle should be treated as a POSIX terminal.
     * 
     * @param resource $handle The stream handle.
     * 
     * @return bool
     * 
     */
    protected function isPosix($handle)
    {
        if (is_bool($this->posix)) {
            // forced to posix
            return $this->posix;
        } elseif (strtolower(substr($this->php_os, 0, 3)) == 'win') {
            // windows is not posix
            return false;
        } else {
            // silence posix_isatty() errors regarding non-standard handles,
            // e.g. php://memory
            return @posix_isatty($handle);
        }
    }
}
