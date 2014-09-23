<?php

/*
 * This file is part of CacheTool.
 *
 * (c) Samuel Gordalina <samuel.gordalina@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CacheTool\Command;

use CacheTool\Util\Formatter;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ApcCacheInfoCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('apc:cache:info')
            ->setDescription('Shows APC user & system cache information')
            ->setHelp('');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->ensureExtensionLoaded('apc');

        $user = $this->getCacheTool()->apc_cache_info('user');
        $system = $this->getCacheTool()->apc_cache_info('system');

        if (!$user || !$system) {
            throw new \RuntimeException("Could not fetch info from APC");
        }

        // missing: cache_list, deleted_list, slot_distribution
        $table = $this->getHelper('table');
        $table
            ->setHeaders(array('Name', 'User', 'System'))
            ->setRows(array(
                array('Slots', $user['num_slots'], $system['num_slots']),
                array('TTL', $user['ttl'], $system['ttl']),
                array('Hits', number_format($user['num_hits']), number_format($system['num_hits'])),
                array('Misses', number_format($user['num_misses']), number_format($system['num_misses'])),
                array('Inserts', number_format($user['num_inserts']), number_format($system['num_inserts'])),
                array('Expunges', number_format($user['expunges']), number_format($system['expunges'])),
                array('Start time', Formatter::date($user['start_time'], 'U'), Formatter::date($system['start_time'], 'U')),
                array('Memory size', Formatter::bytes($user['mem_size']), Formatter::bytes($system['mem_size'])),
                array('Entries', number_format($user['num_entries']), number_format($system['num_entries'])),
                array('File upload progress', $user['file_upload_progress'] ? 'Yes' : 'No', $system['file_upload_progress'] ? 'Yes' : 'No'),
                array('Memory type', $user['memory_type'], $system['memory_type']),
                array('Locking type', $user['locking_type'], $system['locking_type']),
            ))
        ;

        $table->render($output);
    }
}
