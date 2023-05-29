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

Note the payload is a POST request and contains the contents in the body.

## Credits
The plugin is maintained by [Tim Nash](https://timnash.co.uk), however let's face it its about 5 lines of original code while the majority has come from copying Streams built in alert types.  You can view [all thier contributors](https://github.com/xwp/stream/graphs/contributors) and cheer them on.