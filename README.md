# yahoo-fantasy-api-demo
Yahoo! Fantasy Sports API demo using OAuth APIs

This repo will provides starter scripts and links to relevant documentation for interacting with the Yahoo! Fantasy Sports YDN APIs.

## Documentation
The public documentation is somewhat outdated and still tries to link to OAuth1.0 guides, which is no longer supported. You can still use the public documentation to understand basic API structure, but you'll need to use the OAuth2 guide for authorization.

* [Public YDN Fantasy APIs](https://developer.yahoo.com/fantasysports/guide/)
* [OAuth2 documentation](https://developer.yahoo.com/oauth2/guide/)

## OAuth2 Flow

* [Detailed instructions for using OAuth2](https://github.com/smock514/yahoo-fantasy-api-demo/wiki/OAuth2-Flow)

## Endpoints

* **OAuth2 endpoint**: `https://fantasysports.yahooapis.com/fantasy/v2/...`

## Examples

### OAuth2 PHP Scripts
This script demonstrates a very basic OAuth flows in PHP. They do not have any external dependencies, aside from PHP and Curl.

#### Get User's Teams

* [OAuth2 PHP Script](https://github.com/smock514/yahoo-fantasy-api-demo/blob/master/php/bin/oauth_get_teams.php)

##### Usage
Make sure to have your `CONSUMER_KEY` and `CONSUMER_SECRET` handy.

```
% php oauth_get_teams.php <CONSUMER_KEY> <CONSUMER_SECRET> [game_keys]
```

* `CONSUMER_KEY`: Your consumer key from your YDN application (set up as described in the [OAuth2 Flow](https://github.com/smock514/yahoo-fantasy-api-demo/wiki/OAuth2-Flow))
* `CONSUMER_SECRET`: Corresponding consumer secret
* `game_keys`: CSV of game codes or IDs.
   * Examples: `nfl`, `nba,mlb,nfl`, `370`

#### Get Public Players Details

* [OAuth1.0 PHP Script for public data](https://github.com/smock514/yahoo-fantasy-api-demo/blob/master/php/bin/oauth_get_public_data.php)

##### Usage
Just run the script with your keys. It'll fetch public NFL players details.

```
% php oauth_get_public_data.php <CONSUMER_KEY> <CONSUMER_SECRET>
```

* `CONSUMER_KEY`: Your consumer key from your YDN application (set up as described in the [OAuth2 Flow](https://github.com/smock514/yahoo-fantasy-api-demo/wiki/OAuth2-Flow))
* `CONSUMER_SECRET`: Corresponding consumer secret
