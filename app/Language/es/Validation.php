<?php

// Traducciones completas para las reglas de validación

return [
    // Reglas básicas
    'required'              => 'El campo {field} es obligatorio.',
    'matches'               => 'El campo {field} no coincide con el campo {param}.',
    'differs'               => 'El campo {field} debe ser diferente del campo {param}.',
    'is_unique'             => 'El valor del campo {field} ya está en uso.',
    'min_length'            => 'El campo {field} debe tener al menos {param} caracteres.',
    'max_length'            => 'El campo {field} no puede exceder {param} caracteres.',
    'exact_length'          => 'El campo {field} debe tener exactamente {param} caracteres.',
    'in_list'               => 'El campo {field} debe ser uno de los siguientes: {param}.',
    'not_in_list'           => 'El campo {field} no debe ser uno de los siguientes: {param}.',

    // Tipos de datos
    'numeric'               => 'El campo {field} debe contener solo números.',
    'integer'               => 'El campo {field} debe ser un número entero.',
    'decimal'               => 'El campo {field} debe ser un número decimal.',
    'greater_than'          => 'El campo {field} debe ser un número mayor que {param}.',
    'greater_than_equal_to' => 'El campo {field} debe ser un número mayor o igual a {param}.',
    'less_than'             => 'El campo {field} debe ser un número menor que {param}.',
    'less_than_equal_to'    => 'El campo {field} debe ser un número menor o igual a {param}.',

    // Fecha y tiempo
    'valid_date'            => 'El campo {field} debe contener una fecha válida.',
    'valid_date_format'     => 'El campo {field} debe tener el formato de fecha: {param}.',
    'after'                 => 'El campo {field} debe contener una fecha posterior a {param}.',
    'before'                => 'El campo {field} debe contener una fecha anterior a {param}.',

    // Formularios
    'alpha'                 => 'El campo {field} solo puede contener letras.',
    'alpha_space'           => 'El campo {field} solo puede contener letras y espacios.',
    'alpha_numeric'         => 'El campo {field} solo puede contener letras y números.',
    'alpha_numeric_space'   => 'El campo {field} solo puede contener letras, números y espacios.',
    'alpha_numeric_punct'   => 'El campo {field} solo puede contener letras, números, espacios y los siguientes caracteres: ~!#@$%^&*()_+-=,.?',
    'alpha_numeric_dash'    => 'El campo {field} solo puede contener letras, números, guiones y guiones bajos.',
    'valid_email'           => 'El campo {field} debe contener una dirección de correo válida.',
    'valid_emails'          => 'El campo {field} debe contener todas las direcciones de correo válidas.',
    'valid_url'             => 'El campo {field} debe contener una URL válida.',
    'valid_ip'              => 'El campo {field} debe contener una dirección IP válida.',
    'valid_base64'          => 'El campo {field} debe contener una cadena en formato base64 válida.',
    'valid_json'            => 'El campo {field} debe contener un JSON válido.',
    'valid_uuid'            => 'El campo {field} debe contener un UUID válido.',

    // Archivos
    'uploaded'              => 'El archivo {field} es obligatorio.',
    'max_size'              => 'El archivo {field} excede el tamaño máximo permitido de {param}.',
    'is_image'              => 'El archivo {field} debe ser una imagen.',
    'mime_in'               => 'El archivo {field} debe ser de tipo: {param}.',
    'ext_in'                => 'El archivo {field} debe tener una de las siguientes extensiones: {param}.',
    'max_dims'              => 'La imagen {field} excede las dimensiones máximas permitidas.',

    // Personalizados / Otros
    'permit_empty'          => 'El campo {field} puede estar vacío.',
    'string'                => 'El campo {field} debe ser una cadena de texto.',
    'regex_match'           => 'El campo {field} no tiene el formato correcto.',
    'timezone'              => 'El campo {field} debe ser una zona horaria válida.',
    'valid_cc_number'       => 'El campo {field} no contiene un número de tarjeta de crédito válido.',
];
