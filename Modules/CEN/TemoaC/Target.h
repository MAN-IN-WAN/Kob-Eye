//---------------------------------------------------------------------------

#ifndef TargetH
#define TargetH


#include <stdio.h>
#include <string.h>
#include <string>
#include <list>

#include "Classes.h"


typedef std::list<std::string>::iterator StringIterator;

class cStringList : public std::list<std::string> {
public:
	inline std::string String(StringIterator it) { return *it; }
};


class cData {
public:
	unsigned char *Data;
	int Len;
	int Max;
	int Block;

	cData(int blk);
	~cData();
	void Add(unsigned char c);
};

class cLines {
private:
	int Max;
	int *Lines;
	short *Lens;

public:
	int Count;

	cLines();
	~cLines();
	bool Add(int p, short l);
	int Pos(int n);
	short Len(int n);
	void Clear();
	int Find(int p);
};

class cStart {
public:
	int Pos;
	int Len;
	int CtxPos;
	int CtxLen;
	int CtxMem;
	int CtxMemLen;
	int Page;
	int Folio;
	int Verso;
	char *CtxBegin;
	int DebPara, DebSent, DebWord, FinPara, FinSent, FinWord;
	cExtract *Extract;

	cStart();
};

typedef std::list<cStart*>::iterator StartIterator;

class cStartList : public std::list<cStart*> {
public:
	~cStartList() { Destroy(); }
	inline cStart* Start(StartIterator it) { return *it; }
	void Destroy();
};

class cArrow {
public:
	char *Arrow;
	char *Text;
	short ArrowLength;
	short TextLength;
	char *File;
	short Place;
	short Exclude;
	int Order;
	int Found;
	bool OrderCheck;
	bool Genor;
	cArrow *GenFrom;

	short Index;
	int Pos;
	int CtxPos;
	int CtxMem;
	unsigned char *BegWord;
	unsigned char *BegTarget;
	int DebPara, DebSent, DebWord;
	bool IsBegWord;


	cArrow();
	~cArrow();
	cArrow(const char *s);
	void SetText(const char *s);
	void Upper();
	void Substitute();
	bool Compare(unsigned char c, cCompare *cmp, int index);
	inline int Compare(cArrow* arrow) { return strcmp(Text, arrow->Text); }
};

//int CompareArrowUp(void* Item1, void* Item2) {
//	return ((cArrow*) Item1)->Compare((cArrow*) Item2);
//}
//
//int CompareArrowDown(void* Item1, void* Item2) {
//	return ((cArrow*) Item2)->Compare((cArrow*) Item1);
//}
int CompareArrowUp(void* Item1, void* Item2);
int CompareArrowDown(void* Item1, void* Item2);



typedef std::list<cArrow*>::iterator ArrowIterator;

class cArrowList : public std::list<cArrow*> {
public:
	~cArrowList(void) { Destroy(); };
	void Raz(void);
	void Destroy(void);
	inline cArrow* Arrow(ArrowIterator it) { return *it; }
	void ClearGenoa();
	bool Find(char *s);
	void SortByArrowUp() { sort(CompareArrowUp); }
	void SortByArrowDown() { sort(CompareArrowDown); }
	int LastOrder();
};

class cCompare {
public:
	int DebWord, DebSent, DebPara;
	unsigned char *Mem, *BegWord, *BegCtx;
	int Pos, CtxPos, CtxMem;
	cTTargetList *TTLst;

	cCompare();
	~cCompare();
};

class cTTarget {
public:
	cArrow *Arrow;
	unsigned char *BegWord, *EndWord, *BegTarget;
	int DebPara, FinPara, DebSent, FinSent, DebWord, FinWord;
	int Pos, Length, LenTarget, Index, LenWord, CtxPos, CtxLen, CtxMem, CtxMemLen;
	bool BadPlace, IsBegWord, IsEndWord;

	cTTarget();
};

typedef std::list<cTTarget*>::iterator TTargetIterator;

class cTTargetList : public std::list<cTTarget*> {
public:
	~cTTargetList(void) { Destroy(); };
	void Destroy(void);
	inline cTTarget* TTarget(TTargetIterator it) { return *it; }
	void EndWord(unsigned char *endWord, int finWord, unsigned char sep);
	void EndCtx(int pos, int posMem);
};

class cTarget {
public:
	char *Text;
	short Length;
	cStartList *Starts;
	StartIterator Index;
	int ArrowIndex;
	char *File;
	char *Path;

	cTarget();
	~cTarget();

	int Start(StartIterator it) { return Starts->Start(it)->Pos; }
	void Add(int pos, short len, int ctxPos, int ctxLen, int ctxMem, int ctxMemLen, short page, short folio, short verso);
	inline void Add(cStart *start) { Starts->push_back(start); }
	void First();
	void Next();
	void Prev();
	void Last();

	int CurrPos() { return Starts->Start(Index)->Pos; }
	short CurrLen() { return Starts->Start(Index)->Len; }
	int CurrCtxPos() { return Starts->Start(Index)->CtxPos; }
	short CurrCtxLen() { return Starts->Start(Index)->CtxLen; }
	int StrCmpModified(unsigned char *w);
};


class cExtract {
public:
	std::string Text;
	std::string File;
	int Debut;
	int Page;
	int Folio;
	int Verso;
	std::string Balise;

	cExtract();
};

