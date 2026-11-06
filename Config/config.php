<?php

return [
    'name' => 'KwtSms',
    'options' => [
        'username' => ['default' => ''],
        'password' => ['default' => ''],
        'sender' => ['default' => 'KWT-SMS'],
        'test_mode' => ['default' => ''],
        'customer_new' => ['default' => ''],
        'customer_message' => ['default' => 'We received your message #{number}: {subject}. We will reply soon.'],
        'agent_new' => ['default' => ''],
        'agent_numbers' => ['default' => ''],
        'agent_message' => ['default' => 'New conversation #{number} from {name}: {subject}'],
    ],
];
