
[OBJ Systeme|Event|E]

[!Events:=[!E::pollEvents([!pollModule!],[!pollObject!],[!pollStart!],[!pollInterval!],[!pollDuration!])!]!]


{
    "lastSearch": "[!TMS::Now!]",
    [COUNT [!Events!]|Nb]
    "total": [!Nb!],
    "module": "[!pollModule!]",
    "results":
    [
        [STORPROC [!Events!]|Ev]
            {
                "Module":"[!Ev::EventModule!]",
                "ObjectClass":"[!Ev::EventObjectClass!]",
                "ObjectId":"[!Ev::EventId!]",
                "Titre":"[!Ev::Titre!]",
                "Type":"[!Ev::EventType!]",
                "Id":"[!Ev::Id!]"
            }
        [/STORPROC]
    ]
}


