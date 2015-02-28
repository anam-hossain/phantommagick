<?php
namespace Anam\Html2PdfConverter;

use Exception;

class Runner
{
    /**
     * @var string Path to phantomjs binary.
     **/
    protected $binary = 'phantomjs';

    /**
     * @var bool If true, all Command output is returned verbatim
     **/
    private $debug = true;

    /**
     * Constructor
     *
     * @param bool Debug mode
     * @return void
     **/
    public function __construct($debug = null)
    {
        if ($debug !== null) {
            $this->debug = $debug;
        }
    }

    public function run($script, $source, $output, array $options = array())
    {
        $this->verifyBinary($this->binary);

        $arguments = ['script' => $script, 'source' => $source, 'output' => $output] + $options;

        $arguments = $this->escapeShellArguments($arguments);

        $command = escapeshellcmd("{$this->binary} ") . implode(' ', $arguments);

        //die($command);
        if ($this->debug) {
            $command .= ' 2>&1';
        }
        // Execute
        return shell_exec($command);

        //die($result);
        // Escape
        // $args = func_get_args();
        // $cmd = escapeshellcmd("{$this->bin} " . implode(' ', $args));
        // if($this->debug) $cmd .= ' 2>&1';
        // // Execute
        // $result = shell_exec($cmd);
        // if($this->debug) return $result;
        // if($result === null) return false;
        // // Return
        // if(substr($result, 0, 1) !== '{') return $result; // not JSON
        // $json = json_decode($result, $as_array = true);
        // if($json === null) return false;
        // return $json;
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

        if ($this->stringContains($uname, 'darwin')) {
            if (! shell_exec(escapeshellcmd("which {$binary}"))) {
                throw new Exception('Binary does not exist');
            }
        } elseif ($this->stringContains($uname, 'win')) {
            if (! shell_exec(escapeshellcmd("where {$binary}"))) {
                throw new Exception('Binary does not exist');
            }
        } elseif ($this->stringContains($uname, 'linux')) {
            if (! shell_exec(escapeshellcmd("which {$binary}"))) {
                throw new Exception('Binary does not exist');
            }
        } else {
            throw new \RuntimeException("Unknown operating system.");
        }
    }

    public function stringContains($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }
}
