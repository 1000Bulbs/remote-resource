<?php
class RemoteResourceException extends Exception {
  public $response;

  public function __construct($response, $message = "", $code = 0, Exception $previous = null) {
    parent::__construct($message, $code, $previous);

    $this->response = $response; 
  }
}

// 400
class RemoteResourceBadRequest         extends RemoteResourceException {}

// 401
class RemoteResourceUnauthorizedAccess extends RemoteResourceException {}

// 403
class RemoteResourceForbiddenAccess    extends RemoteResourceException {}

// 404
class RemoteResourceResourceNotFound   extends RemoteResourceException {}

// 405
class RemoteResourceMethodNotAllowed   extends RemoteResourceException {}

// 409
class RemoteResourceResourceConflict   extends RemoteResourceException {}

// 410
class RemoteResourceResourceGone       extends RemoteResourceException {}

// 422
class RemoteResourceResourceInvalid    extends RemoteResourceException {}

// 401 - 499
class RemoteResourceClientError        extends RemoteResourceException {}

// 500 - 599
class RemoteResourceServerError        extends RemoteResourceException {}

// unknown status code
class RemoteResourceConnectionError    extends RemoteResourceException {}
