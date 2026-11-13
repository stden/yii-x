<?php
/**
 * passwordStrength.php - проверка сложности пароля
 */
class passwordStrength extends CValidator
{
    const STRONG = 'strong';
    const WEAK = 'weak';

    public $strength;

    private $weak_pattern = '/^(?=.*[a-zA-Z0-9]).{5,}$/';
    private $strong_pattern = '/^(?=.*\d(?=.*\d))(?=.*[a-zA-Z](?=.*[a-zA-Z])).{5,}$/';

    /**
     * Validates the attribute of the object.
     * If there is any error, the error message is added to the object.
     * @param CModel $object the object being validated
     * @param string $attribute the attribute being validated
     * @return void
     */
    protected function validateAttribute($object, $attribute)
    {
        // check the strength parameter used in the validation rule of our model
        if ($this->strength == self::WEAK) {
            $pattern = $this->weak_pattern;
        }
        elseif ($this->strength == self::STRONG) {
            $pattern = $this->strong_pattern;
        }

        // extract the attribute value from it's model object
        $value = $object->$attribute;
        if (!preg_match($pattern, $value)) {
            $this->addError($object, $attribute, 'Слишком простой пароль!');
        }
    }

    /**
     * Returns the JavaScript needed for performing client-side validation.
     * @param CModel $object the data object being validated
     * @param string $attribute the name of the attribute to be validated.
     * @return string the client-side validation script.
     * @see CActiveForm::enableClientValidation
     */
    public function clientValidateAttribute($object, $attribute)
    {

        // check the strength parameter used in the validation rule of our model
        if ($this->strength == self::WEAK) {
            $pattern = $this->weak_pattern;
        }
        elseif ($this->strength == self::STRONG) {
            $pattern = $this->strong_pattern;
        }

        $condition = "!value.match({$pattern})";

        return "
if(" . $condition . ") {
    messages.push(" . CJSON::encode('Слишком простой пароль!') . ");
}
";
    }
}
