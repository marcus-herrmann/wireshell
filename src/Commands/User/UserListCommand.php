<?php namespace Wireshell\Commands\User;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wireshell\Helpers\PwUserTools;
use Wireshell\Helpers\WsTools as Tools;

/**
 * Class UserListCommand
 *
 * Creating ProcessWire users
 *
 * @package Wireshell
 * @author Marcus Herrmann
 * @author Tabea David <info@justonestep.de>
 */

class UserListCommand extends PwUserTools
{

    /**
     * Configures the current command.
     */
    public function configure()
    {
        $this
            ->setName('user:list')
            ->setAliases(['u:l'])
            ->setDescription('Lists ProcessWire users')
            ->addOption('role', null, InputOption::VALUE_REQUIRED, 'Find user by role');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        parent::bootstrapProcessWire($output);

        $users = $this->getUsers($input);
        $output->writeln("Users: " . $users->getTotal());

        if ($users->getTotal() > 0) {
            $content = $this->getUserData($users);
            $headers = array('Username', 'E-Mail', 'Superuser', 'Roles');
            $tables = array($this->buildTable($output, $content, $headers));
            $this->renderTables($output, $tables);
        }
    }

    /**
     * get users
     *
     * @param InputInterface $input
     */
    private function getUsers($input) {
        if (!empty($input->getOption('role'))) {
            $users = wire('users')->find('roles=' . $input->getOption('role'))->sort('name');
        } else {
            $users = wire('users')->find('start=0')->sort('name');
        }

        return $users;
    }

    /**
     * get user data
     *
     * @param $users
     * @return array
     */
    private function getUserData($users) {
        $content = array();
        foreach ($users as $user) {
            $roles = array();
            foreach ($user->roles as $role) {
                $roles[] = $role->name;
            }

            $content[] = array(
                $user->name,
                $user->email,
                $user->isSuperuser() ? '✔' : '',
                implode(', ', $roles)
            );
        }

        return $content;
    }

    /**
     * @param OutputInterface $output
     * @param array $content
     * @param array $headers
     */
    protected function buildTable(OutputInterface $output, $content, $headers)
    {
        $tablePW = new Table($output);
        $tablePW
            ->setStyle('borderless')
            ->setHeaders($headers)
            ->setRows($content);

        return $tablePW;
    }

    /**
     * @param OutputInterface $output
     * @param $tables
     */
    protected function renderTables(OutputInterface $output, $tables)
    {
        $output->writeln("\n");

        foreach ($tables as $table)
        {
            $table->render();
            $output->writeln("\n");
        }
    }

}
