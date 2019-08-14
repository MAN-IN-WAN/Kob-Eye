#include "php_temoa2.h"
#include "Temoa.h"

zend_object_handlers temoa_object_handlers;

typedef struct _temoa_object {
    cTemoa *temoa;
    zend_object std;
} temoa_object;

static inline temoa_object *php_temoa_obj_from_obj(zend_object *obj) {
    return (temoa_object*)((char*)(obj) - XtOffsetOf(temoa_object, std));
}

#define Z_TEMOAOBJ_P(zv)  php_temoa_obj_from_obj(Z_OBJ_P((zv)))

zend_class_entry *temoa_ce;

PHP_METHOD(Temoa, __construct)
{
    zval *id = getThis();
    temoa_object *intern;

    intern = Z_TEMOAOBJ_P(id);
    if(intern != NULL) {
        intern->temoa = new cTemoa();
    }
}

PHP_METHOD(Temoa, SetRules)
{
    char *rules = NULL;
	size_t length = 0;
    zval *id = getThis();
    temoa_object *intern;
    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &rules, &length) == FAILURE) {
        RETURN_NULL();
    }

    intern = Z_TEMOAOBJ_P(id);
    if(intern != NULL) {
        RETURN_BOOL(intern->temoa->SetRules(rules));
    }
    RETURN_NULL();
}

PHP_METHOD(Temoa, SetCorpus)
{
    char *corpus;
	size_t length = 0;
    zval *id = getThis();
    temoa_object *intern;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &corpus, &length) == FAILURE) {
        RETURN_NULL();
    }

    intern = Z_TEMOAOBJ_P(id);
    if(intern != NULL) {
        intern->temoa->SetCorpus(corpus);
    }
    RETURN_NULL();
}

PHP_METHOD(Temoa, AddArrow)
{
    char *word;
	size_t length = 0;
    zval *id = getThis();
    temoa_object *intern;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &word, &length) == FAILURE) {
        RETURN_NULL();
    }

    intern = Z_TEMOAOBJ_P(id);
    if(intern != NULL) {
        intern->temoa->AddArrow(word);
    }
    RETURN_NULL();
}

PHP_METHOD(Temoa, Search)
{
    zval *id = getThis();
    temoa_object *intern;

    intern = Z_TEMOAOBJ_P(id);
    if(intern != NULL) {
        RETURN_BOOL(intern->temoa->Search());
    }
    RETURN_NULL();
}

PHP_METHOD(Temoa, ClearArrows)
{
    zval *id = getThis();
    temoa_object *intern;

    intern = Z_TEMOAOBJ_P(id);
    if(intern != NULL) {
        intern->temoa->ClearArrows();
    }
    RETURN_NULL();
}

PHP_METHOD(Temoa, ClearTargets)
{
    zval *id = getThis();
    temoa_object *intern;

    intern = Z_TEMOAOBJ_P(id);
    if(intern != NULL) {
        intern->temoa->ClearTargets();
    }
    RETURN_NULL();
}

PHP_METHOD(Temoa, TargetCount)
{
	long count = 0;
    zval *id = getThis();
    temoa_object *intern;

    intern = Z_TEMOAOBJ_P(id);
    if(intern != NULL) {
        count = intern->temoa->TargetCount();
    }
    RETURN_LONG(count);
}


PHP_METHOD(Temoa, GetTargetText)
{
    long index;
    zval *id = getThis();
    temoa_object *intern;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "l", &index) == FAILURE) {
        RETURN_NULL();
    }

    intern = Z_TEMOAOBJ_P(id);
    if(intern != NULL) {
        RETURN_STRING(intern->temoa->GetTargetText((int)index), 1);
    }
    RETURN_NULL();
}


PHP_METHOD(Temoa, GetTargetsJson)
{
    zval *id = getThis();
    temoa_object *intern;

    intern = Z_TEMOAOBJ_P(id);
    if(intern != NULL) {
        RETURN_STRING(intern->temoa->GetTargetsJson(), 1);
    }
    RETURN_NULL();
}


