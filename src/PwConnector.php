<?php namespace Wireshell;

use Symfony\Component\Console\Command\Command as SymfonyCommand;

/**
 * Class PwConnector
 *
 * Serving as connector layer between Symfony Commands and ProcessWire
 *
 * @package Wireshell
 * @author Marcus Herrmann
 */

abstract class PwConnector extends SymfonyCommand
{

    public $moduleServiceURL;
    public $moduleServiceKey;
    protected $userContainer;
    protected $roleContainer;

    /**
     * @param $output
     */
    protected function checkForProcessWire($output)
    {
        if (!is_dir(getcwd() . "/wire")) {

            $output->writeln("<error>No ProcessWire installation found.</error>");
            exit(1);
        }
    }

    /**
     * @param $output
     */
    protected function bootstrapProcessWire($output)
    {
        $this->checkForProcessWire($output);

        if (!function_exists('wire')) {
            include(getcwd() . '/index.php');
        }

        $this->userContainer = wire('pages')->get('29');
        $this->roleContainer = wire('pages')->get('30');

        $this->moduleServiceURL = wire('config')->moduleServiceURL;
        $this->moduleServiceKey = wire('config')->moduleServiceKey;

    }

}
