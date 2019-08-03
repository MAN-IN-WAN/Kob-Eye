PHP_ARG_ENABLE(temoa2,
    [Whether to enable the "Temoa2" extension],
    [  --enable-temoa2         Enable "Temoa2" extension support])

if test $PHP_TEMOA2 != "no"; then
    PHP_REQUIRE_CXX()
    PHP_SUBST(TEMOA2_SHARED_LIBADD)
    PHP_ADD_LIBRARY(stdc++, 1, TEMOA2_SHARED_LIBADD)
    PHP_NEW_EXTENSION(temoa2, temoa2.cc Temoa.cpp RTF.cpp Target.cpp Tools.cpp, $ext_shared)
fi