PHP_METHOD(Temoa, doTemoa)
{
    zval *args = NULL;
    int argc, i;

    zval retval;
    zend_fcall_info fci;
    zend_fcall_info_cache fci_cache;

    zval *id = getThis();
    temoa_object *intern;

    intern = Z_TEMOAOBJ_P(id);
    if(intern != NULL) {
        memcpy(&fci, &intern->temoa->fci_onTemoa, sizeof(fci));
        memcpy(&fci_cache, &intern->temoa->fcc_onTemoa, sizeof(fci_cache));
        fci.retval = &retval;

        ZEND_PARSE_PARAMETERS_START(0, -1)
            Z_PARAM_VARIADIC('*', args, argc)
        ZEND_PARSE_PARAMETERS_END();

        if(argc > 0) {
            fci.params = args;
            fci.param_count = argc;
        }

        if (zend_call_function(&fci, &fci_cache) == SUCCESS && Z_TYPE(retval) != IS_UNDEF) {
            if (Z_ISREF(retval)) {
                zend_unwrap_reference(&retval);
            }
            ZVAL_COPY_VALUE(return_value, &retval);
        }
    }
}

PHP_METHOD(Temoa, onTemoa)
{
    zval *args = NULL;
    int argc, i;

    zval *id = getThis();
    temoa_object *intern;

    intern = Z_TEMOAOBJ_P(id);
    if(intern != NULL) {
        ZEND_PARSE_PARAMETERS_START(1, -1)
            Z_PARAM_FUNC(intern->temoa->fci_onTemoa, intern->temoa->fcc_onTemoa)
            Z_PARAM_VARIADIC('*', args, argc)
        ZEND_PARSE_PARAMETERS_END();
    }

    intern->temoa->fci_onTemoa.param_count = argc;
    if(argc > 0) {
        intern->temoa->fci_onTemoa.params = (zval*)safe_emalloc(intern->temoa->fci_onTemoa.param_count, sizeof(zval), 0);
        for(i = 0; i < argc; i++) {
            zval *arg = args + i;
            ZVAL_COPY_VALUE(&intern->temoa->fci_onTemoa.params[i], arg);
        }
    }
}

PHP_METHOD(Temoa, temoaCallback)
{
    zval retval;
    zend_fcall_info fci;
    zend_fcall_info_cache fci_cache;

    ZEND_PARSE_PARAMETERS_START(1, -1)
        Z_PARAM_FUNC(fci, fci_cache)
        Z_PARAM_VARIADIC('*', fci.params, fci.param_count)
    ZEND_PARSE_PARAMETERS_END();

    fci.retval = &retval;

    if (zend_call_function(&fci, &fci_cache) == SUCCESS && Z_TYPE(retval) != IS_UNDEF) {
        if (Z_ISREF(retval)) {
            zend_unwrap_reference(&retval);
        }
        ZVAL_COPY_VALUE(return_value, &retval);
    }
}

ZEND_BEGIN_ARG_INFO_EX(arginfo_temoacallback, 0, 0, 1)
    ZEND_ARG_CALLABLE_INFO(0, cbfn, 0)
ZEND_END_ARG_INFO();

ZEND_BEGIN_ARG_INFO_EX(arginfo_ontemoa, 0, 0, 1)
    ZEND_ARG_CALLABLE_INFO(0, cbfn, 0)
ZEND_END_ARG_INFO();

