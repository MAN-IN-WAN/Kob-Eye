    [SWITCH [!El::type!]|=]
        [CASE rkey]
            {
                model: 'ActivaStock.model.[!El::objectName!]',
                name: '[!Utils::lowercase([!El::objectName!])!]s',
                primaryKey: 'id',
                foreignKey: '[!El::name!]',
                foreignStore: '[!El::objectName!]s'
            }
        [/CASE]
        [DEFAULT][/DEFAULT]
    [/SWITCH]
