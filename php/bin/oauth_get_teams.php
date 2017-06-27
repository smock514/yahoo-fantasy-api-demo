<?php
/**
 * Update this script to include your client ID and secret. You can also
 *  pass them along as command line arguments.
 */
$client_id = 'INSERT_CLIENT_ID_HERE';
$client_secret = 'INSERT_CLIENT_SECRET_HERE';
$game_keys = 'nfl';

// See if we want to pull client info from command line
if (count($argv) >= 3) {
    print "Setting client info from command line.\n";
    $client_id = $argv[1];
    $client_secret = $argv[2];
}

if (count($argv) >= 4) {
    print "Setting game keys from command line.\n";
    $game_keys = $argv[3];
}

$fantasy_url = "https://fantasysports.yahooapis.com/fantasy/v2/users;use_login=1/games;game_keys=${game_keys}/teams";

print "Consumer key:\n" . $client_id . "\n\n";
print "Consumer secret:\n" . $client_secret . "\n\n";


// Construct authorization URL
$auth_url_base = 'https://api.login.yahoo.com/oauth2/request_auth';
$auth_url_params = array('client_id' => $client_id,
                         'redirect_uri' => 'oob',
                         'response_type' => 'code');
$auth_url = $auth_url_base . '?' . http_build_query($auth_url_params);

print "Auth URL: ${auth_url}\n\n";

// Send user to authorization URL to retrive authorization code
print "Please go to the above URL, authorize this app, and enter the code here: ";
$auth_code = rtrim(fgets(STDIN));
if (!$auth_code) {
    print "Invalid auth code\n";
    exit(1);
}

// Get access token based on authorization code
$token_url_base = 'https://api.login.yahoo.com/oauth2/get_token';
$token_url_params = array('client_id' => $client_id,
                          'client_secret' => $client_secret,
                          /*'redirect_uri' => 'oob',
                          'code' => $auth_code,
                          'grant_type' => 'authorization_code'*/);
$token_url = $token_url_base . '?' . http_build_query($token_url_params);

print "\nToken URL:\n" . $token_url . "\n\n";

$auth_header_raw = $client_id . ':' . $client_secret;
$auth_header_base64 = base64_encode($auth_header_raw);
$content_type = 'application/x-www-form-urlencoded';

$curl_headers = array();
$curl_headers[] = "Authorization: Basic ${auth_header_base64}";
$curl_headers[] = "Content-Type: ${content_type}";

print "Authorization:\n Basic " . $auth_header_base64 . "\n\n";
print "Content-Type:\n" . $content_type . "\n\n";

$request_body_params = array('grant_type' => 'authorization_code',
                             'redirect_uri' => 'oob',
                             'code' => $auth_code);
$request_body = http_build_query($request_body_params);

print "Request body:\n" . $request_body . "\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_headers);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body);
curl_setopt($ch, CURLOPT_URL, $token_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);
$ret_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

print_r($result);
if ($ret_code != 200) {
    print "Non-200 response: ${ret_code}\n";
    exit(1);
}

$result_hash = json_decode($result, true);
$access_token = $result_hash['access_token'];
$token_type = $result_hash['token_type'];
$refresh_token = $result_hash['refresh_token'];
$guid = $result_hash['xoauth_yahoo_guid'];

// Try to make actual request

$curl_headers = array();
$curl_headers[] = "Authorization: Bearer ${access_token}";

print "\nTrying to make actual request to ${fantasy_url}\n";
print "Access token:\n" . $access_token . "\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $fantasy_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
$timeout = 5;
curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

$result = curl_exec($ch);
$ret_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$errno = curl_errno($ch);
$error_str = curl_error($ch);

if ($errno || $error_str) {
    print "Error: ${error_str} (${errno})\n";
}

print_r($result);
if ($ret_code != 200) {
    print "Non-200 response: ${ret_code}\n";
    exit(1);
}

// Extract NFL teams
$fantasy_response = new SimpleXMLElement($result);

$fantasy_teams = array();
foreach ($fantasy_response->users->user as $user) {
    foreach ($user->games->game as $game) {
        foreach ($game->teams->team as $team) {
            if ($team->team_key) {
                $team_data = array('key' => $team->team_key,
                                   'name' => $team->name,
                                   'url' => $team->url);
                $fantasy_teams[] = $team_data;
            }
        }
    }
}

$num_teams = count($fantasy_teams);

print "\n";
if ($num_teams == 0) {
    print "User ${guid} does not have any ${game_keys} teams.\n";
} else {
    $team_plural = ($num_teams == 1 ? 'team' : 'teams');
    print "User ${guid} has ${num_teams} ${game_keys} ${team_plural}.\n";
    foreach ($fantasy_teams as $team_data) {
        $team_key = $team_data['key'];
        $team_name = $team_data['name'];
        $team_url = $team_data['url'];

        print " - ${team_name} (${team_key})\n   - ${team_url}\n";
    } 
}
 
?>
