[LIB libCas|LB]
[IF [!LB::check()!]]
    <h1>OK</h1>
    <a href="https://[!CONF::MODULE::PROXYCAS::PROXYCAS_URL!]/logout">Se déconnecter</a>
    [REDIRECT]https://intranet.unibio.fr[/REDIRECT]
[ELSE]
    <h1>NOK</h1>
    [REDIRECT]https://[!CONF::MODULE::PROXYCAS::PROXYCAS_URL!]/login?service=https://intranet.unibio.fr[/REDIRECT]
[/IF]