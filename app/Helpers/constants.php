<?php

const USER_PASSWORD_LENGTH = 8;
const DB_PASSWORD_LENGTH = 32;
const TENANT_HASH_LENGTH = 8;
const SECONDS_IN_MINUTE = 60;
const SORT_FIRST_MODIFIER = 1;
const SORT_SECOND_MODIFIER = 10;
const MINOR_UNITS_IN_CURRENCY = 100;

const MESSAGES = [
    'access_denied' => 'access_denied',
    'not_found' => 'not_found',
    'server_error' => 'server_error',
    'validation_errors' => 'validation_errors',
    'not_authenticated' => 'not_authenticated',
    'an_error_occurred' => 'an_error_occurred',
    'tenant_could_not_be_identified' => 'tenant_could_not_be_identified',
    'prohibited_by_license' => 'prohibited_by_license',
];

const DESCRIPTIONS = [
    'access_denied' => 'The user does not have permission.',
    'not_found' => 'not found.',
    'prohibited_by_license' => 'Operation prohibited by current license.',
];

const ZIP_EXTENSION = '.zip';
const JPG_EXTENSION = '.jpg';

const MAX_IMAGE_DIMENSION = 5000;

const MAX_IMAGE_SIZE = 5120;
