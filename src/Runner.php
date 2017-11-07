<?php
namespace Anam\PhantomMagick;

use Exception;
use Anam\PhantomMagick\Str;

class Runner
{
    /**
     * Executable phantomjs binary path
     *
     * @var string
     */
    protected $binary = 'phantomjs';

    /**
     * Executable phantomjs binary path
     *
     * @var string
     */
    protected $alternateBinary;

    /**
     * Phantomjs command with arguments
     *
     * @var string
     */
    protected $command;

    /**
     * Constructor
     *
     * @param string $binary
     */
    public function __construct($binary = null)
    {
        if ($binary !== null) {
            $this->binary = $binary;
        }

        if (class_exists('\Anam\PhantomLinux\Path')) {
            $this->setAlternateBinary(\Anam\PhantomLinux\Path::binaryPath());
        }
    }

    /**
     * Set Alternate Binary
     *
     * @param string $binary
     *
     * @return void
     **/
    public function setAlternateBinary($binary)
    {
        $this->alternateBinary = $binary;
    }

    /**
     * Get Alternate binary
     *
     * @return string
     **/
    public function getAlternateBinary()
    {
       return $this->alternateBinary;
    }

    /**
     * Set shell command
     *
     * @param string $command
     *
     * @return void
     **/
    public function setCommand($command)
    {
        $this->command = $command;
    }

    /**
     * Get PhantomJS shell command
     *
     * @return string
     **/
    public function getCommand()
    {
       return $this->command;
    }

    /**
     * Run the phantomjs command
     * @param  string $script  Conversion script
     * @param  string $source  Data file location
     * @param  string $output  Output file location
     * @param  array  $options
     * @return string
     */
    public function run($script, $source, $output, array $options = array())
    {
        $binary = $this->pickBinary();

        $arguments = ['script' => $script, 'source' => $source, 'output' => $output] + $options;

        $arguments = $this->escapeShellArguments($arguments);

        $this->setCommand(escapeshellcmd("{$binary} --ssl-protocol=any --ignore-ssl-errors=yes ") . implode(' ', $arguments));

        return shell_exec($this->getCommand());
    }

    /**
     * Escape shell arguments
     *
     * @param  array  $arguments
     * @return array
     */
    private function escapeShellArguments(array $arguments)
    {
        foreach ($arguments as $key => $argument) {
            $arguments[$key] = escapeshellarg($argument);
        }

        return $arguments;
    }

    /**
     * Check phantomjs is installed or not
     *
     * @param  string $binary  Binary location
     * @return boolean
     */
    public function verifyBinary($binary)
    {
        $uname = strtolower(php_uname());

        if (Str::contains($uname, 'darwin')) {
            if (! shell_exec(escapeshellcmd("command -v {$binary} >/dev/null 2>&1"))) {
                return false;
            }
        } elseif (Str::contains($uname, 'win')) {
            if (! shell_exec(escapeshellcmd("{$binary}"))) {
                return false;
            }
        } elseif (Str::contains($uname, 'linux')) {
            if (! shell_exec(escapeshellcmd("which {$binary}"))) {
                return false;
            }
        } else {
            throw new \RuntimeException("Unknown operating system.");
        }

        return true;
    }

    /**
     * Choose binary
     *
     * @return string
     */
    public function pickBinary()
    {
        if ($this->binary != 'phantomjs') {
            if (! $this->verifyBinary($this->binary)) {
                throw new Exception('Binary does not exist');
            }

            return $this->binary;
        }


        if (! $this->verifyBinary($this->binary)) {

            if (! $this->verifyBinary($this->getAlternateBinary())) {
                throw new Exception('Binary does not exist');
            }

            $this->binary = $this->getAlternateBinary();
        }

        return $this->binary;
    }
}
