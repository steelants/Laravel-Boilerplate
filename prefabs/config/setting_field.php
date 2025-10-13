<?php

use App\Types\SettingDataType;

return [
	'main' => [ //Slug
		'general' => [ //Section 1
			'default' => [
				'type'  => SettingDataType::INT,
				'rules' => 'required',
				'help'  => 'Help',
			],
		],
	],
];
