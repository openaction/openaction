# Messenger transports

Each Messenger transport now reads its DSN from a dedicated environment variable so you can switch backends (RabbitMQ, SQS, Doctrine) per transport without code changes:

- `MESSENGER_TRANSPORT_DSN_EMAILING`
- `MESSENGER_TRANSPORT_DSN_TEXTING`
- `MESSENGER_TRANSPORT_DSN_IMPORTING`
- `MESSENGER_TRANSPORT_DSN_PRIORITY_HIGH`
- `MESSENGER_TRANSPORT_DSN_PRIORITY_LOW`
- `MESSENGER_TRANSPORT_DSN_INDEXING`

The failure transport stays on `doctrine://default?queue_name=failed`. In the test environment every transport is already configured as `in-memory://`.

## Default (RabbitMQ)

The default values in `console/.env` keep the previous exchanges/queues by encoding them directly in each DSN:

```
MESSENGER_TRANSPORT_DSN_EMAILING=amqp://guest:guest@localhost:5672/%2f/messages?exchange[name]=emailing&queues[messages_emailing]=
MESSENGER_TRANSPORT_DSN_TEXTING=amqp://guest:guest@localhost:5672/%2f/messages?exchange[name]=texting&queues[messages_texting]=
MESSENGER_TRANSPORT_DSN_IMPORTING=amqp://guest:guest@localhost:5672/%2f/messages?exchange[name]=importing&queues[messages_importing]=
MESSENGER_TRANSPORT_DSN_PRIORITY_HIGH=amqp://guest:guest@localhost:5672/%2f/messages?exchange[name]=priority_high&queues[messages_priority_high]=
MESSENGER_TRANSPORT_DSN_PRIORITY_LOW=amqp://guest:guest@localhost:5672/%2f/messages?exchange[name]=priority_low&queues[messages_priority_low]=
MESSENGER_TRANSPORT_DSN_INDEXING=amqp://guest:guest@localhost:5672/%2f/messages?exchange[name]=indexing&queues[messages_indexing]=
```

## Amazon SQS example

Replace credentials/region as needed; you can use different queues per transport if desired.

```
MESSENGER_TRANSPORT_DSN_EMAILING=sqs://ACCESS_KEY:SECRET@default?region=eu-west-1&queue_name=messages_emailing
MESSENGER_TRANSPORT_DSN_TEXTING=sqs://ACCESS_KEY:SECRET@default?region=eu-west-1&queue_name=messages_texting
MESSENGER_TRANSPORT_DSN_IMPORTING=sqs://ACCESS_KEY:SECRET@default?region=eu-west-1&queue_name=messages_importing
MESSENGER_TRANSPORT_DSN_PRIORITY_HIGH=sqs://ACCESS_KEY:SECRET@default?region=eu-west-1&queue_name=messages_priority_high
MESSENGER_TRANSPORT_DSN_PRIORITY_LOW=sqs://ACCESS_KEY:SECRET@default?region=eu-west-1&queue_name=messages_priority_low
MESSENGER_TRANSPORT_DSN_INDEXING=sqs://ACCESS_KEY:SECRET@default?region=eu-west-1&queue_name=messages_indexing
```

## Doctrine transport example

Doctrine DSNs are also supported, but the schema filter excludes `messenger_messages`, so you must create the table manually or run `symfony console messenger:setup-transports` when switching to Doctrine.

```
MESSENGER_TRANSPORT_DSN_EMAILING=doctrine://default?queue_name=messages_emailing
MESSENGER_TRANSPORT_DSN_TEXTING=doctrine://default?queue_name=messages_texting
MESSENGER_TRANSPORT_DSN_IMPORTING=doctrine://default?queue_name=messages_importing
MESSENGER_TRANSPORT_DSN_PRIORITY_HIGH=doctrine://default?queue_name=messages_priority_high
MESSENGER_TRANSPORT_DSN_PRIORITY_LOW=doctrine://default?queue_name=messages_priority_low
MESSENGER_TRANSPORT_DSN_INDEXING=doctrine://default?queue_name=messages_indexing
```

## Deployment note

Production/staging workers read these variables directly, so ensure each transport DSN is set for the selected backend before deploying or restarting consumers.
