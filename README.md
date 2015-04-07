# RemoteResource
Supports PHP 5.3.2 and up.

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
##### Creating a Model
Extend RemoteResource\RemoteResource, and set static $site.
$resource_name and $plural_resource_name properties are optional, and may be set where the name of the class does not mirror the name of the remote resource.
Configuration is set on a per-resource basis, designated by the $format, $auth_type, and $credentials properties.

```
class ProductImage extends RemoteResource\RemoteResource {
  public static $site                 = "http://example.com/api/product_images";
  public static $resource_name        = "product_image";
  public static $plural_resource_name = "product_images";
  public static $format               = "json";
  public static $auth_type            = "basic";
  public static $credentials          = "user:password";
}
```

##### Simplest Model
```
class ProductImage extends RemoteResource\RemoteResource {
  public static $site = "http://example.com/api/product_images";
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

If an attribute comes back from the remote API which has already been set on the resource instance, it will be overwritten to reflect the persisted value.
```
$product_image = ProductImage::find(15);
echo $product_image->name; // "rainbow cube"
echo $product_image->sizes_and_urls[30]; // "https://path/to/cdn/image/sjiEFzciA.png"
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
Familiarize yourself with RemoteResource Exceptions. __Exceptions are evaluated in the order presented here.__

```
status_code         exception
---------------------------------------------------------------
400                 RemoteResource\Exception\BadRequest
401                 RemoteResource\Exception\UnauthorizedAccess
403                 RemoteResource\Exception\ForbiddenAccess
404                 RemoteResource\Exception\ResourceNotFound
405                 RemoteResource\Exception\MethodNotAllowed
408                 RemoteResource\Exception\RequestTimeout
409                 RemoteResource\Exception\ResourceConflict
410                 RemoteResource\Exception\ResourceGone
401..499            RemoteResource\Exception\ClientError
500..599            RemoteResource\Exception\ServerError
unknown             RemoteResource\Exception\ConnectionError
```

The response that triggered the exception is stored, and can be accessed.

```
try {
} catch ( RemoteResource\Exception\ResourceNotFound $e) {
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

### RemoteResource\Collection
A RemoteResource\Collection is a collection of RemoteResource objects. The RemoteResource\Collection object implements the Iterator interface, and can therefore be treated as a PHP collection.

```
$remote_resource_collection = ProductImage::all();

foreach($remote_resource_collection as $remote_resource) {
  echo get_class( $remote_resource ); // "RemoteResource\RemoteResource"
}
```

##### RemoteResource\Collection methods of interest
```
returns                      method( args )
____________________________________________________________
integer                      size() || count()
RemoteResource               first()
RemoteResource               last()
```

##### Custom Methods
RemoteResource allows you to go off of the rails of RESTful convention. Know that __responses will be returned as arrays, and not as RemoteResource objects__.

```
returns     method        ( args )
_____________________________________________________________
array       static get       ( $path, $attributes=array() )
array       static post      ( $path, $attributes=array() )

array              patch     ( $path, $attributes=array() )
array              delete    ( $path )
```

```
// requests GET "http://example.com/product_images/5/clone/"
ProductImage::get("5/clone");
```

```
// requests POST "http://example.com/product_images/5/clone/"
ProductImage::post("5/clone", array("clone_to" => 15));
```

```
// requests PATCH "http://example.com/product_images/12/increment/"
$product_image->patch("increment");
```

```
// requests DELETE "http://example.com/product_images/12/associated/"
$product_image->delete("associated");
```

### Changelog
View the [CHANGELOG.md](CHANGELOG.md "CHANGELOG.md")

### TODO
- better code commenting ( documentation )
- README section on Formatters and Auth choices
