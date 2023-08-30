<?php

namespace App\Theme\Source;

use JsonSchema\Constraints\Constraint;
use JsonSchema\Validator;

class ThemeManifestValidator
{
    public function validate(array $manifest): array
    {
        $schema = json_decode(file_get_contents(__DIR__.'/Schema/manifest-schema.json'), false, 512, JSON_THROW_ON_ERROR);

        $validator = new Validator();
        $validator->validate($manifest, $schema, Constraint::CHECK_MODE_TYPE_CAST);

        $errors = [];
        foreach ($validator->getErrors() as $error) {
            $errors[] = $error['property'].': '.$error['message'];
        }

        return $errors;
    }
}
