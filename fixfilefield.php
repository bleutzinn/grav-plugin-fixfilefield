<?php

namespace Grav\Plugin;

use Composer\Autoload\ClassLoader;
use Grav\Common\Plugin;
use Grav\Plugin\Form\Form;
use RocketTheme\Toolbox\Event\Event;
use Symfony\Component\Yaml\Yaml;

/**
 * Class FixfilefieldPlugin
 * @package Grav\Plugin
 */
class FixfilefieldPlugin extends Plugin
{
    /**
     * Composer autoload
     *
     * @return ClassLoader
     */
    public function autoload(): ClassLoader
    {
        return require __DIR__ . '/vendor/autoload.php';
    }

    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onPluginsInitialized' => [
                // Uncomment following line when plugin requires Grav < 1.7
                // ['autoload', 100000],
                ['onPluginsInitialized', 0]
            ]
        ];
    }

    /**
     * getFileFields
     * Get form file fields
     *
     * @return mixed
     */
    public function getFileFields(): mixed
    {
        $forms = $this->grav['page']->getForms();

        $results = [];
        foreach ($forms as $form) {
            if (isset($form['fields'])) {
                foreach ($form['fields'] as $name => $definition) {
                    if (isset($definition['type']) && $definition['type'] == 'file') {
                        $results[$name] = $definition;
                    }
                }
            }
        }

        if (!empty($results)) {
            return $results;
        } else {
            return null;
        }
    }

    /*
        Move adding of element attributes from onOutputGenerated into this function
    */
    public function onFormInitialized(Event $event)
    {
        /** @var Form */
        $form = $event['form'];
        $fields = $form->getFields();

        // dump($fields);

        $this->updateFileFields($fields, $form->getFormName());

        // dump($fields);
        // exit;

        $form->setFields($fields);
    }

    /**
     * Recurse over all (nested) form fields and update each File field found. 
     * When field contains attribute `fields` the method will recurse the attribute.
     * 
     * @param array &$fields contains an array of field definitions
     * @param string $formName contains the name of the form
     */
    private function updateFileFields(array &$fields, string $formName) {
        foreach ($fields as $key => $field) {
            if ($field['type'] === 'file') {
                // Add unique field name attribute
                $fields[$key]['datasets']['form-field-name'] = $formName . '-' . $key;

                // Add restrictions attributes if present in the form
                if (isset($field['validate']['required']) && $field['validate']['required']) {
                    $required = $field['validate']['required'];
                    // Set required flag when validate.required == true
                    $fields[$key]['datasets']['required'] = $required;
                    // Unset the 'real' validate.required field to prevent the Form bug #106 and the like
                    $fields[$key]['validate']['required'] = false;
                    // } else {
                    //     $required = false;
                }

                // Set the minimum number of files allowed to upload based on the new lower_limit attribute
                if (isset($fields[$key]['lower_limit'])) {
                    $min_number_of_files = $fields[$key]['lower_limit'];
                    if (is_numeric($min_number_of_files) && $min_number_of_files > 0) {
                        $fields[$key]['datasets']['minnumberoffiles'] = $min_number_of_files;
                    }
                }

                // Set the maximum number of files allowed to upload based on the limit attribute
                if (isset($fields[$key]['limit'])) {
                    $max_number_of_files = $fields[$key]['limit'];
                    if (is_numeric($max_number_of_files) && $max_number_of_files > 0) {
                        $fields[$key]['datasets']['maxnumberoffiles'] = $max_number_of_files;
                    }
                }
            } elseif (isset($field['fields'])) {
                $this->updateFileFields($fields[$key]['fields'], $formName);
            }
        }
    }
    
    /**
     * onPageInitialized
     * 
     * Add JS asset when the page contains a form including one or more file fields
     *
     * @param mixed event
     *
     * @return void
     */
    public function onPageInitialized($event)
    {
        $file_fields = $this->getFileFields();

        // If there is at least one file field add assets
        if (!empty($file_fields)) {
            $this->grav['assets']->addCss('plugin://' . basename(__DIR__) . '/assets/fixfilefield-styles.css', ['position' => 'after']);

            // if (is_array($file_fields)) {
            //     $this->grav['session']->__set('file_fields', $file_fields);
            // }

            // Add validate code inline after the inline form code
            $this->grav['assets']->addJs('plugin://' . basename(__DIR__) .
                '/assets/fixfilefield-validate.js', [
                'group' => 'bottom',
                'priority' => 10,
                // 'loading' => 'inline',
                'id' => 'plugin-fixfilefield-validate',
            ]);

            // Get active language or fallback to English
            $active_language = $this->grav['language']->getLanguage() ?: 'en';
            // Get array translations in the active language from the languages file
            $languages_file = $this->grav['locator']->findResource('plugin://fixfilefield/languages.yaml');
            $translations = Yaml::parse(file_get_contents($languages_file));

            if (!isset($translations[$active_language])) {
                // Fallback to English when the active language is missing
                $active_language = 'en';
            }

            // Create JS code containing translated strings for the current active language
            $js = $this->grav['twig']->processTemplate('translations.js.twig', ['lang' => json_encode($translations[$active_language])]);

            $this->grav['assets']->addInlineJs($js);
        }
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized()
    {
        // Don't proceed if we are in the admin plugin
        if ($this->isAdmin()) {
            return;
        }

        // Enable the main events we are interested in
        $this->enable([
            'onFormInitialized' => ['onFormInitialized', 0],
            'onPageInitialized' => ['onPageInitialized', 0],
            'onTwigLoader' => ['onTwigLoader', 0],
            'onTwigTemplatePaths' => ['onTwigTemplatePaths', 10],
        ]);
    }

    public function onTwigLoader()
    {
        $form_path = $this->grav['locator']->findResource('plugins://form') . DIRECTORY_SEPARATOR . 'templates';
        $this->grav['twig']->addPath($form_path, 'formplugin');
    }

    public function onTwigTemplatePaths()
    {
        $this->grav['twig']->twig_paths[] = __DIR__ . '/templates';
    }
}
