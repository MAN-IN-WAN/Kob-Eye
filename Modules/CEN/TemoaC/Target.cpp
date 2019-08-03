
#include "Target.h"

enum {
	OPDINV = 1, OPDMIS, OPDUWD, OPRINV, OPRUWD, BRAMIS, SCLMIS, DIVNUL, VARINV, COTMIS
};
int EvalErr = 0;


//---------------------------------------------------------------------------

cData::cData(int blk) {
	Len = 0;
	Max = Block = blk;
	Data = (unsigned char *) malloc(Max);
}

cData::~cData() {
	if (Data) free(Data);
}

void cData::Add(unsigned char c) {
	if (Len > Max) {
		Max += Block;
		try {
			unsigned char *p = (unsigned char *) realloc(Data, Max);
			if (p)
				Data = p;
		} catch (const std::exception& e) {
		}
	}
	*(Data + Len++) = c;
}

cLines::cLines() {
	Max = 0;
	Lines = NULL;
	Lens = NULL;
	Count = 0;
}

cLines::~cLines() {
	if (Lines) free(Lines);
	if (Lens) free(Lens);
}

int cLines::Pos(int n) {
	if (Count) {
		if (Count > n)
			return *(Lines + n);
		else
			return *(Lines + Count - 1);
	} else
		return 0;
}

short cLines::Len(int n) {
	if (Count) {
		if (Count > n)
			return *(Lens + n);
		else
			return *(Lens + Count - 1);
	} else
		return 0;
}

bool cLines::Add(int p, short l) {
	if (Count == Max) {
		Max += 16;
		int *pp = (int *) realloc(Lines, Max * sizeof (int));
		if (!pp) return false;
		Lines = pp;
		short *pl = (short *) realloc(Lens, Max * sizeof (short));
		if (!pl) return false;
		Lens = pl;
	}
	*(Lines + Count) = p;
	*(Lens + Count++) = l;
	return true;
}

void cLines::Clear() {
	if (Lines) free(Lines);
	if (Lens) free(Lens);
	memset((void *) this, 0, sizeof (cLines));
}


int cLines::Find(int p) {
	int l = -1;
	for (int i = 0; i < Count; i++) {
		if (p >= *(Lines + i))
			l = i;
		else
			break;
	}
	return l;
}

cStart::cStart() {
	memset((void *) this, 0, sizeof (cStart));
}

void cStartList::Destroy() {
	StartIterator it = begin(), iend = end();
	for (; it != iend; it++) delete Start(it);
	clear();
}


cCompare::cCompare() {
	DebWord = DebSent = DebPara = 0;
	Mem = BegWord = BegCtx = NULL; 
	Pos = CtxPos = CtxMem = 0;
	TTLst = new cTTargetList();
}

cCompare::~cCompare() {
	delete TTLst;
}

cTTarget::cTTarget() {
	memset((void *) this, 0, sizeof (*this));
}

void cTTargetList::Destroy() {
	TTargetIterator it = begin(), iend = end();
	for (; it != iend; it++) delete TTarget(it);
	clear();
}

void cTTargetList::EndWord(unsigned char *endWord, int finWord, unsigned char sep) {
	cTTarget *tt;
	bool ending, bad = false;

//	TTargetIterator it = begin(), iend = end();
	for (auto it = begin(), iend = end(); it != iend; it++) {
		tt = TTarget(it);

		// debut et fin pour sorties
		switch (sep) {
			case 4:
				if (!tt->FinPara) tt->FinPara = finWord;
			case 3:
				if (!tt->FinSent) tt->FinSent = finWord;
			case 2:
				if (!tt->FinWord) tt->FinWord = finWord;
		}

		if (!tt->EndWord) {
			tt->EndWord = endWord;
			ending = tt->BegTarget + tt->LenTarget - 1 == endWord;
			switch (tt->Arrow->Place) {
					//      case 1:
					//        bad = ! tt->IsBegWord;
					//        break;
				case 2:
					bad = !ending;
					break;
				case 3:
					bad = tt->IsBegWord || ending;
					break;
				case 4:
					bad = !(tt->IsBegWord && ending);
			}
			if (bad) {
				tt->Arrow->Found--;
				erase(it);
				delete tt;
			} //else i++;
		} //else i++; 
	}
}

void cTTargetList::EndCtx(int pos, int posMem) {
	cTTarget *tt;

	TTargetIterator it = begin(), iend = end();
	for (; it != iend; it++) {
		tt = TTarget(it);
		if (!tt->CtxMemLen) {
			tt->CtxMemLen = posMem - tt->CtxMem + 1;
			tt->CtxLen = pos - tt->CtxPos + 1;
		}
	}
}

cExtract* cExtractList::Find(std::string file, int page, int folio, int verso) {
	ExtractIterator it = begin(), iend = end();
	for (; it != iend; it++) {
		cExtract *e = Extract(it);
		if (e->File == file && e->Page == page && e->Folio == folio && e->Verso == verso)
			return e;
	}
	return NULL;
}

cExtract* cExtractList::Find(std::string file, int debut) {
	ExtractIterator it = begin(), iend = end();
	for (; it != iend; it++) {
		cExtract *e = Extract(it);
		if (e->File == file && e->Debut == debut)
			return e;
	}
	return NULL;
}

