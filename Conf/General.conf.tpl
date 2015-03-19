<CONF access="sys">
	<GENERAL>
		<AUTH>
			<USER_CHECK_RIGHTS type="const">1</USER_CHECK_RIGHTS>
			<PHP_SESSION_NAME type="const">KE_SESSID</PHP_SESSION_NAME>
			<CONNECT_TIMEOUT type="const">20</CONNECT_TIMEOUT>
			<MAIN_USER_NUM type="const">1</MAIN_USER_NUM>
			<MAIN_SKIN_NUM type="const">Login</MAIN_SKIN_NUM>
			<SHARED_SKIN type="const">Login</SHARED_SKIN>
		</AUTH>
		<CACHE>
			<CONF_CACHE type="const">0</CONF_CACHE>
			<SCHEMA_CACHE type="const">0</SCHEMA_CACHE>
			<SKIN_CACHE type="const">0</SKIN_CACHE>
			<MODULE_CACHE type="const">0</MODULE_CACHE>
			<USER_CACHE type="const">0</USER_CACHE>
			<SQL_CACHE type="const">0</SQL_CACHE>
			<SQL_LITE_CACHE type="const">0</SQL_LITE_CACHE>
		</CACHE>
		<BDD>
			<SQL_MAX_LIMIT type="const">200</SQL_MAX_LIMIT>
			<MAIN_DB_PREFIX type="const">Kob-</MAIN_DB_PREFIX>
			<MAIN_DB_RECURSIV_LIMIT type="const">0</MAIN_DB_RECURSIV_LIMIT>
			<BDD_DSN>mysql://[BDD_USER]:[BDD_PASS]@[BDD_HOST]/[BDD_DBNAME]</BDD_DSN>
		</BDD>
		<SERVER>
			<CHARSET_CODE type="const">UTF-8</CHARSET_CODE>
			<ADD_CONNECT type="const">1</ADD_CONNECT>
			<BLOC_MAX_PARAMS type="const">15</BLOC_MAX_PARAMS>
			<DEFAULT_LINK>Systeme</DEFAULT_LINK>
			<MULTITHREAD type="const">1</MULTITHREAD>
		</SERVER>
		<LANGUAGE>
			<FR>
				<DEFAULT>1</DEFAULT>
				<TITLE>Francais</TITLE>
				<ICON>/Skins/AdminV2/Img/drapeau-france.jpg</ICON>
				<NAV>*</NAV>
			</FR>
		</LANGUAGE>
		<AUTO_COMPLETE_LANG type="const">1</AUTO_COMPLETE_LANG>
	</GENERAL>
	<KEML file="Conf/Keml.conf" />
	<PROCESS file="Conf/Process.conf" />
	<MODULE access="user">
		<SYSTEME file="Modules/Systeme/Systeme.conf" />
		<EXPLORATEUR file="Modules/Explorateur/Explorateur.conf" />
		<REDACTION file="Modules/Redaction/Redaction.conf" />
	</MODULE>
	<SKIN access="user">
	</SKIN>
</CONF>
