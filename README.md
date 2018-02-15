# SPOD API Plugin

## Endpoints

### spodapi/roomsusingdataset

Retrieves Agora rooms where it has been used a given dataset.

**Input parameters**

* `data-url`: a Data API URL of a TET/CKAN dataset (i.e. http://mydomain.com/api/action/datastore_search?resource_id=896c9f0b-fd19-4963-a0e8-c1224e39d9de)

**Returns**

```
{
  "status": "success",
  "result": [
    {
      "ownerId": "<numeric id of user who created the room>",
      "subject": "<iitle of the room>",
      "body":"<first message of the room>",
      "views":"<number of times the room has been opened>",
      "comments":"<number of messages in the room>",
      "opendata":"<number of datasets linked in the room>",
      "timestamp":"<creation time stamp in format: YYYY-MM-DD hh:mm:ss>",
      "id":"<id of the room>"
    },
    {
      ...
    }
  ]
}
```

**Errors**

In case of error, a different JSON is returned:

```
{
  "status": "error",
  "error": "<the error message>"
}
```
