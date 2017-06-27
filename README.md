# yahoo-fantasy-api-demo
Yahoo! Fantasy Sports API demo using OAuth APIs

This repo will provides starter scripts and links to relevant documentation for interacting with the Yahoo! Fantasy Sports YDN APIs.

## Documentation
The public documentation is somewhat outdated and still tries to link to OAuth1.0 guides, which is no longer supported. You can still use the public documentation to understand basic API structure, but you'll need to use the OAuth2 guide for authorization.

* [Public YDN Fantasy APIs](https://developer.yahoo.com/fantasysports/guide/)
* [OAuth2 documentation](https://developer.yahoo.com/oauth2/guide/)

## OAuth2 Flow

* [Detailed instructions for using OAuth2](https://git.corp.yahoo.com/sports/full-fantasy-api-demo/wiki/OAuth2-Flow)

## Endpoints

* **OAuth2 endpoint**: `https://fantasysports.yahooapis.com/fantasy/v2/...`

## Examples

### OAuth2 PHP Scripts
This script demonstrates a very basic OAuth flows in PHP. They do not have any external dependencies, aside from PHP and Curl.

#### Get User's Teams

* [OAuth2 PHP Script](https://git.corp.yahoo.com/sports/full-fantasy-api-demo/blob/master/php/bin/oauth_get_teams.php)

#### Get Game Information (public)

* [OAuth1.0 PHP Script for public data](https://git.corp.yahoo.com/sports/full-fantasy-api-demo/blob/master/php/bin/oauth_get_public_data.php)
