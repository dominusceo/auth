# Project auth users email system
The goal of this project is to demostrate the useful mail module feature from NGINX using it as proxy mail.

NGINX can proxy IMAP, POP3 and SMTP protocols to one of the upstream mail servers that host mail accounts and thus can be used as a single endpoint for email clients. 

This may bring in a number of benefits, a few of it could be these:

 - Easy scaling the number of mail servers
 - Choosing a mail server basing on different rules, for example, choosing the nearest server basing on a client’s IP address
distributing the load among mail servers

# Prerequisites
The Requisites listed are:
 - NGINX  (already includes the Mail modules necessary to proxy email traffic) or NGINX Open Source compiled the Mail modules using the --with-mail parameter for email proxy functionality and --with-mail_ssl_module parameter for SSL/TLS support.
  - PHP 5.X or above (7.X)
 - NGINX 1.4.X or  above 1.9.X
 - Red Hat or Centos (7.X)
