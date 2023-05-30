# Stream Webhook Alert Type
**Provides a simple webhook to send alerts to third parties such as nfty/telegram etc**

## Installation
Note this plugin extends XWP [Stream](https://wordpress.org/plugins/stream) as such this **must** be active for this to do anything. If steam is not active the plugin will sit in your plugins folder feeling sad for itself.

As this plugin is only available on Github you can install it via WP-CLI
```
wp plugin install https://github.com/timnashcouk/stream-alert-webhook/releases/latest/download/stream-alert-webhook.zip --activate
```
Alternatively you can manually download from [Github]( https://github.com/timnashcouk/stream-alert-webhook/releases/latest/) and upload the zip through the admin interface.

## Setup
Once activated, navigate to `Stream -> Alerts -> Add New` a within the `alert me by` dropdown select "Webhook" 
The webhook has a single configurable field "Webhook URL" this is the full URL of where you want to send the payload.

You may select between HTTP Methods
- **GET** sends the payload as a URLEncoded String appended to the Webhook
- **GET(Simple)** sends the payload as a URLEncoded string, but only the sitename and details, not full payload
- **POST** sends the payload as a JSON encoded content in the body

## Examples

### Telegram Bot
Set the webhook to be:
```
https://api.telegram.org/bot{BOT_ID}}/sendMessage?chat_id={CHAT_ID}&text=
```
replacing the {BOT_ID} and {CHAT_ID} strings, for details see [Telegram Bot Tutorial](https://core.telegram.org/bots/tutorial) 

The method should be `Get Simple`
Saving will result in the message:

```
[site name] "Any User > Any Context > Any Action" alert updated
```
In the Telegram channel your bot is linked to.

## Actions and Filters
The plugin has a couple of filters that can help to change the message being sent.

**wp_stream_alert_webhook_data** - Allows you to modify the data being sent, you can remove and add fields, or re-order/rename fields as it might suit. 

**wp_stream_alert_webhook_request_url** - allows you to edit the URL (GET & GET (Simple)) string after it has been built, this might be useful when you need to add additional parameters to the URL where the position in the URL is important.

## Changelog

** V1.1 - May 30th, 2023**
- Added the option to send GET requests, not just POST requests
- Added filters to manipulate the data being sent by the alert
- Added a GET Simple option for sending very simple messages

** V1.0 - May 29th, 2023**
- Initial Release onto Github

## Credits
The plugin is maintained by [Tim Nash](https://timnash.co.uk), however let's face it its about 5 lines of original code while the majority has come from copying Streams built in alert types.  You can view [all thier contributors](https://github.com/xwp/stream/graphs/contributors) and cheer them on.