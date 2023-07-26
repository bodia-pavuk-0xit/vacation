<?php

namespace Drupal\puphpeteer\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Configuration extends ConfigFormBase {

  /**
   * Constructs $messenger and $config_factory objects.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    parent::__construct($config_factory);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'puphpeteer.settings',
    ];
  }

  function getFormId() {
    return 'puphpeteer_configuration';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('puphpeteer.settings')->get();

    $form['service'] = [
      '#type' => 'checkbox',
      '#title' => 'Use an external service instead of launching it ourselves',
      '#default_value' => $config['service'],
    ];

    $form['service_url'] = [
      '#type' => 'textfield',
      '#title' => 'URL to pass to puppeteer.connect\'s browserURL option',
      '#default_value' => $config['service_url'],
      '#states' => [
        'visible' => [
          ':input[name="service"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['executable_path'] = [
      '#type' => 'textfield',
      '#title' => 'Path to node',
      '#required' => TRUE,
      '#default_value' => $config['executable_path'],
      '#states' => [
        'visible' => [
          ':input[name="service"]' => ['checked' => FALSE],
        ],
      ],
    ];

    $form['idle_timeout'] = [
      '#type' => 'number',
      '#min' => 0,
      '#title' => 'Idle timeout',
      '#default_value' => $config['idle_timeout'],
      '#states' => [
        'visible' => [
          ':input[name="service"]' => ['checked' => FALSE],
        ],
      ],
    ];

    $form['read_timeout'] = [
      '#type' => 'number',
      '#min' => 0,
      '#title' => 'Read timeout',
      '#default_value' => $config['read_timeout'],
      '#states' => [
        'visible' => [
          ':input[name="service"]' => ['checked' => FALSE],
        ],
      ],
    ];

    $form['stop_timeout'] = [
      '#type' => 'number',
      '#min' => 5,
      '#title' => 'Stop timeout',
      '#default_value' => $config['stop_timeout'],
      '#states' => [
        'visible' => [
          ':input[name="service"]' => ['checked' => FALSE],
        ],
      ],
    ];

    $form['source'] = [
      '#type' => 'select',
      '#title' => 'Content source',
      '#options' => [
        'printable' => 'Normal printable rendering',
        'canonical' => 'Canonical URL for the entity',
        'print' => 'Print view mode for the entity',
      ],
      '#default_value' => $config['source'],
      '#states' => [
        'visible' => [
          ':input[name="service"]' => ['checked' => FALSE],
        ],
      ],
    ];

    $form['headless'] = [
      '#type' => 'checkbox',
      '#title' => 'Headless? (Normally on)',
      '#description' => 'To use Chrome in headful mode, you need to set DISPLAY in your PHP environment. If Chrome is in headful mode, it disables its ability to generate PDFs. PHP will wait for the browser to be closed before completing the request.',
      '#default_value' => $config['headless'],
      '#states' => [
        'visible' => [
          ':input[name="service"]' => ['checked' => FALSE],
        ],
      ],
    ];

    $form['headless_new'] = [
      '#type' => 'checkbox',
      '#title' => 'New Chrome Headless Mode (Chrome 112 on)',
      '#description' => 'Chrome 112 and later implement a new headless mode. Should we use it?',
      '#default_value' => $config['headless_new'] ?? FALSE,
      '#states' => [
        'visible' => [
          ':input[name="service"]' => ['checked' => FALSE],
          ':input[name="headless"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['debug'] = [
      '#type' => 'checkbox',
      '#title' => 'Debug?',
      '#default_value' => $config['debug'],
      '#states' => [
        'visible' => [
          ':input[name="service"]' => ['checked' => FALSE],
        ],
      ],
    ];

    $form['remote_debugging_address'] = [
      '#type' => 'textfield',
      '#title' => 'The IP address on which Chrome should listen for debugging connections',
      '#default_value' => $config['remote_debugging_address'] ?? '127.0.0.1',
    ];

    $form['remote_debugging_port'] = [
      '#min' => 1025,
      '#type' => 'number',
      '#title' => 'The port on which Chrome should listen for debugging connections',
      '#default_value' => $config['remote_debugging_port'] ?? 9920,
    ];

    $form['log_to_node_console'] = [
      '#type' => 'checkbox',
      '#title' => 'Log node console?',
      '#default_value' => $config['log_to_node_console'],
      '#states' => [
        'visible' => [
          ':input[name="service"]' => ['checked' => FALSE],
        ],
      ],
    ];

    $form['log_to_browser_console'] = [
      '#type' => 'checkbox',
      '#title' => 'Log browser console?',
      '#default_value' => $config['log_to_browser_console'],
      '#states' => [
        'visible' => [
          ':input[name="service"]' => ['checked' => FALSE],
        ],
      ],
    ];

    $form['leave_running'] = [
      '#type' => 'checkbox',
      '#title' => 'Leave the browser running when done',
      '#default_value' => $config['leave_running'],
      '#states' => [
        'visible' => [
          ':input[name="service"]' => ['checked' => FALSE],
        ],
      ],
    ];

    $form['triggerDebugging'] = [
      '#type' => 'checkbox',
      '#title' => 'Trigger JS debugging immediately? (Normally off)',
      '#default_value' => $config['triggerDebugging'],
    ];

    $form['devTools'] = [
      '#type' => 'checkbox',
      '#title' => 'Open devtools? (Normally off)',
      '#default_value' => $config['devTools'],
    ];

    $form['slowMo'] = [
      '#type' => 'number',
      '#min' => 0,
      '#title' => 'Delay to add between Pupeteer actions',
      '#default_value' => $config['slowMo'],
    ];

    $form['printBackground'] = [
      '#type' => 'checkbox',
      '#title' => 'Print background images',
      '#description' => 'Printing background images can sometimes cause PDF generation failure. This option provides a way to check whether you are experiencing that issue.',
      '#default_value' => $config['printBackground'] ?? FALSE,
    ];

    $form['pagedjs'] = [
      '#type' => 'checkbox',
      '#title' => 'Load pagedjs in Chrome?',
      '#default_value' => $config['pagedjs'],
    ];

    $form['basic_auth_username'] = [
      '#type' => 'textfield',
      '#title' => 'Basic auth username (if required)',
      '#default_value' => $config['basic_auth_username'] ?? '',
    ];

    $form['basic_auth_password'] = [
      '#type' => 'textfield',
      '#title' => 'Basic auth password',
      '#default_value' => $config['basic_auth_password'] ?? '',
    ];

    $form['default_wait'] = [
      '#title' => 'How long to wait before generating PDF',
      '#type' => 'select',
      '#options' => [
        'document_loaded' => 'Document Loaded',
        'readystate_interactive' => 'Ready state is interactive',
        'readystate_complete' => 'Ready state is complete',
        'custom_event' => 'Custom event',
        'fixed_timeout' => 'Fixed timeout',
        'custom_function' => 'A custom function that should complete when it\'s time generate the PDF',
        'xpath' => 'Wait for an XPath selection',
      ],
      '#default_value' => $config['default_wait'] ?? 'readystate_complete',
    ];

    $form['default_wait_custom_event'] = [
      '#title' => 'Custom event name',
      '#description' => 'The name of an event that can be listened for on the document DOM element',
      '#type' => 'textfield',
      '#default_value' => $config['default_wait_custom_event'] ?? '',
      '#states' => [
        'visible' => [
          ':input[name="default_wait"]' => ['value' => 'custom_event'],
        ],
      ],
    ];

    $form['default_wait_fixed_timeout'] = [
      '#title' => 'Fixed timeout',
      '#description' => 'A number of milliseconds to wait',
      '#type' => 'number',
      '#minimum' => 0,
      '#default_value' => $config['default_wait_fixed_timeout'] ?? 500,
      '#states' => [
        'visible' => [
          ':input[name="default_wait"]' => ['value' => 'fixed_timeout'],
        ],
      ],
    ];

    $form['default_wait_custom_function'] = [
      '#title' => 'Custom function',
      '#description' => 'A custom set of commands that will be invoked with eval by Chrome',
      '#type' => 'textfield',
      '#default_value' => $config['default_wait_custom_function'] ?? '',
      '#states' => [
        'visible' => [
          ':input[name="default_wait"]' => ['value' => 'custom_function'],
        ],
      ],
    ];

    $form['default_wait_xpath'] = [
      '#title' => 'XPath selector',
      '#description' => 'The selector to an XPath query to use',
      '#type' => 'textfield',
      '#default_value' => $config['default_wait_xpath'] ?? '',
      '#states' => [
        'visible' => [
          ':input[name="default_wait"]' => ['value' => 'xpath'],
        ],
      ],
    ];

    $form['help'] = [
      '#type' => 'markup',
      '#markup' => '<em>When debugging with headful Chrome, you can preview the print media version by pressing Control+Shift+P. Type "Rendering" and select Show Rendering. In the Emulate CSS media dropdown, select print.</em>',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * Check if config variable is overridden by the settings.php.
   *
   * Copied from the smtp module as it's better than what I've managed to
   * come up with but it is still imperfect - it willbe fooled if the override
   * is the same and the editable value.
   *
   * @param string $name
   *   SMTP settings key.
   *
   * @return bool
   *   Boolean.
   */
  protected function isOverridden($name) {
    $original = $this->configFactory->getEditable('puphpeteer.settings')
      ->get($name);
    $current = $this->configFactory->get('puphpeteer.settings')->get($name);
    return $original != $current;
  }

