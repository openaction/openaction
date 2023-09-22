# OpenAction APIs

OpenAction provides an API to work with your projects from external platforms.

## API endpoints

You can find the list of available endpoints in the OpenAPI documentation of your instance
of OpenAction: `https://<your-openaction-console-url>/openapi`.

For example, if you use the instance of OpenAction provided by Citipo,
[you can access it here](https://console.citipo.com/openapi).

## Authentication

The OpenAction API requires authentication to access all endpoints.

There are three types of authentication:

### Standard authentication

The standard authentication grants you access to all the endpoints necessary to implement custom
public tools based on the content created on OpenAction.

It's most likely the authentication you will want to use if you are working on:

* a custom display of the content created on OpenAction such as a mobile app, a custom website, an embedded 
  widget on another website, ... ;
* a custom implementation of an OpenAction created form or contact subscription form ;

To authenticate using the Standard authentication, open the OpenAction project you want to interact with 
and go to the Developers > API Access section.

> *Note*: you may need specific permissions to access this section. If you don't see it in your
> project, ask your administrator to grant you the API access permission.

Once the token is obtained, use it as a Bearer HTTP token in your API calls:

```
curl --request GET \
    --url 'https://<your-openaction-console-url>/api' \
    --header 'Authorization: Bearer <token>'
```

Using the Standard authentication, you can access the General, Website and Community API endpoints.

### Membership authentication

The membership authentication allows you to implement a custom membership system based on contacts
stored in OpenAction.

To authenticate using the Membership authentication, you initially need a Standard token 
(see the previous section) to call the `login`s endpoint for a given member with its username 
and password.

When the authentication succeed, you will receive an AuthorizationToken as response of the login call.

You can then use this AuthorizationToken in the `authorize` endpoint to check the member is correctly 
authenticated:

```
curl --request POST \
  --url https://console.citipo.com/api/community/members/authorize \
  --header 'authorization: Bearer <token>' \
  --header 'content-type: application/json' \
  --data '{
      "_resource": "AuthorizationToken",
      "firstName": "Titouan",
      "lastName": "Galopin",
      "nonce": "xxx",
      "encrypted": "xxx"
  }'
```

Using the Membership authentication, you can access the Membership API endpoints.

### Integration authentication

The integration authentication allows you to authenticate Console administrators on custom external tools
to extend the capabilities of the Console.

Such tools could be:

* A Telegram bot to send regular contacts statistics to each administration administrator of your OpenAction 
  organization about the projects they manage ;
* A dashboard to aggregate the traffic of all the OpenAction projects managed by the currently authenticated 
  console administrator ;
* ...

To authenticate using the Integration authentication, you first need to register an integration in the
Integrations > Developers section of your organization.

For instance, once you register a Telegram Bot as an integration, you will be provided a URL to share 
with your OpenAction colleaborators so that they can authenticate on your Telegram Bot with their OpenAction 
account.

You will then receive an integration token that you can use to authenticate your API calls:

```
curl --request GET \
  --url 'https://console.citipo.com/api/integrations/projects' \
  --header 'Authorization: Bearer telegram_6cbecdad1a33ea8afa60def486578b9e99b238ec0c330d9c521e86308e092f57'
```

Using the Integration authentication, you can access the Integration API endpoints.
