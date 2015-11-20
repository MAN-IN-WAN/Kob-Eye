    [SWITCH [!El::type!]|=]
        [CASE fkey]
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
