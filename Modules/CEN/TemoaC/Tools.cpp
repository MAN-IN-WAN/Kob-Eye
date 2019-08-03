#include "Tools.h"
#include <math.h>
#include <sys/stat.h>

//#include <boost/filesystem.hpp>
//namespace fs=boost::filesystem;


std::string itos(int i)
{
	char b[32];
	sprintf(b, "%d", i);
	std::string s = b;
	return s;
}

std::string lXtos(unsigned long i)
{
	char b[32];
	sprintf(b, "%lX", i);
	std::string s = b;
	return s;
}


std::string cotes(std::string s) {
	return boost::replace_all_copy(s, "'", "''");
}

std::string ts(time_t t) {
	if(! t) return std::string("0000-00-00 00:00:00");
	char buff[22];
	strftime(buff, 22, "%Y-%m-%d %H:%M:%S", gmtime(&t));
	return std::string(buff);
}

std::string tstos(time_t t, int mode) {
	char buff[22];
	const char *fmt;
	switch(mode) {
	case 0: fmt = "%Y%m%d%H%M%S"; break;
	case 1: fmt = "%Y%m%d"; break;
	case 2: fmt = "%H%M%S"; break;
	case 3: fmt = "%H:%M:%S"; break;
	}
	strftime(buff, 22, fmt, gmtime(&t));
	return std::string(buff);
}

time_t timelc() {
	time_t t = time(NULL);
	return t + (timegm(localtime(&t)) - t);
}

double tss = 86400;
double tsf = 25569;

time_t dtots(double d) {
	time_t t = round((d - tsf) * tss);
	return t;
}

double tstod(time_t t) {
	double d  = (t / tss) + tsf;
	return d;
}



time_t ctots(const char *c) {
	static std::string last;

	struct tm stm;
	memset(&stm, 0, sizeof(stm));
	strptime(c, "%Y-%m-%d %H:%M:%S", &stm);
	time_t t = timegm(&stm);
	if(t < 0) t = 0;
	return t;
}

void RenameFile(std::string from, std::string to) {
	remove(to.c_str());
	CreateDir(FilePath(to));
	if(rename(from.c_str(), to.c_str()) != 0) {
		std::string s = "xxx "; s += errno;
		perror(s.c_str());
	}
}

void CopyFile(std::string from, std::string to) {
	CreateDir(FilePath(to));

	ifstream source(from.c_str(), ios::binary);
	ofstream dest(to.c_str(), ios::binary);

	istreambuf_iterator<char> begin_source(source);
	istreambuf_iterator<char> end_source;
	ostreambuf_iterator<char> begin_dest(dest);
	copy(begin_source, end_source, begin_dest);

	source.close();
	dest.close();
}

bool FileExists(const std::string file, bool dir) {
  struct stat buffer;
  if(stat(file.c_str(), &buffer) == 0) {
	  return !dir || (buffer.st_mode & S_IFDIR);
  }
  return false;
}


int FileSize(const std::string file) {
  struct stat buffer;
  if(stat(file.c_str(), &buffer) == 0) {
	  return buffer.st_size;
  }
  return -1;
}

void CreateDir(std::string dir) {
	if(FileExists(dir, true)) return;
	std::string path;

	for(size_t p = 1; p != string::npos;) {
		p = dir.find("/", p);
		if(p ==	string::npos) path = dir;
		else path = dir.substr(0, p++);
		if(! FileExists(path, true))
			mkdir(path.c_str(), 0777);
	}
}

/*
std::string FilePath(std::string file) {
	size_t found;
	found = file.find_last_of("/");
	return file.substr(0, found);
}

std::string FileName(std::string file) {
	size_t found;
	found = file.find_last_of("/");
	return file.substr(found+1);
}
*/