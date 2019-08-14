#ifndef ToolsH
#define ToolsH

#include <stdio.h>
#include <string.h>    //strlen
#include <stdlib.h>    //strlen
#include <iostream>
#include <fstream>
#include <cstdlib>
#include <pthread.h>
#include <list>
#include <time.h>
#include <ctime>
#include <string>
#include <sstream>


#include <boost/format.hpp>
#include <boost/algorithm/string.hpp>


using namespace std;

enum {tsDateTime, tsDate, tsTime};

std::string itos(int i);
std::string lXtos(unsigned long i);
std::string cotes(std::string s);
std::string ts(time_t t);
time_t timelc();
std::string tstos(time_t t, int mode);
time_t dtots(double d);
double tstod(time_t t);
time_t ctots(const char *c);

void RenameFile(std::string from, std::string to);
void CopyFile(std::string from, std::string to);
bool FileExists(std::string file, bool dir=false);
void CreateDir(std::string dir);
std::string FilePath(std::string file);
std::string FileName(std::string file);
int FileSize(const std::string file);

#endif
