<?php

namespace Modules\KwtSms\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\KwtSms\Services\Sender;

class KwtSmsServiceProvider extends ServiceProvider
{
    const SECTION = 'kwtsms';

    const OPTIONS = [
        'username', 'password', 'sender', 'test_mode',
        'customer_new', 'customer_message', 'agent_new', 'agent_numbers', 'agent_message',
    ];

    protected $defer = false;

    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/../Config/config.php', 'kwtsms');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'kwtsms');
        $this->hooks();
    }

    public function register()
    {
    }

    public function hooks()
    {
        \Eventy::addFilter('settings.sections', function ($sections) {
            $sections[self::SECTION] = ['title' => 'kwtSMS', 'icon' => 'phone', 'order' => 600];

            return $sections;
        }, 30);

        \Eventy::addFilter('settings.section_settings', function ($settings, $section) {
            if ($section != self::SECTION) {
                return $settings;
            }
            $settings = \Option::getOptions(array_map(function ($o) {
                return 'kwtsms.' . $o;
            }, self::OPTIONS));
            // The saved password is never sent back to the browser: asterisks mean "keep the saved one".
            $settings['kwtsms.password'] = $settings['kwtsms.password'] ? str_repeat('*', 10) : '';

            return $settings;
        }, 20, 2);

        \Eventy::addFilter('settings.section_params', function ($params, $section) {
            if ($section != self::SECTION) {
                return $params;
            }

            return [
                'template_vars' => [],
                'validator_rules' => [],
                // Core handling: stored encrypted, asterisks are not saved over the real password.
                'settings' => [
                    'kwtsms.password' => ['safe_password' => true, 'encrypt' => true],
                ],
            ];
        }, 20, 2);

        \Eventy::addFilter('settings.view', function ($view, $section) {
            return $section == self::SECTION ? 'kwtsms::settings' : $view;
        }, 20, 2);

        \Eventy::addAction('conversation.created_by_customer', function ($conversation, $thread, $customer) {
            self::onNewConversation($conversation, $customer);
        }, 20, 3);
    }

    public static function options()
    {
        $values = [];
        foreach (self::OPTIONS as $o) {
            $values[$o] = \Option::get('kwtsms.' . $o, config('kwtsms.options.' . $o . '.default'));
        }
        try {
            $values['password'] = $values['password'] !== '' ? decrypt($values['password']) : '';
        } catch (\Throwable $e) {
            $values['password'] = '';
        }

        return $values;
    }

    public static function onNewConversation($conversation, $customer)
    {
        $o = self::options();
        $sender = new Sender($o['username'], $o['password'], $o['sender'], Sender::isOn($o['test_mode']));
        if (!$sender->isConfigured()) {
            return;
        }
        $jobs = Sender::jobsForNewConversation($o, $conversation->number, $conversation->subject,
            $customer ? $customer->getFullName(true) : '', $customer ? $customer->getMainPhoneNumber() : '');
        foreach ($jobs as $job) {
            list($who, $numbers, $text) = $job;
            $result = $sender->send($numbers, $text);
            if (($result['result'] ?? '') !== 'OK') {
                \Log::warning(sprintf('[kwtSMS] new conversation SMS (%s) for #%s failed: %s %s', $who,
                    $conversation->number, $result['code'] ?? '', $result['description'] ?? ''));
            }
        }
    }
}
