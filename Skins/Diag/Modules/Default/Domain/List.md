[!Files:=[!Array::newArray()!]!]
[!Files:=[!Array::push([!Files!],Client,getDomCli)!]!]
[!Files:=[!Array::push([!Files!],Web chez abtel,digA)!]!]
[!Files:=[!Array::push([!Files!],Mail chez abtel,digMX)!]!]
[!Files:=[!Array::push([!Files!],DNS chez abtel,digNS)!]!]

[MODULE Systeme/Utils/List?Chemin=[!Query!]&ACols=[!Files!]]