cArrow::cArrow() {
	memset((void *) this, 0, sizeof (cArrow));
}

cArrow::cArrow(const char *s) {
	memset((void *) this, 0, sizeof (cArrow));
	SetText(s);
}

cArrow::~cArrow() {
	if (Arrow) free(Arrow);
	if (Text) free(Text);
}

void cArrow::SetText(const char *s) {
	AllocStr(Arrow, (char *)s);
	AllocStr(Text, (char *)s);
	TextLength = ArrowLength = strlen(s);
}

bool cArrow::Compare(unsigned char c, cCompare *cmp, int index) {
	if (c == (unsigned char) Text[Index]) {
		if (!Index) {
			IsBegWord = cmp->Mem == cmp->BegWord;
			bool doit = true;
			switch (Place) {
				case 1:
				case 4:
					doit = IsBegWord;
					break;
				case 3:
					doit = !IsBegWord;
			}
			if (!doit) return false;

			CtxMem = cmp->CtxMem;
			BegWord = cmp->BegWord;
			BegTarget = cmp->Mem;
			Pos = cmp->Pos;
			CtxPos = cmp->CtxPos;
			//      Page      = cmp->Page;
			//      Folio     = cmp->Folio;
			//      Verso     = cmp->Verso;
			DebPara = cmp->DebPara;
			DebSent = cmp->DebSent;
			DebWord = cmp->DebWord;
		}
		if (++Index == TextLength) {
			Found++;
			cTTarget *tt = new cTTarget;
			tt->Arrow = this;
			tt->CtxMem = CtxMem;
			tt->BegWord = BegWord;
			tt->BegTarget = BegTarget;
			tt->LenTarget = cmp->Mem - BegTarget + 1;
			tt->Pos = Pos;
			tt->Length = cmp->Pos - Pos + 1;
			tt->EndWord = NULL;
			tt->CtxMemLen = 0;
			tt->Index = index;
			tt->CtxPos = CtxPos;
			tt->IsBegWord = IsBegWord;
			cmp->TTLst->push_back(tt);
			Index = 0;
			tt->DebPara = DebPara;
			tt->DebSent = DebSent;
			tt->DebWord = DebWord;
			return true;
		}
	} else {
		if (Index) {
			Index = 0;
			Compare(c, cmp, index);
		}
	}
	return false;
}

void cArrow::Upper() {
	char *s = Text;

	while (*s) {
		*s = Uppers[(unsigned char) *s];
		s++;
	}
}

void cArrow::Substitute() {
	short j = 0;
	unsigned char *s = (unsigned char*)Arrow;
	unsigned char *w = (unsigned char*)Text;

	while (*s) {
		if ((*w = Modif(s++)) != 0) {
			w++;
			j++;
		}
	}
	*w = '\0';
	TextLength = j;
}
//---------------------------------------------------------------------------
int CompareArrowUp(void* Item1, void* Item2) {
	return ((cArrow*) Item1)->Compare((cArrow*) Item2);
}

int CompareArrowDown(void* Item1, void* Item2) {
	return ((cArrow*) Item2)->Compare((cArrow*) Item1);
}


void cArrowList::Destroy() {
	ArrowIterator it = begin(), iend = end();
	for (; it != iend; it++) delete Arrow(it);
	clear();
}

void cArrowList::Raz(void) {
	ArrowIterator it = begin(), iend = end();
	for (; it != iend; it++) {
		cArrow *a = Arrow(it);
		a->Index = a->Found = 0;
	}
}

void cArrowList::ClearGenoa() {
	ArrowIterator it = end(), ibegin = begin();
	for (; it != ibegin; it--) {
		cArrow *a = Arrow(it);
		if (a->GenFrom) {
			erase(it);
			delete a;
		}
	}
}

bool cArrowList::Find(char *s) {
	ArrowIterator it = begin(), iend = end();
	for (; it != iend; it++)
		if (!strcmp(s, Arrow(it)->Text)) return true;

	return false;
}

int cArrowList::LastOrder() {
	int order = 0;
	ArrowIterator it = begin(), iend = end();
	for (; it != iend; it++) {
		int n = Arrow(it)->Order;
		if (n > order) order = n;
	}
	return order;
}


//---------------------------------------------------------------------------

cTarget::cTarget() {
	memset((void *) this, 0, sizeof (cTarget));
	Starts = new cStartList;
}

cTarget::~cTarget() {
	if (Text) free(Text);
	if (File) free(File);
	if (Path) free(Path);
	delete Starts;
}



void cTarget::First() {
	Index = Starts->begin();
}

void cTarget::Next() {
	if(Index != Starts->end()) {
		Index++;
		if(Index != Starts->end()) Index--;
	}
}

void cTarget::Prev() {
	if (Index != Starts->begin()) Index--;
}

void cTarget::Last() {
	Index = Starts->end();
}

int cTarget::StrCmpModified(unsigned char *w) {
	register unsigned char *s = (unsigned char *)Text;
	register unsigned char c, t;
	register int cmp;


	while (*w || *s) {
		while (*w && (c = Modif((unsigned char *) w++)) == 0);
		while (*s && (t = Modif((unsigned char *) s++)) == 0);
		if (! *s && ! *w) break;
		cmp = c - t;
		if (cmp) break;
		w++;
		s++;
	}
	return cmp;
}

