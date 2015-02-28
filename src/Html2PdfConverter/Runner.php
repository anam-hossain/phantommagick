<?php
namespace Anam\Html2PdfConverter;

use Exception;
use Anam\Html2PdfConverter\Str;

class Runner
{
    /**
     * @var string Path to phantomjs binary.
     **/
    protected $binary = 'phantomjs';

    public function __construct($binary = null)
    {
        if ($binary !== null) {
            $this->binary = $binary;
        }
    }

    public function run($script, $source, $output, array $options = array())
    {
        $this->verifyBinary($this->binary);

        $arguments = ['script' => $script, 'source' => $source, 'output' => $output] + $options;

        $arguments = $this->escapeShellArguments($arguments);

        $command = escapeshellcmd("{$this->binary} ") . implode(' ', $arguments);

        return shell_exec($command);
    }

    private function escapeShellArguments(array $arguments)
    {
        foreach ($arguments as $key => $argument) {
            $arguments[$key] = escapeshellarg($argument);
        }

        return $arguments;
    }

    public function verifyBinary($binary)
    {
        $uname = strtolower(php_uname());

        if (Str::contains($uname, 'darwin')) {
            if (! shell_exec(escapeshellcmd("which {$binary}"))) {
                throw new Exception('Binary does not exist');
            }
        } elseif (Str::contains($uname, 'win')) {
            if (! shell_exec(escapeshellcmd("where {$binary}"))) {
                throw new Exception('Binary does not exist');
            }
        } elseif (Str::contains($uname, 'linux')) {
            if (! shell_exec(escapeshellcmd("which {$binary}"))) {
                throw new Exception('Binary does not exist');
            }
        } else {
            throw new \RuntimeException("Unknown operating system.");
        }
    }
}
