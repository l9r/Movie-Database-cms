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
			"keys"       => array ( "id" => "895628110472433", "secret" => "1806e4f0b13f12ea8c7f80f88c2e4374" ),
			'scope'      =>  'email',
			),
		"Twitter"    => array (
			"enabled"    => true,
			"keys"       => array ( "key" => "", "secret" => "" )
			)
	),
);