<?php
/**
 * Unit tests for Services/Sender.php (no FreeScout needed, no network). Numbers use country code 999 (unassigned).
 *
 * Usage: php tests/run.php
 */

require __DIR__ . '/../Services/Sender.php';

use Modules\KwtSms\Services\Sender;

$fails = 0;
$count = 0;
function check($name, $cond)
{
    global $fails, $count;
    $count++;
    if (!$cond) {
        $fails++;
        echo "FAIL $name\n";
    }
}

$calls = [];
Sender::$transport = function ($numbers, $message, $sender, $test) use (&$calls) {
    $calls[] = compact('numbers', 'message', 'sender', 'test');

    return ['result' => 'OK'];
};

check('render', Sender::render('#{number} {subject} {name} {x}', ['number' => 42, 'subject' => "<p>Hi</p>\n there", 'name' => 'Sara'])
    === '#42 Hi there Sara {x}');

$s = new Sender('', '', '', false);
check('not configured', $s->send('99912345678', 'x')['code'] === 'NOT_CONFIGURED');
$s = new Sender('freescout_username', 'freescout_password', ' ', true);
check('no number', $s->send(', ,', 'x')['code'] === 'NO_NUMBER');
check('ok', $s->send('99912345678, 99912345679', 'x')['result'] === 'OK');
check('split', $calls[0]['numbers'] === ['99912345678', '99912345679']);
check('default sender', $calls[0]['sender'] === 'KWT-SMS');
check('test flag', $calls[0]['test'] === true);

Sender::$transport = function () {
    throw new RuntimeException('boom');
};
check('exception caught', $s->send('99912345678', 'x')['code'] === 'EXCEPTION');

$opts = [
    'customer_new' => 'on', 'customer_message' => 'Got #{number}: {subject}',
    'agent_new' => 'on', 'agent_numbers' => '99912345678,99912345679', 'agent_message' => 'New #{number} from {name}',
];
$jobs = Sender::jobsForNewConversation($opts, 77, 'VPN down', 'Sara', '+999 1234 5670');
check('two jobs', count($jobs) === 2);
check('customer job', $jobs[0] === ['customer', '+999 1234 5670', 'Got #77: VPN down']);
check('agent job', $jobs[1] === ['agent', '99912345678,99912345679', 'New #77 from Sara']);
check('no phone, no customer SMS', count(Sender::jobsForNewConversation($opts, 1, 's', 'n', '')) === 1);
check('off by default', Sender::jobsForNewConversation([], 1, 's', 'n', '+999 1234 5670') === []);
check('agent on, no numbers', Sender::jobsForNewConversation(['agent_new' => 'on'], 1, 's', 'n', '') === []);
check('empty template skipped', Sender::jobsForNewConversation(['customer_new' => 'on', 'customer_message' => ' '], 1, 's', 'n', '1') === []);
check('isOn', Sender::isOn('on') && Sender::isOn('1') && !Sender::isOn('') && !Sender::isOn('off'));

echo $fails ? "FAILED $fails of $count\n" : "OK $count tests\n";
exit($fails ? 1 : 0);
