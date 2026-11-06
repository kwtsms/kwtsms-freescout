<?php

namespace Modules\KwtSms\Services;

/**
 * Sends SMS through the kwtSMS PHP library and fills message templates. Never throws: a failed SMS must not break
 * conversation handling.
 */
class Sender
{
    const VERSION = '1.0.0';

    /** @var callable|null replaces the kwtSMS client in tests: fn(array $numbers, string $message, string $sender, bool $test): array */
    public static $transport = null;

    private $username;
    private $password;
    private $sender;
    private $testMode;

    public function __construct($username, $password, $sender, $testMode)
    {
        $this->username = (string) $username;
        $this->password = (string) $password;
        $this->sender = trim((string) $sender) !== '' ? trim((string) $sender) : 'KWT-SMS';
        $this->testMode = (bool) $testMode;
    }

    public function isConfigured()
    {
        return $this->username !== '' && $this->password !== '';
    }

    /**
     * @param string|array $numbers one number, a comma separated list or an array
     * @return array the kwtSMS answer: result OK, or ERROR with code and description
     */
    public function send($numbers, $message)
    {
        $list = is_array($numbers) ? $numbers : explode(',', (string) $numbers);
        $list = array_values(array_filter(array_map('trim', $list), 'strlen'));
        if (!$this->isConfigured()) {
            return ['result' => 'ERROR', 'code' => 'NOT_CONFIGURED', 'description' => 'kwtSMS API username or password is missing'];
        }
        if (!$list) {
            return ['result' => 'ERROR', 'code' => 'NO_NUMBER', 'description' => 'No phone number'];
        }
        try {
            if (self::$transport) {
                return call_user_func(self::$transport, $list, $message, $this->sender, $this->testMode);
            }
            $client = new \KwtSMS\KwtSMS($this->username, $this->password, $this->sender, $this->testMode, '',
                'kwtsms-freescout/' . self::VERSION);

            return $client->send($list, $message);
        } catch (\Throwable $e) {
            return ['result' => 'ERROR', 'code' => 'EXCEPTION', 'description' => $e->getMessage()];
        }
    }

    /** Fill {number}, {subject} and {name}; unknown placeholders are left as typed. */
    public static function render($template, array $vars)
    {
        $map = [];
        foreach ($vars as $k => $v) {
            $map['{' . $k . '}'] = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $v)));
        }

        return strtr((string) $template, $map);
    }

    /**
     * What to send for a new conversation: a list of [who, numbers, text].
     *
     * @param array $options the kwtsms.* settings without the prefix
     */
    public static function jobsForNewConversation(array $options, $number, $subject, $customerName, $customerPhone)
    {
        $vars = ['number' => $number, 'subject' => $subject, 'name' => $customerName];
        $jobs = [];
        if (self::isOn($options['customer_new'] ?? '') && trim((string) $customerPhone) !== '') {
            $jobs[] = ['customer', trim((string) $customerPhone), self::render($options['customer_message'] ?? '', $vars)];
        }
        if (self::isOn($options['agent_new'] ?? '') && trim((string) ($options['agent_numbers'] ?? '')) !== '') {
            $jobs[] = ['agent', $options['agent_numbers'], self::render($options['agent_message'] ?? '', $vars)];
        }

        return array_values(array_filter($jobs, function ($job) {
            return trim($job[2]) !== '';
        }));
    }

    public static function isOn($value)
    {
        return in_array((string) $value, ['on', '1', 'true'], true);
    }
}