  public function validateExecutable(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    if (!$values['service']) {
      $path = $values['executable_path'];
      $dir = dirname($path);
      if (!is_dir($dir)) {
        $form_state->setErrorByName('executable_path',
          $this->t('The directory :dir does not exist', [':dir' => $dir]));
        return;
      }

      if (!is_executable($path)) {
        $form_state->setErrorByName('executable_path',
          $this->t(':path is not an executable', [':path' => $path]));
        return;
      }

      $output = '';
      $result_code = 0;
      $invoke = "{$path} -v";
      exec($invoke, $output, $result_code);

      if ($result_code) {
        $form_state->setErrorByName('executable_path',
          $this->t('Seeking to execute :path gave result code :result', [
            ':path' => $path,
            ':result' => $result_code,
          ]));
        return;
      }

      $this->messenger()
        ->addMessage($this->t('Node :version found', [
          ':version' => $output[0],
        ]));
    }
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    $this->validateExecutable($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $config = $this->configFactory->getEditable('puphpeteer.settings');
    foreach ([
               'executable_path',
               'idle_timeout',
               'read_timeout',
               'stop_timeout',
               'log_to_node_console',
               'debug',
               'log_to_browser_console',
               'headless',
               'headless_new',
               'remote_debugging_port',
               'remote_debugging_address',
               'slowMo',
               'devTools',
               'triggerDebugging',
               'source',
               'pagedjs',
               'service',
               'service_url',
               'leave_running',
               'basic_auth_username',
               'basic_auth_password',
               'printBackground',
               'default_wait',
               'default_wait_custom_event',
               'default_wait_fixed_timeout',
               'default_wait_custom_function',
               'default_wait_xpath',
             ] as $key) {
      $config->set($key, $values[$key]);
    }
    $config->save();
  }

}
