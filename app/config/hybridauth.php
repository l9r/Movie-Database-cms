<?php
return array(
	"base_url"   => url() . '/social/auth',
	"providers"  => array (
		"Google"     => array (
			"enabled"    => true,
			"keys"       => array ( "id" => "", "secret" => "" ),
			"scope"      => "https://www.googleapis.com/auth/userinfo.profile ".
				"https://www.googleapis.com/auth/userinfo.email"   ,
		),
		"Facebook"   => array (
			"enabled"    => true,
			"keys"       => array ( "id" => "895630303805547", "secret" => "94dd1f4ec2bffd87bc60689a9be3ffc3" ),
			'scope'      =>  'email',
		),
		"Twitter"    => array (
			"enabled"    => true,
			"keys"       => array ( "key" => "", "secret" => "" )
		)
	),
);