# JMAP Proxy

The goal of this project is to implement a spec-compliant [JMAP](https://jmap.io/) server in PHP. It should be focussed onto the [Core](https://tools.ietf.org/html/rfc8620) and [Mail](https://tools.ietf.org/html/rfc8621) specifications but is not limited to it.

The main difficulty with leveraging JMAP to build modern, responsive E-Mail-Clients is that there are no easy to adapt backend implementations: While it makes sense to implement the protocol within existing IMAP servers like Cyrus and Dovecot from a performance point of view, it often prevents existing setups from adopting JMAP as they may not easily switch their IMAP server setup, if they have access to it at all. After all, this also prevents commonly-used self-hosted webmail clients like Roundcube from broadly adopting JMAP.

By implementing the JMAP layer using PHP, this project makes JMAP more accessible to the community and makes it easy to use the protocol for use-cases outside the focus of a mail server, for example sharing files within a groupware.

## Getting started

There is still a lack of documentation on how to use the code. Until it is done, start by running a local PHP server:

```
php -d opcache.enable=0 -S localhost:9010 -t public_html public_html/index.php
```

See [Core/README.md](src/Core/README.md) for an overview of the internals of JMAP and this library!

## Authors

See [AUTHORS](AUTHORS)

## License

See [LICENSE](LICENSE)
