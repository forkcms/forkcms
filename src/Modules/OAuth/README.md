# OAuth Module
The oAuth module provides a way to authenticate users using Microsoft Azure.

## Prerequisites

### Azure Application
To use this module you need to create an application in Azure. You can do this at the [Azure Portal](https://portal.azure.com).
Take note of the `Application (client) ID` `Application secret` and `Directory (tenant) ID` as you will need them later.
When setting up you'll need to provide a callback URL. This is the URL where the user will be redirected to after logging in.
This URL should be `https://<your-domain>/private/connect/azure/check`.

### Composer packages
This module requires the following composer packages:
* `knpuniversity/oauth2-client-bundle`
* `thenetworg/oauth2-azure`

## Setup
After installing the module, it still needs to be setup. got to `https://<your-domain>/private/en/o_auth/module_settings` and add your `client_id`, `secret` and `tenant`.
To enable the login button on the login page also enable the `enabled` setting.

### Setup Groups
The oAuth module needs to know which groups to add users to. To do this go to `https://<your-domain>/en/backend/user_group_index` and edit the groups you want to use.
When the oAuth module is enabled a new field wil be present on the edit page. This field is called `oAuth role` and should contain the `value` of the group in Azure.
On login the user will be added to the groups that match the `value` of the group in Azure.
If no group is found the login will fail.

### Setup security.yaml
Update the following line in `config/packages/security.yaml`. This will allow both off the authenticators to be used.
```yaml
custom_authenticator: ForkCMS\Modules\Backend\Domain\Authentication\BackendAuthenticator
```

to 
```yaml
custom_authenticators:
  - ForkCMS\Modules\OAuth\Domain\Authentication\AzureAuthenticator
  - ForkCMS\Modules\Backend\Domain\Authentication\BackendAuthenticator
entry_point: ForkCMS\Modules\Backend\Domain\Authentication\BackendAuthenticator
```
### Setup services.yaml
Add the following services to `config/services.yaml`. This wil make it possible to install the module at any time without breaking te needed services in the security.yaml file.
```yaml
ForkCMS\Modules\OAuth\Domain\OAuth\AzureProviderFactory:
  tags:
    - { name: oauth.provider_factory }

ForkCMS\Modules\OAuth\Domain\Authentication\AzureAuthenticator:
  tags:
    - { name: security.authenticator }

TheNetworg\OAuth2\Client\Provider\Azure:
  factory: [ '@ForkCMS\Modules\OAuth\Domain\OAuth\AzureProviderFactory', 'create' ]
```

### Changes to other modules
To make sure the oAuth module works correctly some changes have been made to other modules.
* src/Modules/Backend/templates/Backend/login.html.twig (added login button)
* src/Modules/Backend/templates/base/formTheme.html.twig (add additional form fields, disable checkboxes in user groups)
* src/Modules/Backend/Domain/UserGroup/UserGroup.php (added oAuth role field)
* src/Modules/Backend/Domain/UserGroup/UserGroupType.php (added oAuth role field)
* src/Modules/Backend/Domain/UserGroup/UserGroupDataTransferObject.php (added oAuth role field)

