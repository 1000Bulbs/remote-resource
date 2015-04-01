# RemoteResource

### Philosophy
RemoteResource adopts most of the public API of ActiveResource for Rails and enforces many of the same expectations.

### Documentation

##### Available Instance Methods
```
returns     method( args )
_____________________________________________________
boolean     updateAttributes( $attributes = array() )
null        destroy()
boolean     save()
boolean     persisted()
boolean     valid()
integer     id()
array       errors()
array       attributes()
```

##### Available Class Methods

```
returns                      method( args )
____________________________________________________________
RemoteResource               create( $attributes = array() )
RemoteResource               find( $id )
RemoteResourceCollection     where( $attributes_to_match = array() )
RemoteResourceCollection     all()
```

### Usage
##### Global Configuration
As a requirement, set credentials to be consumed in the Basic Auth format.

```
RemoteResourceConfig::setCredentials('user:password');
```
##### Creating a Model
Extend RemoteResource, and set static $site, $resource_name, and $plural_resource_name properties.

```
class ProductImage extends RemoteResource {
  public static $site                 = "http://localhost:3000/api/product_images"; // required
  public static $resource_name        = "product_image";                            // required
  public static $plural_resource_name = "product_images";                           // required
}
```
##### Attribute Assignment
Dynamically assigned attributes are added to the attributes which will be sent over on save.

```
$product_image = new ProductImage;
$product_image->file = $file;
$product_image->name = 'rainbow cube';
$product_image->save();
```

As with ActiveResource, attributes are accessible as methods. If an attribute comes back from the remote API which has already been set on the resource instance, it will be overwritten to reflect the persisted value.
```
$product_image = ProductImage::find(15);
echo $product_image->name; // "rainbow cube"
echo $product_image->sizes_and_urls[30]; // "https://path/to/cdn/image/product_admin/100/sjiEFzciA.png?123984"
```

##### Create
```
$product_image = ProductImage::create($attributes);
```

##### Where
```
$product_images = ProductImage::where( array('product_id' => 12) );
```

##### Find
```
$product_image = ProductImage::find(15);
```
##### All
```
$product_images = ProductImage::all();
```

##### Update
As with ActiveResource, the update() method is reserved for internal use. use updateAttributes() instead.

```
$product_image->updateAttributes( array('product_id' => 12) );
```

##### Destroy
```
$product_image->destroy();
```

##### Save
```
$product_image->save();
```

### Expectations / Assumptions
Expectations on how the remote API is configured mirrors ActiveResource expectations. I will list those expectations here, since they aren't clearly documented for ActiveResource. __It is necessary for the remote API you are accessing to fulfill the HTTP, status codes, and JSON responses from the interface below, unless you do not plan (or do not need) to use the associated methods.__

```
method    HTTP                                 status        expected JSON response
_______________________________________________________________________________________
create    POST /remote_resource_path/          201           {'remote_resource': {}}
create    POST /remote_resource_path/          422           {'errors': []}

where     GET /remote_resource_path/           200           {'remote_resources': []}

all       GET /remote_resource_path/           200           {'remote_resources': []}

find      GET /remote_resource/path/:id        200           {'remote_resource': {}}

update    PATCH /remote_resource_path/:id      204           nil
update    PATCH /remote_resource_path/:id      422           {'errors': []}

destroy   DELETE /remote_resource_path/:id     204           nil
```

### Exceptions
Familiarized yourself with RemoteResource Exceptions. __Exceptions are evaluated in the order presented here.__

```
status_code         exception
------------------------------------------------
400                 RemoteResourceBadRequest
401                 RemoteResourceUnauthorizedAccess
403                 RemoteResourceForbiddenAccess
404                 RemoteResourceResourceNotFound
405                 RemoteResourceMethodNotAllowed
409                 RemoteResourceResourceConflict
410                 RemoteResourceResourceGone
401..499            RemoteResourceClientError
500..599            RemoteResourceServerError
unknown             RemoteResourceConnectionError
```

The response that triggered the exception is stored, and can be accessed.

```
try {
} catch ( RemoteResourceResourceNotFound $e) {
  $response = $e->response;
}
```

### Checking Validity
In order to determine whether or not a resource was correctly created, check the validity of the resource.

```
$product_image = ProductImage::create($attributes);
if ( $product_image->valid() ) {
  // success
}
```

Validity is determined by whether or not errors were generated for the resource. A resource is considered valid when it has no errors. This does not necessarily mean that the resource has been persisted. Check the persisted() method for this information.

### RemoteResourceCollection
A RemoteResourceCollection is a collection of RemoteResource objects. The RemoteResourceCollection object implements the Iterator interface, and can therefore be treated as a PHP collection.

```
$remote_resource_collection = ProductImage::all();

foreach($remote_resource_collection as $remote_resource) {
  echo class_name( $remote_resource ); // "RemoteResource"
}
```

##### RemoteResourceCollection methods of interest
```
returns                      method( args )
____________________________________________________________
RemoteResource               current()
RemoteResource               next()
integer                      size() || count()
RemoteResource               first()
RemoteResource               last()
```

### Changelog
View the [CHANGELOG.md](CHANGELOG.md "CHANGELOG.md")

### Version
View the [version](version.rb "version")

### TODO
- Custom API Actions
- Pre-recorded Tests ( PHP VCR )
- Clean up example code
