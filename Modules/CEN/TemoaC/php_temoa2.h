#ifndef PHP_TEMOA2_H
#define PHP_TEMOA2_H

#define PHP_TEMOA2_EXTNAME  "temoa2"
#define PHP_TEMOA2_EXTVER   "1.0"

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif 

extern "C" {
#include "php.h"
}

extern zend_module_entry temoa2_module_entry;
#define temoa_module_ptr &temoa2_module_entry
#define phpext_temoa_ptr temoa2_module_ptr


#endif /* PHP_TEMOA2_H */

