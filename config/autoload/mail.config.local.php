<?php

return array(
	'mail' => array(
		'transport' => array(
			'options' => array(
				#'host' => 'localhost',
				'host' => 'smtp.sendgrid.net',
				'connection_class'  => 'plain',
				'port' => '587',
				//'port' => 25,
				'connection_config' => array(
					'username' => 'eightyco',
					'password' => 'sendgrid3ightyc0',
					'ssl' => 'tls'
				),
			),
		),
	),
);
