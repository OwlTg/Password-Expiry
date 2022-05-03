
![Logo](https://promotexter.com/wp-content/themes/wp-bootstrap-4/assets/images/header/ptx-header-logo-dark.svg)
# 

Platform for businesses in sending customize marketing campaigns thru SMS, Viber and Emails.



## Password Expiration
Given the recent security attacks, we would like to implement additional security features.
A mandatory password expiration should force users to **change their password every 90 Days**.







#### Business Requirements
- Force redirect user to password expired form when over 90 days since password updated
- Should validate if password is using old or new encryption
- Should reuse existing setting for changing password
- Should display error message below if new password is the same as current:
```http
  "Please choose a password that you haven't used recently."
```
#### Session Expiry
Below are the new session expiry rules:

| Action  |Expiry                |
| :-------- :------------------------- |
| `login with password expired after 90 days`| 10 minutes |
| `successful login without password expiring`| 30 minutes |
| `System engines`| N/A |


## Documentation

[Proposed Process](https://docs.google.com/document/d/1tWMZ3dbXAZSNPV4fRDSEIImbL-knpoRe/edit)