cExtract::cExtract() {
	memset((void *) this, 0, sizeof (cExtract));
}

void cExtractList::Destroy() {
	ExtractIterator it = begin(), iend = end();
	for (; it != iend; it++) delete Extract(it);
	clear();
}

//=======================================================

cGenoa::cGenoa() {
	memset((void *) this, 0, sizeof (cGenoa));
}

cGenoa::~cGenoa() {
	if (What) free(What);
	if (With) free(With);
	if (Expression) free(Expression);
}

bool cGenoa::Test(char *word, int len, int pos, int level) {
	if (Level <= level && !strncmp(word + pos, What, Len))
		return eval(Expression, ExpLen, word, len, pos);
	else
		return false;
}

void cGenoaList::Destroy() {
	GenoaIterator it = begin(), iend = end();
	for (; it != iend; it++) delete Genoa(it);
	clear();
}


//
// applique les r�gles de Genoa
// s : mot � traiter
// lst : liste des mots deja generes
// nlst : liste des nouveaux mots
//

//bool cGenoaList::Apply(std::string s, cStringList *lst, cStringList *nlst, int level, FILE *f) {
//	std::string w;
//	bool changed = false;
//	int idx;
//	char *p = s.c_str();
//	char *o = p;
//	int len = s.Length();
//
//	for (int l = 0; *p; l++, p++) {
//		for (int i = 0; i < Count; i++) {
//			cGenoa *g = (cGenoa *) Items[i];
//			if (g->Test(o, len, l, level)) {
//				changed = true;
//				w = s.SubString(1, l) + g->With + s.SubString(l + g->Len + 1, 128);
//				if (!lst->Find(w, idx) && !nlst->Find(w, idx)) {
//					nlst->Add(w);
//				}
//				if (f) {
//					fprintf(f, "%s\t%d\t%s->%s (%d)\t%s\r\n",
//						s.c_str(), l + 1, g->What, g->With, g->Level, w.c_str());
//					fflush(f);
//				}
//			}
//		}
//	}
//	return changed;
//}

//=======================================================




//---------------------------------------------------------------------------

int TargetListSortByText(void *item1, void *item2) {
	int cmp = strcmp(((cTarget *) item1)->Text, ((cTarget *) item2)->Text);
	if (!cmp && ((cTarget *) item1)->File)
		cmp = strcmp(((cTarget *) item1)->File, ((cTarget *) item2)->File);
	return cmp;
}

int TargetListSortByNum(void *item1, void *item2) {
	int cmp = ((cTarget *) item1)->Starts->size() - ((cTarget *) item2)->Starts->size();
	if (!cmp && ((cTarget *) item1)->File)
		cmp = strcmp(((cTarget *) item1)->File, ((cTarget *) item2)->File);
	return cmp;
}

int TargetListSortByDocText(void *item1, void *item2) {
	int cmp = 0;
	if (((cTarget *) item1)->File)
		cmp = strcmp(((cTarget *) item1)->File, ((cTarget *) item2)->File);
	if (!cmp)
		cmp = strcmp(((cTarget *) item1)->Text, ((cTarget *) item2)->Text);
	return cmp;
}

int TargetListSortByDocNum(void *item1, void *item2) {
	int cmp = 0;
	if (((cTarget *) item1)->File)
		cmp = strcmp(((cTarget *) item1)->File, ((cTarget *) item2)->File);
	if (!cmp)
		cmp = ((cTarget *) item1)->Starts->size() - ((cTarget *) item2)->Starts->size();
	return cmp;
}

int TargetListSortByTextDesc(void *item1, void *item2) {
	int cmp = strcmp(((cTarget *) item2)->Text, ((cTarget *) item1)->Text);
	if (!cmp && ((cTarget *) item1)->File)
		cmp = strcmp(((cTarget *) item1)->File, ((cTarget *) item2)->File);
	return cmp;
}

int TargetListSortByNumDesc(void *item1, void *item2) {
	int cmp = ((cTarget *) item2)->Starts->size() - ((cTarget *) item1)->Starts->size();
	if (!cmp && ((cTarget *) item1)->File)
		cmp = strcmp(((cTarget *) item1)->File, ((cTarget *) item2)->File);
	return cmp;
}

int TargetListSortByDocTextDesc(void *item1, void *item2) {
	int cmp = 0;
	if (((cTarget *) item1)->File)
		cmp = strcmp(((cTarget *) item1)->File, ((cTarget *) item2)->File);
	if (!cmp)
		cmp = strcmp(((cTarget *) item2)->Text, ((cTarget *) item1)->Text);
	return cmp;
}

int TargetListSortByDocNumDesc(void *item1, void *item2) {
	int cmp = 0;
	if (((cTarget *) item1)->File)
		cmp = strcmp(((cTarget *) item1)->File, ((cTarget *) item2)->File);
	if (!cmp)
		cmp = ((cTarget *) item2)->Starts->size() - ((cTarget *) item1)->Starts->size();
	return cmp;
}
// 0903

int strRcmp(char *t1, char *t2) {
	int l1 = strlen(t1);
	int l2 = strlen(t2);
	int cmp = 0;
	while (!cmp && l1 && l2) {
		cmp = t1[--l1] - t2[--l2];
	}
	if (!cmp) cmp = l1 - l2;
	return cmp;
}

