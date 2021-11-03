# LDAP-Login
This is a premade login page that can be used for LDAP login authentation. Can be easily edited and deployed. You can also easily add your own scripts/commands that will run when the user authentation succeeds. By default (until changed and updated), on a successful login the page will display the users email addresses assigned to them in Active Directory.
To get this to work you will have to add this extension to your php.ini file: 'extension=php_ldap.dll' and then restart your Apache server once it's been added.
