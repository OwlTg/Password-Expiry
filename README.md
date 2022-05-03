
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
- Should have a confirm password
- Should display error message below if new password is the same as current:
```http
  "Please choose a password that you haven't used recently."
```
#### Session Expiry
Below are the new session expiry rules:

| Action  | Expiry                    |
| :-------- |:--------------------------|
| `login with password expired after 90 days`| 10 minutes                |
| `successful login without password expiring`| 30 minutes                |
| `System engines`| N/A                       |


## Documentation

[Proposed Process](https://docs.google.com/document/d/1tWMZ3dbXAZSNPV4fRDSEIImbL-knpoRe/edit)


## How to use

To start using the api, connect thru ajax call :

````
PUT controller/settings/change_password
````
Parameters:

| Key              | Value           | Description                       |
|:-----------------|:----------------|:----------------------------------|
| current_password | `alpha numeric` | Required, user's current password |
| password         | `alpha numeric` | Required, this is the new password set                 
| confirm_password | `alpha numeric`  | Optional, coming from password expired form only|


#### Password Rules

- minimum 10 characters
- should have atleast 1 number
- should have atleast 1 letter
- should have atleast 1 capital
- should have atleast 1 Uppercase
- should not use the **same** last password

##Customization

Rules and validations can be altered inside the model

````
  model/User_mo.php
````

Since the requirement is to maintain the same functions( this function is also utilized by System Engines, Reset Password, Forgot Password)
, a separate function can be found  `validate_change_password_with_confirm`

We are using a custom library : `Data_validator` for automatic validations.

You can set the rules for each parameter and the error message can be easily mapped by using the same key with **html name attribute**:

````
  $this->validation_rules= array(
    'confirm_password' => array(
                'label' => "Confirm Password",
                'rules' => array(
                    'callback_password_not_identical' => array($this, 'callback_password_not_identical'),
                )
            )
         );
````

#### Callback Functions

You can customize rules by using a callback function:

````
'callback_password_not_identical' => array($this, 'callback_password_not_identical'),
````

Do whatever additional validations needed and return a custom **Error Message**

````
 public function callback_password_not_identical($field, $value, $data)
    {
        if ($data['confirm_password'] != $data['password']) {
            return "New Password and confirm password does not match.";
        }
        return TRUE;
    }
````

#### Callback Function Parameters
Callback functions accepts 3 parameters:

```
callback_function($field, $value, $data)
```
- `$field` is the target input field's name attribute, in this case: **confirm_password**
- `$value` is the input value of the target field, e.g **Test1234..**
- `$data` is the object of the parameter e.g. 
`
{
 current_password : passWord01., 
 password: Test1234..,
 confirm_password : Test1234..
}
`