int TargetListSortByTextR(void *item1, void *item2) {
	int cmp = strRcmp(((cTarget *) item1)->Text, ((cTarget *) item2)->Text);
	if (!cmp && ((cTarget *) item1)->File)
		cmp = strcmp(((cTarget *) item1)->File, ((cTarget *) item2)->File);
	return cmp;
}

int TargetListSortByTextRDesc(void *item1, void *item2) {
	int cmp = strRcmp(((cTarget *) item2)->Text, ((cTarget *) item1)->Text);
	if (!cmp && ((cTarget *) item1)->File)
		cmp = strcmp(((cTarget *) item1)->File, ((cTarget *) item2)->File);
	return cmp;
}

int TargetListSortByDocTextR(void *item1, void *item2) {
	int cmp = 0;
	if (((cTarget *) item1)->File)
		cmp = strcmp(((cTarget *) item1)->File, ((cTarget *) item2)->File);
	if (!cmp)
		cmp = strRcmp(((cTarget *) item1)->Text, ((cTarget *) item2)->Text);
	return cmp;
}

int TargetListSortByDocTextRDesc(void *item1, void *item2) {
	int cmp = 0;
	if (((cTarget *) item1)->File)
		cmp = strcmp(((cTarget *) item1)->File, ((cTarget *) item2)->File);
	if (!cmp)
		cmp = strRcmp(((cTarget *) item2)->Text, ((cTarget *) item1)->Text);
	return cmp;
}

void cTargetList::Destroy() {
	TargetIterator it = begin(), iend = end();
	for (; it != iend; it++) delete Target(it);
	clear();
}

void cTargetList::EndWord(int finWord, unsigned char sep) {
	for (auto it = begin(), iend = end(); it != iend;) {
		cStartList *sts = Target(it++)->Starts;
		for (auto j = sts->begin(), jend = sts->end(); j != jend;) {
			cStart *st = sts->Start(j++);
			switch (sep) {
				case 4:
					if (!st->FinPara) st->FinPara = finWord;
				case 3:
					if (!st->FinSent) st->FinSent = finWord;
				case 2:
					if (!st->FinWord) st->FinWord = finWord;
			}
		}
	}
}

cTarget* cTargetList::Find(char *text) {
	TargetIterator it = begin(), iend = end();
	for (; it != iend;) {
		cTarget *t = Target(it++);
		if (!strcmp(t->Text, text))
			return t;
	}
	return NULL;
}

//---------------------------------------------------------------------------

cStrPair::cStrPair(char *what, char *with, short whatLen, short withLen) {
	memset(this, 0, sizeof (cStrPair));
	AllocStr(What, what, whatLen);
	AllocStr(With, with, withLen);
	WhatLen = whatLen;
	WithLen = withLen;
}

cStrPair::~cStrPair() {
	if (What) free(What);
	if (With) free(With);
}

cFilter::cFilter(char *s, short len) {
	memset(this, 0, sizeof (cFilter));

	Len = len;
	if (*s == '^') {
		Place = 1;
		s++;
		Len--;
	}
	if (*(s + Len - 1) == '$') {
		Len--;
		Place = Place ? 4 : 2;
	}
	AllocStr(Text, s, Len);
}

cFilter::~cFilter() {
	if (Text) free(Text);
}

void cFilterList::Destroy() {
	FilterIterator it = begin(), iend = end();
	for (; it != iend; it++) delete Filter(it);
	clear();
}

void cFilterList::AddFilter(char *s) {
	char *p = strchr(s, '=');
	if (p && *(++p)) {
		for (;;) {
			s = p;
			p = strchr(s, ',');
			int len = p ? p - s : strlen(s);
			if (len) push_back(new cFilter(s, len));
			if (!p) break;
			p++;
		}
	}
}

cFilter* cFilterList::Find(char *s) {
	int l = strlen(s);
	FilterIterator it = begin(), iend = end();
	for (; it != iend; it++) {
		cFilter *f = Filter(it);
		if (!strncmp(s, f->Text, l)) return f;
	}
	return NULL;
}





unsigned char MarkPage;
unsigned char MarkRecto;
unsigned char MarkVerso;
unsigned char MarkGlyph;
unsigned char MarkNote;
unsigned char MarkLine;
unsigned char MarkRefer;
unsigned char Uppers[256];
unsigned char Substitutions[256];
unsigned char Separators[256];
unsigned char Vowels[256];
unsigned char Consonants[256];
cStrPair **StrPairs = NULL;
int StrPairsNum = 0;
char **Filters = NULL;
int FiltersNum = 0;
cGenoaList *GenoaList = NULL;
int GenoaMax;


//---------------------------------------------------------------------------
// initialisation des r�gles
//

