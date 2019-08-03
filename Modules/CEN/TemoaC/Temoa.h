
#ifndef TEMOA_H
#define TEMOA_H

extern "C" {
#include "php.h"
}

#include "RTF.h"

using namespace std;

class cTemoa {
private:
	std::stringstream DocStream;
	cMarkList MarkList;
	cLines Lines;
	cNoteList NoteList;
	cTargetList TargetList;
	cArrowList ArrowList;
	cFilterList FilterList;
	std::list<std::string> Corpus;

	bool LstMode;
	short OrtOrtho;
	bool OrtUpper;
	bool OrtModif;
	bool OrtGenoa;
	bool OrtSepar;
	bool OrtSymbol;
	short OrtPlace;
	short OrtContx;
	bool OrtAll;
	bool OrtOrder;
	bool OrtExclu;
	bool OrtAsOrt;
	short OrtResult;
	bool OrtResSym;
	bool OrtCorpus;
	int OrtLevel;
	bool OrtSvGenoa;
	
	void FindTarget(cArrowList *lst, cFilterList *flt, char *file);
	bool CheckTarget(cTTargetList *tlist, cArrowList *alist);	
	int CheckFilter(unsigned char *p, short len, cFilterList *flt, bool symbol);	
	void AddWord(cTTarget *tt, short wlen, char *file);	
	void FindPage(int p, int *page, int *folio, int *verso, int *vign, cPict **pict, int *refer, int *refend, bool refpos);
	bool OpenFile(std::string file);

public:
	cTemoa();
	bool SetRules(const char *rules);
	void SetCorpus(const char *corpus);
	void AddArrow(const char *word);
	bool Search();
	void ClearArrows();
	void ClearTargets();
	int TargetCount();
	const char* GetTargetText(int n);
	const char* GetTargetsJson();
	
public:
    // Here we store our callback function
    zend_fcall_info fci_onTemoa;
    zend_fcall_info_cache fcc_onTemoa;

};

#endif /* TEMOA_H */

