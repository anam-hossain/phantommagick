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
    protected $alternateBinary = '/bin/phantomjs';

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
        $this->pickBinary();

        $arguments = ['script' => $script, 'source' => $source, 'output' => $output] + $options;

        $arguments = $this->escapeShellArguments($arguments);

        $command = escapeshellcmd("{$this->binary} ") . implode(' ', $arguments);

        return shell_exec($command);
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
     * @return void
     */
    public function verifyBinary($binary)
    {
        $uname = strtolower(php_uname());

        if (Str::contains($uname, 'darwin')) {
            if (! shell_exec(escapeshellcmd("which {$binary}"))) {
                return false;
            }
        } elseif (Str::contains($uname, 'win')) {
            if (! shell_exec(escapeshellcmd("where {$binary}"))) {
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
     * @return void
     */
    public function pickBinary()
    {
        if ($this->binary != 'phantomjs') {
            if (! $this->verifyBinary($this->binary)) {
                 throw new Exception('Binary does not exist');
            }
        }

        if ($this->binary == 'phantomjs') {
            if (! $this->verifyBinary($this->binary)) {
                $this->binary = $this->$alternateBinary;

                if (! $this->verifyBinary($this->binary)) {
                     throw new Exception('Binary does not exist');
                }
            }
        }
    }
}
