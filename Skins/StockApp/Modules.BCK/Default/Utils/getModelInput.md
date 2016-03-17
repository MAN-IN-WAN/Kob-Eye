    [SWITCH [!El::type!]|=]
        [CASE boolean]
                {name: '[!El::name!]',        type: 'boolean'}
        [/CASE]
        [CASE text]
                {name: '[!El::name!]',        type: 'string'}
        [/CASE]
        [CASE password]
                {name: '[!El::name!]',        type: 'string'}
        [/CASE]
        [CASE email]
                {name: '[!El::name!]',        type: 'string'}
        [/CASE]
        [CASE url]
                {name: '[!El::name!]',        type: 'string'}
        [/CASE]
        [CASE int]
                {name: '[!El::name!]',        type: 'integer'}
        [/CASE]
        [CASE float]
                {name: '[!El::name!]',        type: 'float'}
        [/CASE]
        [CASE price]
                {name: '[!El::name!]',        type: 'float'}
        [/CASE]
        [CASE pourcent]
                {name: '[!El::name!]',        type: 'float'}
        [/CASE]
        [CASE fkey]
                {name: '[!El::name!]',        type: 'int'}
        [/CASE]
        [CASE date]
                {name: '[!El::name!]',        type: 'date'}
        [/CASE]
        [DEFAULT]
                {name: '[!El::name!]',        type: 'string'}
        [/DEFAULT]
    [/SWITCH]
