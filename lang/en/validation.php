<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute_must_be_accepted',
    'accepted_if' => ':attribute_must_be_accepted_when_:other_is_:value',
    'active_url' => ':attribute_must_be_a_valid_URL',
    'after' => ':attribute_must_be_a_date_after_:date',
    'after_or_equal' => ':attribute_must_be_a_date_after_or_equal_to_:date',
    'alpha' => ':attribute_must_only_contain_letters',
    'alpha_dash' => ':attribute_must_only_contain_letters_numbers_dashes_and_underscores',
    'alpha_num' => ':attribute_must_only_contain_letters_and_numbers',
    'array' => ':attribute_must_be_an_array',
    'ascii' => ':attribute_must_only_contain_single-byte_alphanumeric_characters_and_symbols',
    'before' => ':attribute_must_be_a_date_before_:date',
    'before_or_equal' => ':attribute_must_be_a_date_before_or_equal_to_:date',
    'between' => [
        'array' => ':attribute_must_have_between_:min_and_:max_items',
        'file' => ':attribute_must_be_between_:min_and_:max_kilobytes',
        'numeric' => ':attribute_must_be_between_:min_and_:max',
        'string' => ':attribute_must_be_between_:min_and_:max_characters',
    ],
    'boolean' => ':attribute_must_be_true_or_false',
    'can' => ':attribute_contains_an_unauthorized_value',
    'confirmed' => ':attribute_confirmation_does_not_match',
    'contains' => ':attribute_is_missing_a_required_value',
    'current_password' => 'password_is_incorrect',
    'date' => ':attribute_must_be_a_valid_date',
    'date_equals' => ':attribute_must_be_a_date_equal_to_:date',
    'date_format' => ':attribute_must_match_the_format_:format',
    'decimal' => ':attribute_must_have_:decimal_decimal_places',
    'declined' => ':attribute_must_be_declined',
    'declined_if' => ':attribute_must_be_declined_when_:other_is_:value',
    'different' => ':attribute_and_:other_must_be_different',
    'digits' => ':attribute_must_be_:digits_digits',
    'digits_between' => ':attribute_must_be_between_:min_and_:max_digits',
    'dimensions' => ':attribute_has_invalid_image_dimensions',
    'distinct' => ':attribute_has_a_duplicate_value',
    'doesnt_end_with' => ':attribute_must_not_end_with_one_of_the_following:_:values',
    'doesnt_start_with' => ':attribute_must_not_start_with_one_of_the_following:_:values',
    'email' => ':attribute_must_be_a_valid_email_address',
    'ends_with' => ':attribute_must_end_with_one_of_the_following:_:values',
    'enum' => 'selected_:attribute_is_invalid',
    'exists' => 'selected_:attribute_is_invalid',
    'extensions' => ':attribute_must_have_one_of_the_following_extensions:_:values',
    'file' => ':attribute_must_be_a_file',
    'filled' => ':attribute_must_have_a_value',
    'gt' => [
        'array' => ':attribute_must_have_more_than_:value_items',
        'file' => ':attribute_must_be_greater_than_:value_kilobytes',
        'numeric' => ':attribute_must_be_greater_than_:value',
        'string' => ':attribute_must_be_greater_than_:value_characters',
    ],
    'gte' => [
        'array' => ':attribute_must_have_:value_items_or_more',
        'file' => ':attribute_must_be_greater_than_or_equal_to_:value_kilobytes',
        'numeric' => ':attribute_must_be_greater_than_or_equal_to_:value',
        'string' => ':attribute_must_be_greater_than_or_equal_to_:value_characters',
    ],
    'hex_color' => ':attribute_must_be_a_valid_hexadecimal_color',
    'image' => ':attribute_must_be_an_image',
    'in' => 'selected_:attribute_is_invalid',
    'in_array' => ':attribute_must_exist_in_:other',
    'integer' => ':attribute_must_be_an_integer',
    'ip' => ':attribute_must_be_a_valid_IP_address',
    'ipv4' => ':attribute_must_be_a_valid_IPv4_address',
    'ipv6' => ':attribute_must_be_a_valid_IPv6_address',
    'json' => ':attribute_must_be_a_valid_JSON_string',
    'list' => ':attribute_must_be_a_list',
    'lowercase' => ':attribute_must_be_lowercase',
    'lt' => [
        'array' => ':attribute_must_have_less_than_:value_items',
        'file' => ':attribute_must_be_less_than_:value_kilobytes',
        'numeric' => ':attribute_must_be_less_than_:value',
        'string' => ':attribute_must_be_less_than_:value_characters',
    ],
    'lte' => [
        'array' => ':attribute_must_not_have_more_than_:value_items',
        'file' => ':attribute_must_be_less_than_or_equal_to_:value_kilobytes',
        'numeric' => ':attribute_must_be_less_than_or_equal_to_:value',
        'string' => ':attribute_must_be_less_than_or_equal_to_:value_characters',
    ],
    'mac_address' => ':attribute_must_be_a_valid_MAC_address',
    'max' => [
        'array' => ':attribute_must_not_have_more_than_:max_items',
        'file' => ':attribute_must_not_be_greater_than_:max_kilobytes',
        'numeric' => ':attribute_must_not_be_greater_than_:max',
        'string' => ':attribute_must_not_be_greater_than_:max_characters',
    ],
    'max_digits' => ':attribute_must_not_have_more_than_:max_digits',
    'mimes' => ':attribute_must_be_a_file_of_type:_:values',
    'mimetypes' => ':attribute_must_be_a_file_of_type:_:values',
    'min' => [
        'array' => ':attribute_must_have_at_least_:min_items',
        'file' => ':attribute_must_be_at_least_:min_kilobytes',
        'numeric' => ':attribute_must_be_at_least_:min',
        'string' => ':attribute_must_be_at_least_:min_characters',
    ],
    'min_digits' => ':attribute_must_have_at_least_:min_digits',
    'missing' => ':attribute_must_be_missing',
    'missing_if' => ':attribute_must_be_missing_when_:other_is_:value',
    'missing_unless' => ':attribute_must_be_missing_unless_:other_is_:value',
    'missing_with' => ':attribute_must_be_missing_when_:values_is_present',
    'missing_with_all' => ':attribute_must_be_missing_when_:values_are_present',
    'multiple_of' => ':attribute_must_be_a_multiple_of_:value',
    'not_in' => 'selected_:attribute_is_invalid',
    'not_regex' => ':attribute_format_is_invalid',
    'numeric' => ':attribute_must_be_a_number',
    'password' => [
        'letters' => ':attribute_must_contain_at_least_one_letter',
        'mixed' => ':attribute_must_contain_at_least_one_uppercase_and_one_lowercase_letter',
        'numbers' => ':attribute_must_contain_at_least_one_number',
        'symbols' => ':attribute_must_contain_at_least_one_symbol',
        'uncompromised' => 'given_:attribute_has_appeared_in_a_data_leak._Please_choose_a_different_:attribute',
    ],
    'present' => ':attribute_must_be_present',
    'present_if' => ':attribute_must_be_present_when_:other_is_:value',
    'present_unless' => ':attribute_must_be_present_unless_:other_is_:value',
    'present_with' => ':attribute_must_be_present_when_:values_is_present',
    'present_with_all' => ':attribute_must_be_present_when_:values_are_present',
    'prohibited' => ':attribute_is_prohibited',
    'prohibited_if' => ':attribute_is_prohibited_when_:other_is_:value',
    'prohibited_unless' => ':attribute_is_prohibited_unless_:other_is_in_:values',
    'prohibits' => ':attribute_prohibits_:other_from_being_present',
    'regex' => ':attribute_format_is_invalid',
    'required' => ':attribute_is_required',
    'required_array_keys' => ':attribute_must_contain_entries_for:_:values',
    'required_if' => ':attribute_is_required_when_:other_is_:value',
    'required_if_accepted' => ':attribute_is_required_when_:other_is_accepted',
    'required_if_declined' => ':attribute_is_required_when_:other_is_declined',
    'required_unless' => ':attribute_is_required_unless_:other_is_in_:values',
    'required_with' => ':attribute_is_required_when_:values_is_present',
    'required_with_all' => ':attribute_is_required_when_:values_are_present',
    'required_without' => ':attribute_is_required_when_:values_is_not_present',
    'required_without_all' => ':attribute_is_required_when_none_of_:values_are_present',
    'same' => ':attribute_must_match_:other',
    'size' => [
        'array' => ':attribute_must_contain_:size_items',
        'file' => ':attribute_must_be_:size_kilobytes',
        'numeric' => ':attribute_must_be_:size',
        'string' => ':attribute_must_be_:size_characters',
    ],
    'starts_with' => ':attribute_must_start_with_one_of_the_following:_:values',
    'string' => ':attribute_must_be_a_string',
    'timezone' => ':attribute_must_be_a_valid_timezone',
    'unique' => ':attribute_has_already_been_taken',
    'uploaded' => ':attribute_failed_to_upload',
    'uppercase' => ':attribute_must_be_uppercase',
    'url' => ':attribute_must_be_a_valid_URL',
    'ulid' => ':attribute_must_be_a_valid_ULID',
    'uuid' => ':attribute_must_be_a_valid_UUID',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'ids.*' => 'id',
        'permission_ids.*' => 'permission_id',

        // Pagination
        'pagination.per_page' => 'pagination_per_page',
        'pagination.page' => 'pagination_page',
        'pagination.cursor' => 'pagination_cursor',

        // Filters
        'filters.tenant_id' => 'filters_tenant_id',
        'filters.license_id' => 'filters_license_id',
        'filters.status' => 'filters_status',
        'filters.type' => 'filters_type',
    ],

];
