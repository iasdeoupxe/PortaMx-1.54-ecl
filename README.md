# :warning: Deprecated :warning:

PortaMx hasn't been updated since years and the vendor has vanished (see next warning). This repository has been archived, use at your own risk.

# :warning: Security Warning :warning:

If there is still some one using this Portal / Software this is an important security warning:

It seems a few months ago the `portamx.com` domain was completely unavailable and in the meantime there is some kind of "parking page" which means that ownership might have been taken over by some unknown entity.

This could also mean that a malicious new owner of that domain could e.g. create a new update server which is publishing a malicious update package (e.g. with injected malware / web shell or similar).

If you still want to keep this software running it is strongly suggested to e.g. edit your `/etc/hosts` file and replace something like this:

```
127.0.0.1       localhost
::1             localhost ip6-localhost ip6-loopback
```

by this:

```
127.0.0.1       localhost docserver.portamx.com portamx.com
::1             localhost ip6-localhost ip6-loopback docserver.portamx.com portamx.com
```

so that all requests are hitting your own host and not the problematic not valid domain anymore.

# PortaMx-1.54-ecl
Portal Management Extension for SMF 2.0.x

This is a fork of https://github.com/PortaMx/PortaMx-1.54-ecl (from https://portamx.com/) contains fixes (e.g. updates for newer PHP versions) collected from various bugreports / postings at http://portamx.com which are not (yet) incorporated into the upstream github repository.

**Note:** No active development (e.g. SMF 2.1 compatibility) is done / support is given in this repository. Only compatibility with the current SMF 2.0 releases is tried to kept.
