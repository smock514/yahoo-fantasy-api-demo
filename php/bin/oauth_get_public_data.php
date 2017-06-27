<?php
/**
 * Update this script to include your client ID and secret. You can also
 *  pass them along as command line arguments.
 */
$client_id = 'INSERT_CLIENT_ID_HERE';
$client_secret = 'INSERT_CLIENT_SECRET_HERE';

// See if we want to pull client info from command line
if (count($argv) == 3) {
    print "Setting client info from command line.\n";
    $client_id = $argv[1];
    $client_secret = $argv[2];
}

// Want to go through OAuth1.0 flow to get public data -- we only support
//  OAuth2 for private, cookied data and 3-legged auth, so non-cookied data 
//  needs OAuth1 (if you don't want to interact w/ the login servers)

// When doing 2-legged OAuth, there is no access token or secret.
$access_token = '';
$access_token_secret = '';

print "Consumer key:\n" . $client_id . "\n\n";
print "Consumer secret:\n" . $client_secret . "\n\n";
print "Access token:\n" . $access_token . "\n\n";
print "Access token secret:\n" . $access_token_secret . "\n\n";

$method = 'GET';
$game_code = 'nfl';
$url = 'https://fantasysports.yahooapis.com/fantasy/v2/games;game_keys=' . $game_code  . '/players;count=5';

// Build OAuth params
$oauth_consumer_key = $client_id;
$oauth_nonce = rand(0, 1000000);
$oauth_signature_method = 'HMAC-SHA1';
$oauth_timestamp = time();
//$oauth_token = $access_token;
$oauth_version = '1.0';

$params =  '';
$params .= 'oauth_consumer_key=' . urlencode($oauth_consumer_key);
$params .= '&oauth_nonce=' . urlencode($oauth_nonce);
$params .= '&oauth_signature_method=' . urlencode($oauth_signature_method);
$params .= '&oauth_timestamp=' . urlencode($oauth_timestamp);
//$params .= '&oauth_token=' . urlencode($oauth_token);
$params .= '&oauth_version=' . urlencode($oauth_version);

$base_string = urlencode($method) . '&' . urlencode($url) . '&' .
  urlencode($params);

print 'Base string: ' . $base_string . "\n\n";

$secret = urlencode($client_secret) . '&' . urlencode($access_token_secret);
$signature = base64_encode(hash_hmac('sha1', $base_string, $secret, true));

print 'Secret: ' . $secret . "\n\n";
print 'Signature: ' . $signature . "\n\n";

$final_url = $url . '?' . $params . '&oauth_signature=' . urlencode($signature);

print 'Final URL: ' . $final_url . "\n\n";


// Try to make actual request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $final_url);
curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
$timeout = 2;
curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

$result = curl_exec($ch);
$ret_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$errno = curl_errno($ch);
$error_str = curl_error($ch);

if ($errno || $error_str) {
    print "Error: ${error_str} (${errno})\n";
}

//print_r($result);
if ($ret_code != 200) {
    print "Non-200 response: ${ret_code}\n";
    exit(1);
}


// Extract NFL players
$fantasy_response = new SimpleXMLElement($result);

$fantasy_players = array();
foreach ($fantasy_response->games->game as $game) {
    foreach ($game->players->player as $player) {
        if ($player->player_key) {
            $player_data = array('key' => $player->player_key,
                                 'name' => $player->name->full);
            $fantasy_players[] = $player_data;
        }
    }
}

$num_players = count($fantasy_players);

print "\n";
if ($num_players == 0) {
    print "Could not find any players for ${game_code}.\n";
} else {
    print "Here are the first ${num_players} players for ${game_code}.\n";
    foreach ($fantasy_players as $player_data) {
        $player_key = $player_data['key'];
        $player_name = $player_data['name'];

        print " - ${player_name} (${player_key})\n";
    } 
}


?>