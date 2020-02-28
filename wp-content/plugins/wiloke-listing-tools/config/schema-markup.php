<?php
return [
	'listing' => array(
		'@context' => 'http://schema.org/',
		'@type'    => 'LocalBusiness',
		'name'     => '{{postTitle}}',
		'image'    => '{{coverImg}}',
		'description' => '{{postExcerpt}}',
		'address' => array(
			'addressLocality' => '{{listing_location}}',
			'streetAddress'   => '{{googleAddress}}'
		),
		'aggregateRating' => array(
			'@type'         => 'aggregateRating',
			'ratingValue' => '{{averageRating}}',
			'reviewCount' => '{{reviewCount}}',
			'bestRating' => '{{bestRating}}',
			'worstRating' => '{{worstRating}}'
		),
		'review' => '{{reviewDetails}}',
		'geo' => array(
			'@type' => 'GeoCoordinates',
			'latitude' => '{{latitude}}',
			'longitude' => '{{longitude}}'
		),
		'sameAs' => '{{socialNetworks}}',
		'telephone'  => '{{telephone}}',
		'photos' => '{{photos}}',
		'priceRange' => '{{priceRange}}',
		'email' => '{{email}}'
	),
	'event' => array(
		'@context' => 'http://schema.org/',
		'@type'    => 'Event',
		'name'     => '{{postTitle}}',
		'image'    => '{{coverImg}}',
		'description' => '{{postExcerpt}}',
		'address' => array(
			'addressLocality' => '{{listing_location}}',
			'streetAddress'   => '{{googleAddress}}'
		),
		'sameAs' => '{{socialNetworks}}',
		'telephone'  => '{{telephone}}',
		'photos' => '{{photos}}',
		'priceRange' => '{{priceRange}}',
		'email' => '{{email}}',
		'startDate' => '{{eventStartDate}}',
		'endDate' => '{{eventEndDate}}',
	),
];