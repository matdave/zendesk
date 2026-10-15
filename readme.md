# Zendesk

This package includes a FormIt hook for creating and updating tickets in Zendesk. 

Signup for a [Zendesk account here](https://www.zendesk.com/).

## Authentication

This package authenticates with the Zendesk API using the [OAuth client credentials flow](https://developer.zendesk.com/documentation/authentication/oauth-migration/#client-credentials-grant). It is intended for server-to-server automation with no user interaction, so no refresh token is issued — a new access token is requested automatically whenever the current one expires.

### Configure the OAuth client

Create an OAuth client in Zendesk Admin Center under **Apps and integrations > APIs > OAuth clients**.

> **The client type must be set to `Confidential`.** The client credentials flow only works with confidential (server-side) clients.

After saving, note the **Identifier** and **Secret** — the Secret is shown only once. Actions performed with a client credentials token are attributed to the Zendesk user who created the OAuth client, typically an admin.

## System Settings

| Key                   | Description                                                                         |
|-----------------------|-------------------------------------------------------------------------------------|
| zendesk.domain        | The prefix domain of your Zendesk account, e.g. {{domain}}.zendesk.com              |
| zendesk.client_id     | The Identifier of your Zendesk OAuth client                                         |
| zendesk.client_secret | The Secret of your Zendesk OAuth client                                             |
| zendesk.scope         | The OAuth scopes requested when obtaining an access token. Defaults to `read write` |

## Hook Options

| Option               | Default                     | Description                                                  |
|----------------------|-----------------------------|--------------------------------------------------------------|
| zdAllowNewTicket     | true                        | Allows new tickets to be created                             |
| zdAllowCommentTicket | true                        | Allows commenting on existing tickets (requires a ticket ID) |
| zdAllowCommentClosed | false                       | Allow commenting on closed tickets                           |
| zdPublicComment      | true                        | Set comments as public                                       |
| zdTicketField        | ticket                      | The field name for the ticket ID                             |
| zdEmailField         | email                       | The field name for the commentor email                       |
| zdNameField          | name                        | The field name for the commentor name                        |
| zdCommentField       | comment                     | The field name for the ticket message                        |
| zdSubject            | `New Ticket from [[+name]]` | The subject of a new ticket                                  | 


### Example Usage
```html
[[FormIt? 
    &hooks=`zdHook,redirect`
    &zdAllowCommentTicket=`false`
    &redirectTo=`3`
]]
```
