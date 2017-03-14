<?php
return [
	//draugiem.lv
	'dr_app_id'        => '',
	'dr_app_key'       => '',

	//twitter
	'twt_app_key'      => 'C7hCbXxL6CtnHY2YQsJA',
	'twt_app_secret'   => 'CCiazoP4A6Lng3NXvcuiBcMzYFBGhd865ulYCg3bR8',
	'twt_user'         => 'latvijas_pasts',

	//facebook
	'fb_app_id'        => '271595409644317',
	'fb_app_secret'    => '9df31f75a5a9134100975f8c39c4ca63',
	'fb_page_url'      => '',

	//instagram
	'insta_app_key'    => '',
	'insta_app_secret' => '',

	'google_maps_api_key' => 'AIzaSyAgFqe-gF5G9zxJLY6ihG5BV7v8_ySlDs0',

	'messages' => [
		0  => [ 'text' => "Bonjour, hola, salaam, sveicināti. Es esmu KOKOšteins, un Koko mani ir radījis ar mērķi atrast Click Praktikantu no Nākotnes🚀.\nKādā uzdevumā šeit esi Tu?" ],
		1  => [ 'text' => "Oho, man sistēmā no tā gandrīzi estājās errors, pastāstīšu arī meistaram Koko. Tad pastāsti vairāk - kas ir foršākais, ko Tu šogad esi izdarījis un ar ko lepojies?" ],
		2  => [ 'text' => 'Es personīgi šogad atrisināju dzīves jēgas mistēriju, bet ko nu par to… Kādas izglītības programmas Tev ir uzinstalētas?' ],
		3  => [
			'text' => 'Jā, mans meistars Koko arī man iemācīja šo to par dzīvi...
Vai Tu esi savas 💡 paspējis notestēt/izmēģināt kādā darba vietā vai praksē?'
		],
		4  => [ 'text' => 'Ierakstīšu šo atbildi Tavā failā. Labi?' ],
		5  => [
			'text'       => 'Tādā gadījumā, izvēlies vienu no bildēm, kas raksturotu Tavu ideālo darba dienu.',
			'attachment' => [
				[
					'type'    => 'template',
					'payload' => [
						'template_type' => 'generic',
						'elements'      => [
							[
								'title'     => 'Neapgrūtinu sevi ar darbu',
								'image_url' => URL::base() . 'assets/img/kokostein_a.jpg',
								"buttons"   => [
									[
										"type"    => "postback",
										"title"   => "Neapgrūtinu sevi ar darbu",
										"payload" => "fifth:chosen:A"
									]
								]
							],
							[
								'title'     => 'Esmu biroja pirmrindnieks!',
								'image_url' => URL::base() . 'assets/img/kokostein_b.jpg',
								"buttons"   => [
									[
										"type"    => "postback",
										"title"   => "Esmu biroja pirmrindnieks!",
										"payload" => "fifth:chosen:B"
									]
								]
							],
							[
								'title'     => 'Mani darbs iedvesmo',
								'image_url' => URL::base() . 'assets/img/kokostein_c.jpg',
								"buttons"   => [
									[
										"type"    => "postback",
										"title"   => "Mani darbs iedvesmo",
										"payload" => "fifth:chosen:C"
									]
								]
							]
						]
					]
				]
			],
		],
		6  => [ 'text' => 'Izskatās, ka Tu esi visai piemērots, lai sāktu karjeru reklāmas industrijā. Atsūti, lūdzu, savu epastu un telefona numuru – ja saimniekiem neuzmetīsies 404 errors, gaidi ziņu par interviju! Vai Tu vēl kaut ko vēlētos piebilst?', ],
		7  => [ 'text' => 'Uber! Priecājos ar Tevi iepazīties, mazais cilvēk. Lai Koko miers ir ar Tevi, atā!', ],
		8  => null,
		9  => [
			'text' => [
				'ERROR ERROR ERROR',
				'Labi, nav error, bet man vairs nav tev jautājumu.'
			],
		],
		10 => [
			'text' => [
				'ERROR ERROR ERROR',
				'Labi, nav error, bet man apnika ar tevi runāt.'
			],
		],
	]

];

/**
 * message: {
 * attachment: {
 * type: "template",
 * payload: {
 * template_type: "generic",
 * elements: [{
 * title: "rift",
 * subtitle: "Next-generation virtual reality",
 * item_url: "https://www.oculus.com/en-us/rift/",
 * image_url: "http://messengerdemo.parseapp.com/img/rift.png",
 * buttons: [{
 * type: "web_url",
 * url: "https://www.oculus.com/en-us/rift/",
 * title: "Open Web URL"
 * }, {
 * type: "postback",
 * title: "Call Postback",
 * payload: "Payload for first bubble",
 * }],
 * }, {
 * title: "touch",
 * subtitle: "Your Hands, Now in VR",
 * item_url: "https://www.oculus.com/en-us/touch/",
 * image_url: "http://messengerdemo.parseapp.com/img/touch.png",
 * buttons: [{
 * type: "web_url",
 * url: "https://www.oculus.com/en-us/touch/",
 * title: "Open Web URL"
 * }, {
 * type: "postback",
 * title: "Call Postback",
 * payload: "Payload for second bubble",
 * }]
 * }]
 * }
 * }
 * }
 */