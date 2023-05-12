<?php

namespace Pantheon\TerminusDecoupledKit\Commands;

use Consolidation\AnnotatedCommand\AnnotationData;
use Pantheon\Terminus\Commands\Site\CreateCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Robo\Contract\BuilderAwareInterface;
use Robo\LoadAllTasks;
use Pantheon\Terminus\Exceptions\TerminusException;

/**
 * Class DecoupledKitCreateCommand.
 *
 * @package Pantheon\TerminusDecoupledKit\Commands
 */
class DecoupledKitCreateCommand extends CreateCommand implements BuilderAwareInterface
{
    // Allow Robo tasks to be used in this class. Specifically interactive exec.
    use LoadAllTasks;
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
     * @usage <site> <label> --org=<org> --cms<cms> --install-cms<install-cms> Creates a new site named <site>, human-readably labeled <label>, associated with <organization>, for the specified <cms>.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Pantheon\Terminus\Exceptions\TerminusException
     */
    public function createProject($site_name, $label, $options = ['org' => null, 'region' => null, 'cms' => null, 'install-cms' => TRUE])
    {
        $upstreams = [
          'drupal' => 'c76c0e51-ad85-41d7-b095-a98a75869760',
          'wordpress' => 'c9f5e5c0-248f-4205-b63a-d2729572dd1f'
        ];

        $install_cms = filter_var($options['install-cms'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($install_cms === NULL) {
          throw new TerminusException('Invalid value: --install-cms must be a boolean.');
        }

        $cms = strtolower($options['cms']);
        $cms_endpont = 'https://dev-' . $site_name . '.pantheonsite.io';

        $this->create($site_name, $label, $upstreams[$cms], ['org' => $options['org']]);

        $this->log()->notice("Installing {cms} on {site_name}", ['cms' => $options['cms'], 'site_name' => $site_name]);

        if ($cms == 'drupal') {
          $install_cms && $this->_exec('terminus drush ' . $site_name . '.dev -- site-install pantheon_decoupled_profile -y');
          $this->log()->notice("Now let's create your front-end project...");
          $this->_exec('npm init pantheon-decoupled-kit@canary -- next-drupal --cmsEndpoint=' . $cms_endpont);
        }

        if ($cms == 'wordpress') {
          $install_cms && $this->_exec('terminus wp ' . $site_name . '.dev -- core install --prompt=title,admin_user,admin_email,admin_password');
          $this->log()->notice("Now let's create your front-end project...");
          $this->_exec('npm init pantheon-decoupled-kit@canary -- next-wp --cmsEndpoint=' . $cms_endpont);
        }

        $this->log()->notice("Next steps: import your repository to create a Front-End Site https://docs.pantheon.io/guides/decoupled/no-starter-kit/import-repo");

        return "Your Decoupled Kit project has been created!";
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
