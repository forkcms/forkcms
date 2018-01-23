# Events: Publish / Subscribe

Publish/subscribe (pub/sub in short) is a pattern where sender (publishers) trigger an event, whereon receivers (subscribers) can subscribe. With this mechanism modules can be linked and are able to interact on certain events.

For instance, if you don't like the existing search module and like to implement [Solr](http://lucene.apache.org/solr/), this system could come in handy. You don't need to hack every module with some specific code. You can subscribe to events that are triggered when a page is edited (after_edit), or when a blogpost is created (after_add), and execute the code to add/edit/delete the indexed data.

## Trigger an event (Publish)

In this release we added a lot of events. If you write your own modules and want other modules to be able to subscribe to certain events, you can use the BackendModel::triggerEvent() or FrontendModel::triggerEvent()-method (depending on which application you are working on).

The methods have three arguments:

* `$module`, the name of the module that triggers the event.
* `$eventName`, the name of the event, this is up to you, but choose a logical name.
* `$data`, optionally you can add data that will be send to all subscribers. For instance you can pass an array with all the data of the added item.

When an event is triggered, the system will check if there are subscribers. If there are no subscriptions, nothing will be done so no resources are wasted. If there are subscriptions, we will add a line in the queue for each subscription. When the items are inserted in the queue we will start processing the queue right away in the background.

## Subscribe on an event

Ofcourse the events are useless when nothing subscribes to them. So, there is a method that can be used to subscribe to an event: `BackendModel::subscribeToEvent()` and `FrontendModel::subscribeToEvent()`.
When you subscribe to an event, there are a few arguments that tell the system to which event you want to subscribe:

* `$eventModule`, the name of the module that triggers the event.
* `$eventName`, the name of the event whereon you want to subscribe.
* `$module`, the name of the module that subscribes to the event.
* `$callback`, the callback that will be executed when the event is triggered.

Besides a method to subscribe there is a method to unsubscribe from events also: `BackendModel::unsubscribeFromEvent()` and `FrontendModel::unsubscribeFromEvent()`.

## Queue

Each time an event is triggered we will check if the processing is ongoing, if not the processing is started. If the queue isn't empty one item will be locked and processed.

Processing means that we lock the item and execute the provided callback. If this was successfull the item will be removed from the queue, otherwise it will be marked as an error. When the first item is processed we will look in the queue if there are other items to process. If that isn't the case, the script is terminated so no resources are wasted.
