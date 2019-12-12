<?php

namespace Drupal\subscription\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the UniqueInteger constraint.
 */
class ValidDNIValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($items, Constraint $constraint) {
    foreach ($items as $item) {
      // First check if the value is an integer.
      if (!$this->isDNI($item->value)) {
        // The value is not an valid DNI.
        $this->context->addViolation($constraint->notDNI, ['%value' => $item->value]);
      }
    }
  }

  /**
   * Is unique?
   *
   * @param string $value
   */
  private function isDNI($value) {
    $word = substr(strtoupper($value), -1);
    $number = substr($value, 0, -1);
    if (substr("TRWAGMYFPDXBNJZSQVHLCKE",$number % 23,1) == $word)
      return TRUE;
    else
      return FALSE;
    }

}
