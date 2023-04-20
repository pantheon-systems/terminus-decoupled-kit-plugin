<?php

namespace Pantheon\TerminusDecoupledKit\Commands;

use Consolidation\AnnotatedCommand\AnnotationData;
use Pantheon\Terminus\Commands\Site\CreateCommand;
// use Pantheon\Terminus\Helpers\Traits\CommandExecutorTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DecoupledKitCreateCommand.
 *
 * @package Pantheon\TerminusDecoupledKit\Commands
 */
class DecoupledKitCreateCommand extends CreateCommand
{
    // use CommandExecutorTrait;
    /**
     * Creates a new Decoupled Kit project.
     *
     * @authorize
     *
     * @command decoupled-kit:create
     *
     * @param string $site_name Site name
     * @param string $label Site label
     * @option org Organization name, label, or ID
     * @option region Specify the service region where the site should be
     *   created. See documentation for valid regions.
     * @option cms Specify the CMS to use for the site.
     *
     * @usage <site> <label> Creates a new site named <site>, human-readably labeled <label>.
     * @usage <site> <label> --org=<org> --cms<cms> Creates a new site named <site>, human-readably labeled <label>, associated with <organization>, for the specified <cms>.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Pantheon\Terminus\Exceptions\TerminusException
     */
    public function createProject($site_name, $label, $options = ['org' => null, 'region' => null, 'cms' => null])
    {
        // $this->log()->notice("Creating {site_name}: {label} on {org}", ['site_name' => $site_name, 'label' => $label, 'org' => $options['org']]);

        // TODO:
        // Install CMS
        // Call create-decoupled-kit with correct args

        $upstreams = [
          'drupal' => 'c76c0e51-ad85-41d7-b095-a98a75869760',
          'wordpress' => 'c9f5e5c0-248f-4205-b63a-d2729572dd1f'
        ];

        $this->create($site_name, $label, $upstreams[strtolower($options['cms'])], ['org' => $options['org']]);

        // Example using exec
        // $exec_result = $this->execute("terminus site:list");
        // $this->log()->notice(sprintf($exec_result[0]));

        return "Site created!";
    }

    /**
     * Prompt the user for the any unspecified inputs.
     *
     * n.b. This hook is not called in --no-interaction mode.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param AnnotationData $annotationData
     *
     * @hook interact decoupled-kit:create
     *
     * @throws \Pantheon\Terminus\Exceptions\TerminusException
     */
    public function interact(InputInterface $input, OutputInterface $output, AnnotationData $annotationData)
    {
        if (!$input->getArgument('site_name')) {
          $site_name = $this->io()->ask('Choose your site name', 'my-site-name');
          $input->setArgument('site_name', $site_name);
        }
        if (!$input->getArgument('label')) {
          $label = $this->io()->ask('Choose your site label', 'My Site Label');
          $input->setArgument('label', $label);
        }
        if (!$input->getOption('cms')) {
          $cms = $this->io()->choice('Choose your CMS back-end', ['Drupal', 'WordPress']);
          $input->setOption('cms', $cms);
        }
    }
}