void RulesInit() {
	memset(Uppers, 0, 256);
	memset(Substitutions, 0, 256);
	memset(Separators, 0, 256);
	memset(Vowels, 0, 256);
	memset(Consonants, 0, 256);
	for (int i = 32; i < 256; i++) {
		Substitutions[i] = Uppers[i] = toupper(i);
	}
	for (int i = 0; i < StrPairsNum; i++)
		delete *(StrPairs + i);
	StrPairsNum = 0;
	if (StrPairs) free(StrPairs);
	StrPairs = NULL;
	for (int i = 0; i < FiltersNum; i++)
		if (Filters[i]) free(Filters[i]);
	FiltersNum = 0;
	if (Filters) free(Filters);
	Filters = NULL;

	MarkPage = 177;
	MarkRecto = 242;
	MarkVerso = 190;
	MarkGlyph = 173;
	MarkNote = 185;
	MarkLine = 172;
	MarkRefer = 174;
}

#define RUL_SECTION 7

#define RUL_MARKERS 0
#define RUL_SEPAR   1
#define RUL_UPPERS  2
#define RUL_SUBST   3
#define RUL_VOWEL   4
#define RUL_CONSON  5
#define RUL_FILTER  6
#define RUL_BUFFER  1024

const char *RuleSection[] = {"[Markers]", "[Separators]", "[Uppers]", "[Substitutions]", "[Vowels]", "[Consonants]", "[Filters]"};
const char *MarkerText[] = {"Page", "Recto", "Verso", "Glyph", "Note", "Line"};


bool RulesRead(std::string file) {
	FILE *f;
	unsigned char rec[RUL_BUFFER];
	int mode = 0;
	short l, i;
	unsigned char *p, *s;
	unsigned char s0[RUL_BUFFER], s1[32];


	if ((f = fopen(file.c_str(), "r")) == NULL) return false;

	RulesInit();

	while (fgets((char *) rec, RUL_BUFFER, f), !feof(f)) {
		if (! *rec || *rec == '\r' || *rec == '\n' || *rec == ';') continue;

		for (p = rec; *p && *p != '\r' && *p != '\n'; p++);
		*p = '\0';

		if (*rec == '[') {
			for (mode = 0; mode < RUL_SECTION && strcmp((char *)rec, RuleSection[mode]); mode++);
			i = 0; // pour les separateurs
			continue;
		}

		p = rec;
		switch (mode) {
			case RUL_MARKERS:
				for (l = 0; *p && *p != '='; p++, l++);
				if (*p == '=') {
					p++;
					for (i = 0; i < MARKER_LEN; i++) {
						if (!strncmp((char *)rec, MarkerText[i], l)) {
							//            Markers[i] = GetChar(p);
							unsigned char c = GetChar(p);
							switch (i) {
								case MARKER_PAGE: MarkPage = c;
									break;
								case MARKER_RECTO: MarkRecto = c;
									break;
								case MARKER_VERSO: MarkVerso = c;
									break;
								case MARKER_GLYPH: MarkGlyph = c;
									break;
								case MARKER_NOTE: MarkLine = c;
									break;
							}
							break;
						}
					}
				}
				break;
			case RUL_SEPAR:
				i = GetChar(p);
				if (i && *p == '=') {
					p++;
					if (*p >= '1' && *p <= '4')
						Separators[i] = *p - '0';
				}
				break;
			case RUL_UPPERS:
				i = GetChar(p);
				if (i >= 32 && *p == '=') {
					p++;
					l = GetChar(p);
					Uppers[i] = l;
					//        Lowers[l] = i;
				}
				break;
			case RUL_SUBST:
				s = p;
				while(*s && *s != '=') s++;
				if (*s && s != p) {
					for (l = 0; p < s; l++) s0[l] = GetChar(p);
					p++;
					*s1 = 0;
					for (i = 0; *p && i < l; i++) s1[i] = GetChar(p);

					if (l == 1 && i <= 1)
						Substitutions[*s0] = *s1;
					else if (l) {
						cStrPair **pp = (cStrPair **) realloc(StrPairs, (StrPairsNum + 1) * sizeof (cStrPair *));
						if (pp) {
							StrPairs = pp;
							StrPairs[StrPairsNum++] = new cStrPair((char *) s0, (char *) s1, l, i);
						}
					}
				}
				break;
			case RUL_VOWEL:
				while (*p) Vowels[GetChar(p)] = 1;
				break;
			case RUL_CONSON:
				while (*p) Consonants[GetChar(p)] = 1;
				break;
			case RUL_FILTER:
				char **pp = (char **) realloc(Filters, (FiltersNum + 1) * sizeof (char *));
				if (pp) {
					Filters = pp;
					Filters[FiltersNum] = NULL;
					AllocStr(Filters[FiltersNum++], (char *) p);
				}
		}
	}
	fclose(f);

	return true;
}

unsigned char GetChar(unsigned char *(&s)) {
	unsigned char c = 0;

	if (!isdigit(*s))
		c = *s++;
	else if (*s == '0' && *(s + 1) == 'x') {
		s += 2;
		if (isdigit(*s))
			c = *s++ -'0';
		else if (*s >= 'a' && *s <= 'f')
			c = *s++ -'a' + 10;
		else return c;

		if (isdigit(*s))
			c = (c << 4) + (*s++ -'0');
		else if (*s >= 'a' && *s <= 'f')
			c = (c << 4) + (*s++ -'a' + 10);
	} else {
		int i = 0;
		while (i < 3 && isdigit(*s)) {
			c *= 10;
			c += (*s++ -'0');
		}
	}
	return c;
}

