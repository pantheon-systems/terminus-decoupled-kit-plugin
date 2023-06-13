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
     * @param string $upstream_id Upstream name or UUID
     *
     * @option org Organization name, label, or ID
     * @option region Specify the service region where the site should be
     *   created. See documentation for valid regions.
     * @option cms Specify the CMS to use for the site.
     *
     * @usage <site> <label> <upstream> Creates a new site named <site>, human-readably labeled <label>, using code from <upstream>.
     * @usage <site> <label> <upstream> --org=<org> --cms<cms> --install-cms<install-cms> --region<region> Creates a new site named <site>, human-readably labeled <label>, associated with <organization>, for the specified <cms>.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Pantheon\Terminus\Exceptions\TerminusException
     */
    public function createProject($site_name, $label, $upstream_id = null, $options = ['org' => null, 'region' => null, 'cms' => null, 'install-cms' => TRUE])
    {
        $install_cms = filter_var($options['install-cms'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($install_cms === NULL) {
          throw new TerminusException('Invalid value: --install-cms must be a boolean.');
        }

        $cms = strtolower($options['cms']);
        $cms_endpont = 'https://dev-' . $site_name . '.pantheonsite.io';

        $cms_type = '';
        $upstream = '';
        switch ($cms) {
            case 'd10':
            case 'drupal':
            case 'drupal 10':
                $cms_type = 'd10';
                $upstream = 'decoupled-drupal-10-composer-managed';
                break;
            case 'd9':
            case 'drupal 9':
                $cms_type = 'd9';
                $upstream = 'decoupled-drupal-composer-managed';
                break;
            case 'wordpress':
            case 'wp':
                $cms_type = 'wp';
                $upstream = 'decoupled-wordpress-composer-managed';
                break;
            case 'any':
                $cms_type = 'any';
                break;
            default:
                throw new TerminusException('Invalid value: --cms only accepts the values drupal, wordpress, wp, d9, d10 or any.');
        }

        $upstream_id ??= $upstream;

        $region = strtolower($options['region'] ?? '');

        $this->create($site_name, $label, $upstream_id, ['org' => $options['org'], 'region' => $region]);

        $this->log()->notice("Installing {cms} on {site_name}", ['cms' => $options['cms'], 'site_name' => $site_name]);

        if ($cms == 'drupal' || $cms == 'd10' || $cms == 'd9' || $cms == 'drupal 9' || $cms == 'drupal 10') {
          $install_cms && $this->_exec('terminus drush ' . $site_name . '.dev -- site-install pantheon_decoupled_profile -y');
          $this->log()->notice("Now let's create your front-end project...");
        }

        if ($cms == 'wordpress' || $cms == 'wp') {
          $install_cms && $this->_exec('terminus wp ' . $site_name . '.dev -- core install --prompt=title,admin_user,admin_email,admin_password');
          $install_cms && $this->_exec('terminus wp ' . $site_name . '.dev rewrite structure \'/%postname%/\'');
          $install_cms && $this->_exec('terminus wp ' . $site_name . '.dev pantheon cache purge-all --no-interaction');
          $this->log()->notice("Now let's create your front-end project...");
        }

        $this->_exec('npm init pantheon-decoupled-kit@canary -- --cmsType ' . $cms_type . ' --cmsEndpoint=' . $cms_endpont);
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

        $upstream_arg = $input->getArgument('upstream_id');
        if (!$input->getOption('cms') && !isset($upstream_arg)) {
          $cms = $this->io()->choice('Choose your CMS back-end', ['Drupal 10', 'Drupal 9', 'WordPress']);
          $input->setOption('cms', $cms);
        }
        else {
          $input->setOption('cms', 'any');
        }
    }
}
