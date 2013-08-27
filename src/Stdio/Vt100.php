<?php
/**
 * 
 * This file is part of Aura for PHP.
 * 
 * @package Aura.Cli
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Cli\Stdio;

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
 */
class Vt100
{
    /**
     * 
     * Array of format conversions for use on a variety of pre-set console
     * style combinations.
     * 
     * Based on the `ANSI/VT100 Terminal Control reference` at
     * <http://www.termsys.demon.co.uk/vtansi.htm>.
     * 
     * @var array
     * 
     */
    protected $codes = [
        'reset'       => '0',
        'bold'        => '1',
        'dim'         => '2',
        'ul'          => '4',
        'blink'       => '5',
        'reverse'     => '7',
        'black'       => '30',
        'red'         => '31',
        'green'       => '32',
        'yellow'      => '33',
        'blue'        => '34',
        'magenta'     => '35',
        'cyan'        => '36',
        'white'       => '37',
        'blackbg'     => '40',
        'redbg'       => '41',
        'greenbg'     => '42',
        'yellowbg'    => '43',
        'bluebg'      => '44',
        'magentabg'   => '45',
        'cyanbg'      => '46',
        'whitebg'     => '47',
    ];

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

    /**
     * 
     * The PHP_OS value. Provided so we can fake the OS as needed.
     * 
     * @var string
     * 
     */
    protected $php_os = PHP_OS;

    public function __construct()
    {
        $this->regex = '<<\s*((('
                     . implode('|', array_keys($this->codes))
                     . ')(\s*))+)>>';
    }
    
    /**
     * 
     * Forces output to format for POSIX terminals, or to strip for non-POSIX
     * terminals; when null, will auto-determine if the terminal is POSIX.
     * 
     * @param bool $flag True to force formatting, false to force stripping,
     * or null to auto-determine.
     * 
     * @return null
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
     * @return bool
     * 
     */
    public function getPosix()
    {
        return $this->posix;
    }

    /**
     * 
     * Sets the `$php_os` value.
     * 
     * @param string $php_os The new PHP OS value.
     * 
     * @return null
     * 
     */
    public function setPhpOs($php_os)
    {
        $this->php_os = $php_os;
    }

    /**
     * 
     * Gets the `$php_os` value.
     * 
     * @return string
     * 
     */
    public function getPhpOs()
    {
        return $this->php_os;
    }

    /**
     * 
     * Converts <<markup>> in text to VT100 control codes.
     * 
     * @param string $text The text to format.
     * 
     * @return string The formatted text.
     * 
     */
    public function format($text)
    {
        return preg_replace_callback(
            "/{$this->regex}/Umsi",
            [$this, 'formatCallback'],
            $text
        );
    }

    protected function formatCallback(array $matches)
    {
        $str = preg_replace('/(\s+)/msi', ';', $matches[1]);
        return chr(27) . '[' . strtr($str, $this->codes) . 'm';
    }
    
    /**
     * 
     * Strips <<markup>> from text.
     * 
     * @param string $text The text to strip <<markup>> from.
     * 
     * @return string The plain text.
     * 
     */
    public function strip($text)
    {
        return preg_replace("/{$this->regex}/Umsi", '', $text);
    }

    /**
     * 
     * Writes text to a handle object, converting to control codes if the
     * handle is a posix TTY, or to plain text if not.
     * 
     * @param Handle $handle The handle object.
     * 
     * @param string $text The text to write to the handle object, converting
     * %-markup if the handle is a posix TTY, or stripping markup if not.
     * 
     * @return null
     * 
     * @see writeln()
     * 
     */
    public function write(Handle $handle, $text)
    {
        if ($this->isPosix($handle)) {
            // it's a tty. use formatted text.
            $handle->fwrite($this->format($text));
        } else {
            // not a tty, or a non-standard handle. use plain text.
            $handle->fwrite($this->strip($text));
        }
    }

    /**
     * 
     * Writes text to a handle object, converting to control codes if the
     * handle is a posix TTY, or to plain text if not, and then appends a
     * newline.
     * 
     * @param Handle $handle The handle object.
     * 
     * @param string $text The text to write to the handle object, converting
     * %-markup if the handle is a posix TTY, or stripping markup if not.
     * 
     * @return null
     * 
     * @see write()
     * 
     */
    public function writeln(Handle $handle, $text)
    {
        $this->write($handle, $text);
        $handle->fwrite(PHP_EOL);
    }

    /**
     * 
     * Determines if a handle object should be treated as a POSIX terminal.
     * 
     * @param Handle $handle The handle object.
     * 
     * @return bool
     * 
     */
    protected function isPosix(Handle $handle)
    {
        if (is_bool($this->posix)) {
            // forced to posix
            return $this->posix;
        } elseif (strtolower(substr($this->php_os, 0, 3)) == 'win') {
            // windows is not posix
            return false;
        } else {
            // check the handle itself
            return $handle->isPosixTty();
        }
    }
}