void AllocStr(char * &string, char *value) {
	int len;

	if (string) free(string);
	string = NULL;
	if (value && (len = strlen(value)) > 0 && (string = (char *) malloc(len + 1)) != NULL)
		strcpy(string, value);
}

void AllocStr(char * &string, char *value, int len) {
	if (string) free(string);
	string = NULL;
	if (len && (string = (char *) malloc(len + 1)) != NULL) {
		strncpy(string, value, len);
		*(string + len) = '\0';
	}
}

void Upper(char *dst, char *src, int len) {
	while (len--) {
		*dst++ = Uppers[(unsigned char) *src++];
	}
	*dst = '\0';
}

void Upper(char *s) {
	while (*s) {
		*s = Uppers[(unsigned char) *s];
		s++;
	}
}

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

std::string FileDocument(std::string file, std::string path) {
	if (file != "") {
		if (FilePath(file) == "")
			file = path + file;
	}
	return FileName(file) + " | " + FilePath(file);
}

std::string DocumentFile(std::string file) {
	std::size_t i = file.find(" | ");
	std::string x = file.substr(i + 3, file.length() - i - 3);
	if (x != "") x += "\\";
	x += file.substr(1, i - 1);
	return x;
}

std::string DocumentFileName(std::string file) {
	std::size_t i = file.find(" | ");
	return file.substr(1, i - 1);
}

char Substitute(char * &org, sSubstStruct *ss) {
	if (ss->len) {
		ss->len--;
		return *(ss->with++);
	}

	cStrPair *p;
	for (int i = 0; i < StrPairsNum; i++) {
		p = *(StrPairs + i);
		if (!UpperStrNCmp(org, p->What, p->WhatLen)) {
			org += p->WhatLen;
			ss->with = p->With;
			ss->len = p->WithLen - 1;
			return *(ss->with++);
		}
	}
	return Substitutions[Uppers[(unsigned char) *org++]];
}

int UpperStrCmp(char *what, char *with) {
	int cmp;
	do {
		cmp = Uppers[(unsigned char) *what] - *with;
	} while (!cmp && *what++ && *with++);
	return cmp;
}

int UpperStrNCmp(char *what, char *with, int len) {
	int cmp;
	do {
		cmp = Uppers[(unsigned char) *what] - *with;
	} while (!cmp && *what++ && *with++ && --len);
	return cmp;
}

int SeparStrNCmp(char *what, char *with, int len, bool symbol) {
	int cmp;
	do {
		if (symbol && Separators[(unsigned char) *what] == 1) continue;
		cmp = Uppers[(unsigned char) *what] - *with++;
		len--;
	} while (!cmp && *what++ && *with && len);
	return cmp;
}


char * FindFilter(char *s) {
	int l = strlen(s);
	for (int i = 0; i < FiltersNum; i++) {
		char *f = Filters[i];
		if (strlen(f) > l && *(f + l) == ':' && !strncmp(s, f, l))
			return f;
	}
	return NULL;
}



//===============================================

cStringList *TextOrg = NULL;
cStringList *TextTrad = NULL;
cStringList *TextCtrl = NULL;
std::string Language = "fra";

void TradInit() {
	if (TextOrg) delete TextOrg;
	TextOrg = NULL;
	if (TextTrad) delete TextTrad;
	TextTrad = NULL;
	if (TextCtrl) delete TextCtrl;
	TextCtrl = NULL;
	Language = "fra";
}

bool TradRead(std::string file) {
	char rec[RUL_BUFFER];
	char *o, *p, *t, *u;
	int i, j;
	bool header = true;
	bool ret = true;

	FILE *f = fopen(file.c_str(), "r");
	if (f) {
		while (fgets(rec, RUL_BUFFER, f), !feof(f)) {
			if (! *rec || *rec == '\r' || *rec == '\n' || *rec == ';') continue;

			if (header) {
				header = false;
				if (!strncmp(rec, "Temoa:", 6)) {
					o = rec + 6;
					char *p = strchr(o, ':');
					if (p) {
						TradInit();
						Language = std::string(o, p - o);
						TextOrg = new cStringList;
						TextTrad = new cStringList;
						TextCtrl = new cStringList;
//						TextOrg->Sorted = false;
//						TextTrad->Sorted = false;
//						TextCtrl->Sorted = false;
						continue;
					}
				}
				ret = false;
				break;
			}

			if (*rec == '"') {
				o = rec + 1;
				p = strchr(o, '"');
				if (!p || !(p - o)) continue;
				t = p;
				if (*(++t) != '=' || *(++t) != '"') continue;
				u = strchr(++t, '"');
				if (!u || !(u - t)) continue;
				TextOrg->push_back(std::string(o, p - o));
				TextTrad->push_back(std::string(t, u - t));
			} else {
				TextCtrl->push_back(rec);
			}
		}
		fclose(f);
	}
	return ret;
}





//---------------------------------------------------------------

//bool GetCaption(std::string s, std::string *caption, std::string *hint, std::string * text) {
//	int len = s.Length();
//	for (int i = 0; i < TextCtrl->Count; i++) {
//		std::string t = TextCtrl->Strings[i];
//		int cmp = strncmp(s.c_str(), t.c_str(), len);
//		//    if(cmp < 0)
//		//      break;
//		if (!cmp) {
//			len += 2; // skip "
//			t = t.SubString(len, t.Length() - len);
//			len = t.Pos("\":\"");
//			*caption = t.SubString(1, len - 1);
//			t = t.SubString(len + 3, t.Length() - len - 3);
//			len = t.Pos("\":\"");
//			*hint = t.SubString(1, len ? len - 1 : t.Length());
//			if (len)
//				*text = t.SubString(len + 3, t.Length() - len - 2);
//			else
//				*text = "";
//			return true;
//		}
//	}
//	return false;
//}

