# JMAP Core

This library implements the JMAP core according to [RFC 8620](https://tools.ietf.org/html/rfc8620). It is used as a foundation for implementing data models that can be synced with a server using JMAP via so-called _capabilities_.

Check out the PHPDocs!

## Usage

The main entry point is an instance of the class `Barnslig\Jmap\Core\JMAP`. It exposes multiple [PSR-15](https://www.php-fig.org/psr/psr-15/) compliant HTTP Server Request Handlers:

-   `JMAP::sessionHandler`
    -   Implements the [JMAP Session Resource](https://tools.ietf.org/html/rfc8620#section-2)
    -   Mountpoint: `/.well-known/jmap`
-   `JMAP::apiHandler`
    -   Implements the `apiUrl` endpoint of the Session Resource. See [3.1. Making an API Request](https://tools.ietf.org/html/rfc8620#section-3.1)
    -   Mountpoint: Arbitrary as long as it matches the `apiUrl` value of the Session Resource. Proposed: `/api`

For a full example using [League\Route\Router](https://github.com/thephpleague/route), see [public_html/index.php](/public_html/index.php).

## Session

A session consists of capabilities, accounts and endpoints. It is used to hold the state of the API while processing a request.

## Structure of a JMAP API

### Capabilities

Capabilities are used to extend the functionality of a JMAP API. Examples for capabilities are Mail, Contacts and Calendars.

A single JMAP API usually has multiple capabilities, at least `urn:ietf:params:jmap:core`.

At every request, the API determines the set of used capabilities via the `using` property, see [3.3. The Request Object](https://tools.ietf.org/html/rfc8620#section-3.3).

### Types

Every capability consists of types that provide methods. For example, a capability of type `urn:ietf:params:jmap:mail` may have a type called `Mailbox`.

Types define an interface for creating, retrieving, updating, and deleting objects of their kind.

### Methods

Every type consists of at least one method. They are what is actually called during a request. Using the previous example, records of type `Mailbox` would be fetched via a `Mailbox/get` call, modified via a `Mailbox/set` call etc.

### Invocation

Invocations represent method calls and responses. An invocation is a 3-tuple consisting of:

1. Method name
1. Arguments object
1. Method call ID

Example: `["Mailbox/get", { "accountId": "A13824" }, "#0]`

The method call ID is an arbitrary string from the client to be echoed back with the response. It is used by the client to re-identify responses when issuing multiple method calls during a single request and by the server to resolve references to the results of other method call during response computation.

A server's response uses the same 3-tuple with the arguments replaced by the method's return value.

## Implementing a capability

A capability MUST extend the abstract class [Capability](Capability.php). Within the capability class, it then registers its types and corresponding methods which are implemented using the [Type](Type.php) and [Method](Method.php) interfaces.

For an example, check out the [CoreCapability](Capabilities/).

Types usually use [Standard Methods](https://tools.ietf.org/html/rfc8620#section-5). To ease development, the library provides them as abstract Method classes with already built-in [JSON Schema](https://json-schema.org/) request validation:

-   [GetMethod](Methods/GetMethod.php) implementing [5.1. /get](https://tools.ietf.org/html/rfc8620#section-5.1)
-   [ChangesMethod](Methods/ChangesMethod.php) implementing [5.2. /changes](https://tools.ietf.org/html/rfc8620#section-5.2)
-   [SetMethod](Methods/SetMethod.php) implementing [5.3. /set](https://tools.ietf.org/html/rfc8620#section-5.3)
-   [CopyMethod](Methods/CopyMethod.php) implementing [5.4. /copy](https://tools.ietf.org/html/rfc8620#section-5.4)
-   [QueryMethod](Methods/Queryethod.php) implementing [5.5. /query](https://tools.ietf.org/html/rfc8620#section-5.5)
-   [QueryChangesMethod](Methods/QueryChangesethod.php) implementing [5.6. /queryChanges](https://tools.ietf.org/html/rfc8620#section-5.6)

When a method invocation fails, it MUST throw a [MethodInvocationException](Exceptions/MethodInvocationException.php).
