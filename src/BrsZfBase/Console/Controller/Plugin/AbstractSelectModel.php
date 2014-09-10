<?php

/**
 * (c) BRS software - Tomasz Borys <t.borys@brs-software.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BrsZfBase\Console\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Console\Prompt;
use Zend\Console\ColorInterface as Color;
use BrsZfSloth\Exception\NotFoundException;
use __;
use BrsZfBase\Exception;

/**
 * @author Tomasz Borys <t.borys@brs-software.pl>
 * @version 1.0 2014-04-18
 */
abstract class AbstractSelectModel extends AbstractPlugin
{
    protected $config = [];

    abstract protected function getConfiguration();

    public function __construct()
    {
        $this->config = array_merge([
            // mandatory config
            // 'repository' => 'module.model.repository',
            // 'list_select_fn' => function ($select, $conv, $options) {},
            // 'get_model_fn' => function ($repository, $options) {},

            // optional config
            'invalid_model_id_message' => 'Invalid model ID',
            'list_text_fn' => function ($model, $options) {
                return sprintf('%s) model %s', str_pad($model->getId(), 3, ' ', STR_PAD_LEFT), $model->getId());
            },
            'list_select_message' => 'Select a model from below list:',
            'list_enter_message' => 'Enter model ID:',
            'list_notfound_message' => 'Invalid ID, try again...',
        ], $this->getConfiguration());
    }

    public function hasConfig($key)
    {
        return array_key_exists($key, $this->config);
    }

    public function getConfig($key = null)
    {
        if ($key) {
            if ($this->hasConfig($key)) {
                return $this->config[$key];
            }
            throw new Exception\RuntimeException(
                sprintf('config key "%s" not found in %s', $key, get_class($this))
            );
        }
        return $this->config;
    }



    public function __invoke(array $options = [])
    {
        $options = __::defaults($options, [
            'id' => null
        ]);

        $sm = $this->getController()->getServiceLocator();
        $console = $sm->get('console');
        $repo = $sm->get($this->getConfig('repository'));

        if ($this->hasConfig('get_model_fn')) {
            $getModelFn = $this->getConfig('get_model_fn');
            try {
                $model = $getModelFn($repo, $options);

            } catch (NotFoundException $e) {
                $console->error($e->getMessage());
            }
            if ($model) {
                return $model;
            }
        }

        if ($options['id']) {
            try {
                return $repo->get('id', (int) $options['id']);
            } catch (NotFoundException $e) {
                $console->error($this->getConfig('invalid_model_id_message'));
            }
        } elseif ($this->hasConfig('list_select_fn')) {
            $list = array_map_closure(
                $repo->fetch(function($s, $c) use ($options) {
                    $selFn = $this->getConfig('list_select_fn');
                    $selFn($s, $c, $options);
                }),
                $this->getConfig('list_text_fn')
            );
            $console->writeLine($this->getConfig('list_select_message') . "\n" . implode("\n", $list), Color::MAGENTA);

            while (true) {
                try {
                    return $repo->get('id', (new Prompt\Number($this->getConfig('list_enter_message')))->show());

                } catch (NotFoundException $e) {
                    $console->writeWarningLine($this->getConfig('list_notfound_message'));
                }
            }
        } else {
            $console->error("Couldn't get a model");
        }
    }
}