//void MenuLabels(std::string form, TMenuItem *wc) {
//	if (!TextCtrl) return;
//
//	std::string caption, hint, text;
//
//	if (GetCaption(form + ":" + wc->Name + ":", &caption, &hint, &text)) {
//		wc->Caption = caption;
//		wc->Hint = hint;
//	}
//
//	for (int i = 0; i < wc->Count; i++) {
//		TMenuItem *c = wc->Items[i];
//		MenuLabels(form, c);
//	}
//}

//void ControlLabels(std::string form, TWinControl *wc) {
//	if (!TextCtrl) return;
//
//	std::string caption, hint, text;
//	bool b = false;
//	bool r = false;
//	TClass ClassRef;
//
//	for (ClassRef = wc->ClassType(); ClassRef != NULL; ClassRef = ClassRef->ClassParent()) {
//		std::string s = String(ClassRef->ClassName());
//		if (s == "TRadioGroup") {
//			r = true;
//			break;
//		}
//		if (s == "TWinControl") {
//			b = true;
//			break;
//		}
//	}
//
//	if (GetCaption(form + ":" + wc->Name + ":", &caption, &hint, &text)) {
//		if (caption != "") ((cControl *) wc)->Caption = caption;
//		if (hint != "") wc->Hint = hint;
//		if (r) {
//			int p;
//			while ((p = text.Pos("\\r\\n")) != 0) {
//				text.Delete(p, 4);
//				text.Insert("\r\n", p);
//			}
//			((TRadioGroup *) wc)->Items->Text = text;
//		}
//	}
//
//	if (b) {
//		for (int i = 0; i < wc->ControlCount; i++)
//			ControlLabels(form, (TWinControl *) wc->Controls[i]);
//	}
//}

unsigned char Modif(unsigned char *s) {
	static short what = 0, with = 0, index;
	static cStrPair *pair;
	cStrPair *p;

	char c;

	if (!with) {
		if (what) {
			what--;
			return '\0';
		}
		for (int i = 0; i < StrPairsNum; i++) {
			p = *(StrPairs + i);
			if (!UpperStrNCmp((char *)s, p->What, p->WhatLen)) {
				pair = p;
				what = pair->WhatLen;
				with = pair->WithLen;
				index = 0;
				break;
			}
		}
		if (!with) return Substitutions[Uppers[(unsigned char) *s]];
	}
	with--;
	if (what) what--;
	return *(pair->With + index++);
}

int eval(char *expr, int len, char *word, int wlen, int pos) {
	register char c;
	register short oper = 1;
	int num = 0;
	int braket = 0, i = 0, curop = 0, curpr = 0;
	bool sign = false;

	int sknum[10], sklen[10], *stknum, *stklen;
	char skop[15], skpr[15], *stkop, *stkpr;

	stknum = sknum - 1;
	stklen = sklen - 1;
	stkop = skop;
	stkpr = skpr - 1;
	memset(sknum, 0, sizeof (sknum));
	memset(sklen, -1, sizeof (sklen));
	memset(skop, 0, sizeof (skop));



	EvalErr = 0;

	while (i < len) {
		c = expr[i];
		if (c == '(') {
			if (!oper) {
				EvalErr = OPDUWD;
				break;
			}
			int j = ++i;
			while (i < len) {
				if (expr[i] == '(') braket++;
				else if (expr[i] == ')') {
					if (!braket) break;
					braket--;
				}
				i++;
			}
			if (i == len) {
				EvalErr = BRAMIS;
				break;
			}
			num = eval(expr + j, i - j, word, len, pos);
			if (EvalErr) break;
			i++;
			*++stknum = sign ? -num : num;
			sign = false;
			*++stklen = -1;
			oper = 0;
		} else if (c == 'x' || c == 'X') {
			if (!oper) {
				EvalErr = OPDUWD;
				break;
			}
			i++;
			*++stknum = pos;
			sign = false;
			*++stklen = -1;
			oper = 0;
		} else if (c >= '0' && c <= '9') {
			if (!oper) {
				EvalErr = OPDUWD;
				break;
			}
			for (num = 0;;) {
				num += c - '0';
				if (++i == len) break;
				c = expr[i];
				if (!(c >= '0' && c <= '9')) break;
				num *= 10;
			}
			*++stknum = sign ? -num : num;
			sign = false;
			*++stklen = -1;
			oper = 0;
		} else if (c == '"') {
			if (!oper) {
				EvalErr = OPDUWD;
				break;
			}
			num = ++i;
			while (i < len && expr[i] != '"') i++;
			if (i == len) {
				EvalErr = COTMIS;
				break;
			}
			*++stknum = num;
			sign = false;
			*++stklen = i - num;
			i++;
			oper = 0;
		} else if (c == '\'') {
			if (!oper) {
				EvalErr = OPDUWD;
				break;
			}
			num = ++i;
			while (i < len && expr[i] != '\'') i++;
			if (i == len) {
				EvalErr = COTMIS;
				break;
			}
			*++stknum = num;
			sign = false;
			*++stklen = i - num;
			i++;
			oper = 0;
		} else {
			switch (c) {
				case '+':
				case '-':
					if (!oper) {
						curop = c;
						curpr = 6;
						oper = 1;
					} else if (oper == 1) {
						sign = c == '-';
						oper = 2;
					} else EvalErr = OPRUWD;
					break;
				case '=':
				case '#':
					if (!oper) {
						curop = c;
						curpr = 5;
						oper = 1;
					} else EvalErr = OPRUWD;
					break;
				case '&':
				case '|':
					if (!oper) {
						curop = c;
						curpr = 4;
						oper = 1;
					} else EvalErr = OPRUWD;
					break;
				default:
					EvalErr = oper ? OPDINV : OPRINV;
			}
			if (EvalErr) break;
			i++;
		}
		if (i == len && oper) {
			EvalErr = OPDMIS;
			break;
		}

		while ((i >= len && *stkop) || (*stkop && curpr && curpr <= *stkpr)) {
			while (*stkop && curpr <= *stkpr) {
				int b = *stknum--;
				int bl = *stklen--;
				int a = *stknum--;
				int al = *stklen--;
				char *s;

				stkpr--;
				switch (c = *stkop--) {
					case '+':
						a += b;
						break;
					case '-':
						a -= b;
						break;
					case '&':
						a &= b;
						break;
					case '|':
						a |= b;
						break;
					default:
						if (bl == -1) {
							s = expr + a;
						} else {
							s = expr + b;
							b = a;
							al = bl;
						}
						if (b < 0 || b >= wlen)
							a = strncmp(s, " ", al);
						else
							a = strncmp(word + b, s, al);

						switch (c) {
							case '=':
								a = a == 0;
								break;
							case '#':
								a = a != 0;
								break;
						}
				}
				*++stknum = a;
				*++stklen = -1;
			}
		}

		if (curpr > 2) {
			*++stkop = curop;
			*++stkpr = curpr;
			curpr = 0;
		}
	} // while(i < len)
	return EvalErr ? 0 : *stknum;
}