const zend_function_entry temoa_methods[] = {
    PHP_ME(Temoa,  __construct,     NULL, ZEND_ACC_PUBLIC | ZEND_ACC_CTOR)
    PHP_ME(Temoa,  SetCorpus,       NULL, ZEND_ACC_PUBLIC)
    PHP_ME(Temoa,  SetRules,       NULL, ZEND_ACC_PUBLIC)
    PHP_ME(Temoa,  AddArrow,        NULL, ZEND_ACC_PUBLIC)
    PHP_ME(Temoa,  Search,          NULL, ZEND_ACC_PUBLIC)
    PHP_ME(Temoa,  ClearArrows,  	 NULL, ZEND_ACC_PUBLIC)
    PHP_ME(Temoa,  ClearTargets,  	 NULL, ZEND_ACC_PUBLIC)
    PHP_ME(Temoa,  TargetCount,  	 NULL, ZEND_ACC_PUBLIC)
    PHP_ME(Temoa,  GetTargetText,  	 NULL, ZEND_ACC_PUBLIC)
    PHP_ME(Temoa,  GetTargetsJson,   NULL, ZEND_ACC_PUBLIC)
    PHP_ME(Temoa,  temoaCallback,    arginfo_temoacallback, ZEND_ACC_PUBLIC)
    PHP_ME(Temoa,  onTemoa,          arginfo_ontemoa, ZEND_ACC_PUBLIC)
    PHP_ME(Temoa,  doTemoa,          NULL, ZEND_ACC_PUBLIC)
    PHP_FE_END
};

zend_object *temoa_object_new(zend_class_entry *ce TSRMLS_DC)
{
    temoa_object *intern = (temoa_object*)ecalloc(1,
            sizeof(temoa_object) +
            zend_object_properties_size(ce));

    zend_object_std_init(&intern->std, ce TSRMLS_CC);
    object_properties_init(&intern->std, ce);

    intern->std.handlers = &temoa_object_handlers;

    return &intern->std;
}

static void temoa_object_destroy(zend_object *object)
{
    temoa_object *my_obj;
    my_obj = (temoa_object*)((char *)object - XtOffsetOf(temoa_object, std));

    /* Now we could do something with my_obj->my_custom_buffer, like sending it
       on a socket, or flush it to a file, or whatever, but not free it here */

    zend_objects_destroy_object(object); /* call __destruct() from userland */
}

static void temoa_object_free(zend_object *object)
{
    temoa_object *my_obj;
    my_obj = (temoa_object *)((char *)object - XtOffsetOf(temoa_object, std));
    delete my_obj->temoa;
    zend_object_std_dtor(object); /* call Zend's free handler, which will free object properties */
}

PHP_MINIT_FUNCTION(temoa2)
{
    zend_class_entry ce;
    INIT_CLASS_ENTRY(ce, "temoa2\\Temoa", temoa_methods);
    temoa_ce = zend_register_internal_class(&ce TSRMLS_CC);
    temoa_ce->create_object = temoa_object_new;

    memcpy(&temoa_object_handlers, zend_get_std_object_handlers(), sizeof(temoa_object_handlers));

    temoa_object_handlers.free_obj = temoa_object_free; /* This is the free handler */
    temoa_object_handlers.dtor_obj = temoa_object_destroy; /* This is the dtor handler */
    temoa_object_handlers.offset   = XtOffsetOf(temoa_object, std); /* Here, we declare the offset to the engine */

    return SUCCESS;
}

zend_module_entry temoa2_module_entry = {
#if ZEND_MODULE_API_NO >= 20010901
    STANDARD_MODULE_HEADER,
#endif
    PHP_TEMOA2_EXTNAME,
    NULL,                  /* Functions */
    PHP_MINIT(temoa2),
    NULL,                  /* MSHUTDOWN */
    NULL,                  /* RINIT */
    NULL,                  /* RSHUTDOWN */
    NULL,                  /* MINFO */
#if ZEND_MODULE_API_NO >= 20010901
    PHP_TEMOA2_EXTVER,
#endif
    STANDARD_MODULE_PROPERTIES
};

#ifdef COMPILE_DL_TEMOA2
extern "C" {
ZEND_GET_MODULE(temoa2)
}
#endif
