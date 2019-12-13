# subscription

This is a example implementation for a subscription system with drupal 8. The system:

* Allow to add a custom block for create subscrition (Or you can create via subscription/add).
* The form has some validations:
  * DNI validation.
  * Allow only 1 request per DNI.
  * Verify Age submit based in settings page.
* With admin user we have a custom permission for access to admin page (/subscription/list). From here you can edit/delete/approve or deny. If you approve or deny the user will be notify.
* From admin/config/Subscription you can manage the min age and the mail for notify when a subscription is created.

# Environment for testing

Exists a test environment https://dev-subscription-jacinto.pantheonsite.io/ with the module enabled and allow check this implementation. The user for testing is (admin/admin)

# NOTES

This module require apply this core patch https://www.drupal.org/files/issues/2018-12-28/3015384-7.patch from https://www.drupal.org/project/drupal/issues/3015384 the reason is allow the filter by status with a select in another case we can't create this filter with views.