// recuperation des champs ascii
// input :
//   fld = pointeur sur le debut du champ
//   sep = caractere de separation
//   next = renvoie un pointeur sur le prochain champ ou NULL
// output :
//   taille du champ

int TextFieldLen(char *fld, char sep, char *&next) {
	next = fld;

	if (*next == '"') while (*++next != '"');
	while (*next && (*next != sep || *next == '\r' || *next == '\n')) next++;

	int len = next - fld;
	if (*next != sep) next = NULL;
	else next++;

	return len;
}


// Raz la liste des regle Genoa

void GenoaInit() {
	if (GenoaList) 
		delete GenoaList;
	GenoaList = NULL;
	GenoaMax = 0;
}


// lecture des regles Genoa

bool GenoaRead(std::string file) {
	FILE *f;
	char rec[256];
	char *s, *n;

	if ((f = fopen(file.c_str(), "r")) == NULL) return false;

	GenoaInit();
	GenoaList = new cGenoaList;

	while (fgets((char *) rec, 256, f), !feof(f)) {
		if (!strncmp(rec, "[Genor]", 7)) break;
	}

	while (fgets((char *) rec, 256, f), !feof(f)) {
		if (*rec == '[') break;
		if (isdigit(*rec)) {
			int l = atoi(rec);
			if (l > GenoaMax) GenoaMax = l;
			Upper(rec);
			l = strlen(rec);
			if (l && rec[--l] == '\n') rec[l] = '\0';
			cGenoa *g = new cGenoa;
			g->Level = atoi(rec);
			TextFieldLen(rec, ',', s);
			AllocStr(g->What, s, TextFieldLen(s, ',', n));
			g->Len = strlen(g->What);
			s = n;
			AllocStr(g->With, s, TextFieldLen(s, ',', n));
			AllocStr(g->Expression, n);
			g->ExpLen = strlen(g->Expression);
			GenoaList->push_back(g);
		}
	}
	fclose(f);
	return true;
}


// generation des orthographes
// input: s = chaine d'origine
//        level = niveau de generation
// output: lst = liste des chaines generees

void Genor(cStringList *lst, std::string s, int level, FILE *f) {
	cStringList *nlst = new cStringList;

//	lst->clear();
//	lst->Sorted = true;
//	lst->Objects[lst->Add(s)] = NULL; // mot a traiter
//
//	bool b = true;
//	while (b) {
//		b = false;
//		for (int i = lst->Count; i--;) {
//			if (lst->Objects[i] == NULL) {
//				lst->Objects[i] = (TObject *) 1; // mot deja traite
//				b |= (GenoaList->Apply(lst->Strings[i], lst, nlst, level, f));
//			}
//		}
//		if (b) {
//			for (int j = nlst->Count; j--;) {
//				s = nlst->Strings[j];
//				lst->Objects[lst->Add(nlst->Strings[j])] = NULL; // mot a traiter
//				nlst->Delete(j);
//			}
//			nlst->Clear();
//		}
//	}

	delete nlst;
}




