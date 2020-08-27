[OBJ Systeme|Event|E]

[!items:=[!E::pollAll([!pollStart!],[!pollInterval!],[!pollDuration!])!]!]


{
    "lastSearch": "[!TMS::Now!]",
    [COUNT [!items::Ev!]|NbEv]
    "totalEv": [!NbEv!],
    [COUNT [!items::Au!]|NbAu]
    "totalAu": [!NbAu!],
    "events":
    [
        [STORPROC [!items::Ev!]|Ev]
            {
            "Module":"[!Ev::EventModule!]",
            "ObjectClass":"[!Ev::EventObjectClass!]",
            "ObjectId":"[!Ev::EventId!]",
            "Titre":"[!Ev::Titre!]",
            "Type":"[!Ev::EventType!]",
            "Id":"[!Ev::Id!]"
            }
        [/STORPROC]
    ],
    "alerts":
    [
        [STORPROC [!items::Au!]|Au]
            {
            "Icone":"[!Au::Icon!]",
            "Module":"[!Au::AlertModule!]",
            "ObjectClass":"[!Au::AlertObject!]",
            "ObjectId":"[!Au::ObjectId!]",
            "Titre":"[!Au::Title!]",
            "Date":"[!Au::Date!]",
            "Tag":"[!Au::Tag!]"
            }
        [/STORPROC]
    ]
}
