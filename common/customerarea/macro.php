<?php

require '../../vendor/autoload.php';
include "../config/common_cfg.php";
include "../param.php";

/*$stripe = new \Stripe\StripeClient(
  'rk_test_51H8fNKHGzhgYgqhx6ZT58UckmFiG4ou5d6Mr9LJnhGvaHCBcXzoqWs9bjHUZKvAt5FZ7Cq5PGAxGJigiLq2EcVMG00Ge3QSWFV'
);

$stripe->prices->update(
  'price_1JU62HHGzhgYgqhxl7yDJ1K4',
  ['lookup_key' => '']
);

$stripe->prices->update(
  'price_1JIqwkHGzhgYgqhx0BG5qjgp',
  ['lookup_key' => '']
);

$stripe->prices->update(
  'price_1JfhYaHGzhgYgqhxGt3ju6uR',
  ['lookup_key' => 'pb_fixe']
);

$stripe->prices->update(
  'price_1JfhOAHGzhgYgqhxVZXpNb6g',
  ['lookup_key' => 'pb_conso']
);*/

/*
$stripe = new \Stripe\StripeClient(
  'sk_test_51H8fNKHGzhgYgqhxXKxXLCKqGMGaHXXfQ3AedURHAd2BTaNjr07L7wLHVZP41UMNWnxRHt4R7XTdeydg0GWcUXL400QA2swxxl'
);

$stripe->prices->all(['lookup_keys' => 'pb_conso']);
*/

$stripe = new \Stripe\StripeClient(
  'sk_test_51H8fNKHGzhgYgqhxXKxXLCKqGMGaHXXfQ3AedURHAd2BTaNjr07L7wLHVZP41UMNWnxRHt4R7XTdeydg0GWcUXL400QA2swxxl'
);

print_r($stripe->prices->retrieve(
  'price_1JfhOAHGzhgYgqhxVZXpNb6g',
  []
));

print_r($stripe->prices->all(['limit' => '3']));

?>


