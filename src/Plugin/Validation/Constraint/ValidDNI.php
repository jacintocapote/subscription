<?php

namespace Drupal\subscription\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks that the submitted value is a valid DNI.
 *
 * @Constraint(
 *   id = "ValidDNI",
 *   label = @Translation("ValidDNI", context = "Validation"),
 *   type = "string"
 * )
 */
class ValidDNI extends Constraint {

  // The message that will be shown if the value is not an DNI.
  public $notDNI = '%value is not an valid DNI';


}
