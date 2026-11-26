<?php

return [
    'boolean' => 'The :attribute field must be true or false.',
    'confirmed' => 'The :attribute confirmation does not match.',
    'current_password' => 'The password is incorrect.',
    'date' => 'The :attribute is not a valid date.',
    'email' => 'The :attribute must be a valid email address.',
    'file' => 'The :attribute must be a file.',
    'image' => 'The :attribute must be an image (jpg, png or webp).',
    'in' => 'The selected :attribute is invalid.',
    'integer' => 'The :attribute must be a whole number.',
    'max' => [
        'numeric' => 'The :attribute must not be greater than :max.',
        'file' => 'The :attribute must not be larger than :max kilobytes.',
        'string' => 'The :attribute must not be longer than :max characters.',
        'array' => 'The :attribute must not have more than :max items.',
    ],
    'mimes' => 'The :attribute must be a file of type: :values.',
    'min' => [
        'numeric' => 'The :attribute must be at least :min.',
        'file' => 'The :attribute must be at least :min kilobytes.',
        'string' => 'The :attribute must be at least :min characters.',
        'array' => 'The :attribute must have at least :min items.',
    ],
    'required' => 'The :attribute field is required.',
    'string' => 'The :attribute must be text.',
    'unique' => 'This :attribute is already in use.',
    'url' => 'The :attribute must be a valid URL (starting with http:// or https://).',
    'uploaded' => 'The :attribute failed to upload. The file may be too large.',
    'password' => [
        'letters' => 'The :attribute must contain at least one letter.',
        'numbers' => 'The :attribute must contain at least one number.',
        'mixed' => 'The :attribute must contain upper and lower case letters.',
        'symbols' => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different one.',
    ],
    'attributes' => [
        'name' => 'name', 'email' => 'email', 'subject' => 'subject', 'message' => 'message', 'password' => 'password',
    ],
];