typedef std::list<cExtract*>::iterator ExtractIterator;

class cExtractList : public std::list<cExtract*> {
public:
	~cExtractList(void) { Destroy(); }
	void Destroy(void);
	inline cExtract* Extract(ExtractIterator it) { return *it; }
	cExtract* Find(std::string file, int page, int folio, int verso);
	cExtract* Find(std::string file, int debut);
};


//=======================================================

class cGenoa {
public:
	int Level, Len, ExpLen;
	char *What, *With, *Expression;

	cGenoa();
	~cGenoa();
	bool Test(char *word, int len, int pos, int level);
};

typedef std::list<cGenoa*>::iterator GenoaIterator;

class cGenoaList : public std::list<cGenoa*> {
public:
	~cGenoaList(void) { Destroy(); }
	void Destroy(void);	
	inline cGenoa* Genoa(GenoaIterator it) { return *it; }
	bool Apply(std::string s, std::list<std::string> *lst, std::list<std::string> *tlst, int level, FILE *f);
};


//=======================================================




#define SORTBYNUM        0
#define SORTBYNUMDESC    1
#define SORTBYTEXT       2
#define SORTBYTEXTDESC   3
#define SORTBYTEXTR      4
#define SORTBYTEXTRDESC  5

int TargetListSortByText(void *item1, void *item2);
int TargetListSortByNum(void *item1, void *item2);
int TargetListSortByDocText(void *item1, void *item2);
int TargetListSortByDocNum(void *item1, void *item2);
int TargetListSortByTextDesc(void *item1, void *item2);
int TargetListSortByNumDesc(void *item1, void *item2);
int TargetListSortByDocTextDesc(void *item1, void *item2);
int TargetListSortByDocNumDesc(void *item1, void *item2);
// 0903
int strRcmp(char *t1, char *t2);
int TargetListSortByTextR(void *item1, void *item2);
int TargetListSortByTextRDesc(void *item1, void *item2);
int TargetListSortByDocTextR(void *item1, void *item2);
int TargetListSortByDocTextRDesc(void *item1, void *item2);

typedef std::list<cTarget*>::iterator TargetIterator;

class cTargetList : public std::list<cTarget*> {
public:
	~cTargetList(void) { Destroy(); }
	void Destroy(void);
	inline cTarget* Target(TargetIterator it) { return *it; }
	void EndWord(int finWord, unsigned char sep);
	cTarget* Find(char *text);
};

class cStrPair {
public:
	char *What;
	char *With;
	short WhatLen;
	short WithLen;

	cStrPair(char *what, char *with, short whatLen, short withLen);
	~cStrPair();
};

typedef struct {
	short len;
	char *with;
} sSubstStruct;

class cFilter {
public:
	char *Text;
	short Len;
	short Place;

	cFilter(char *s, short len);
	~cFilter();
};

typedef std::list<cFilter*>::iterator FilterIterator;

class cFilterList : public std::list<cFilter*> {
public:

	~cFilterList(void) { Destroy(); }
	void Destroy(void);
	void AddFilter(char *s);
	inline cFilter* Filter(FilterIterator it) { return *it; }
	cFilter* Find(char *s);
};



//class cControl : public TControl {
//__published:
//	__property Caption;
//};


//---------------------------------------------------------------------------
#define MARKER_LEN   6

#define MARKER_PAGE  0
#define MARKER_RECTO 1
#define MARKER_VERSO 2
#define MARKER_GLYPH 3
#define MARKER_NOTE  4
#define MARKER_LIGNE 5

extern unsigned char MarkPage;
extern unsigned char MarkRecto;
extern unsigned char MarkVerso;
extern unsigned char MarkGlyph;
extern unsigned char MarkNote;
extern unsigned char MarkLine;
extern unsigned char MarkRefer; 
extern unsigned char Uppers[256];
extern unsigned char Substitutions[256];
extern unsigned char Separators[256];
extern unsigned char Vowels[256];
extern unsigned char Consonants[256];
extern cStrPair **StrPairs;
extern int StrPairsNum;
extern char **Filters;
extern int FiltersNum;
extern std::string Language;
extern cGenoaList *GenoaList;
extern int GenoaMax;


void RulesInit();
bool RulesRead(std::string file);
void TradInit();
bool TradRead(std::string file);
char * TradText(char *s);
//void MenuLabels(std::string form, TMenuItem *wc);
//void ControlLabels(std::string form, TWinControl *wc);
unsigned char GetChar(unsigned char *(&s));

void GenoaInit();
bool GenoaRead(std::string file);
int TextFieldLen(char *fld, char sep, char *&next);
int eval(char *expr, int len, char *word, int wlen, int pos);
void Genor(cStringList *lst, std::string s, int level, FILE *f);


void AllocStr(char * &string, char *value);
void AllocStr(char * &string, char *value, int len);
void Upper(char *dst, char *src, int len);
void Upper(char *s);
char Substitute(char * &org, sSubstStruct *ss);
int UpperStrCmp(char *what, char *with);
int UpperStrNCmp(char *what, char *with, int len);
int SeparStrNCmp(char *what, char *with, int len, bool symbol);
//void LoadFilters(TCheckListBox *lst);
char * FindFilter(char *s);
unsigned char Modif(unsigned char *s);

std::string DocumentFile(std::string file);
std::string FileDocument(std::string file, std::string path);
std::string DocumentFileName(std::string file);

#endif
