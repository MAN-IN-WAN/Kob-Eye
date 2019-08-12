
#include <cstdlib>
#include "Temoa.h"

cTemoa::cTemoa() {
	LstMode = false;
	OrtOrtho = 2;
	OrtUpper = true;
	OrtModif = true;
	OrtGenoa = false;
	OrtSepar = true;
	OrtSymbol = true;
	OrtPlace = 0;
	OrtContx = 0;
	OrtAll = false;
	OrtOrder = false;
	OrtExclu = false;
	OrtAsOrt = true;
	OrtResult = 2;
	OrtResSym = false;
	OrtCorpus = false;
	OrtLevel = 1;
	OrtSvGenoa = false;
}

void cTemoa::AddArrow(const char *word) {
	ArrowList.push_back(new cArrow(word));
}

void cTemoa::ClearArrows() {
	ArrowList.Destroy();
}

void cTemoa::ClearTargets() {
	TargetList.Destroy();
}

int cTemoa::TargetCount() {
	return (long)TargetList.size();
}

const char* cTemoa::GetTargetText(int n) {
	TargetIterator it = std::next(TargetList.begin(), n);
	return TargetList.Target(it)->Text;
}


const char* cTemoa::GetTargetsJson() {
	char *s = (char *)malloc(2);
	strcpy(s, "[");
	size_t len = 3;
	bool sep = false;
	char b[4096];
	for (auto it = TargetList.begin(), iend = TargetList.end(); it != iend;) {
		cTarget *t = *it++;
		int count = t->Starts->size();
		snprintf(b, 4096, "{\"text\":\"%s\",\"count\":\"%d\",\"doc\":\"%s\"}",t->Text,count,t->File);
		len += strlen(b)+1;
		s = (char *)realloc(s, len);
		if(sep) strcat(s, ",");
		sep = true;
		strcat(s, b);
	}
	strcat(s, "]");
	return s;
}
// open and read the file

bool cTemoa::OpenFile(std::string file) {
	FILE *f = fopen(file.c_str(), "r");
	if (!f) return false;
	cRtf *rtf = new cRtf(f, &DocStream, &MarkList, &Lines, &NoteList);
	rtf->Read();
	fclose(f);
	delete rtf;
	return true;
}

// explode corpus string in the List;
void cTemoa::SetCorpus(const char *corpus) {
	int pos, curr = 0, prev = 0;
	std::string s = corpus;
	Corpus.clear();
	for (;;) {
		pos = s.find(";", curr);
		if (pos < 0) break;

		curr = pos;
		Corpus.push_back(s.substr(prev, curr - prev));
		prev = ++curr;
	}
}

bool cTemoa::SetRules(const char *rules) {
	return(RulesRead(rules) && GenoaRead(rules));
}

// search on the corpus

bool cTemoa::Search() {
	// reset Text for different ortho
	for (auto it = ArrowList.begin(), iend = ArrowList.end(); it != iend; it++) {
		cArrow *arw = (cArrow*) * it;
		AllocStr(arw->Text, arw->Arrow);
		arw->TextLength = arw->ArrowLength;
	}
	if (OrtOrtho) {
		for (auto it = ArrowList.begin(), iend = ArrowList.end(); it != iend; it++) {
			cArrow *arw = ArrowList.Arrow(it);
			if (OrtModif)
				arw->Substitute();
			else
				arw->Upper();
		}
	}
	if (OrtGenoa) {
//		GenoaArrow(OrtLevel, OrtSvGenoa);
//		frmGenoa->ArrowList = ArrowList;
//		if (genor) {
//			if (frmGenoa->ShowModal() != mrOk) {
//				ArrowList->ClearGenoa();
//				return;
//			}
//		}
	}

	TargetList.Destroy();
	bool ret = true;
	for (auto it = Corpus.begin(), iend = Corpus.end(); it != iend; it++) {
		if (OpenFile(*it)) FindTarget(&ArrowList, &FilterList, (char *) (*it).c_str());
		else ret = false;
	}
	return ret;
}

void cTemoa::FindTarget(cArrowList *lst, cFilterList *flt, char *file) {
	register unsigned char c, *p, sep;
	register int pos = 0;
	cTTarget *tt;
	cArrow *a;
	unsigned char *endWord;
	int begContext;
	int lenWord;
	int posMem = 0, posPara, posSent, posWord, memPara, memSent, memWord;
	int finWord = 0;
	cCompare *cmp = new cCompare;
	bool found = false;
	cTargetList *tlist = new cTargetList;
	bool filter = flt->size() == 0;
	bool okCtx;


	LstMode = false;

	cmp->CtxPos = -1;
	begContext = 4;
	lst->Raz();

	std::string s = DocStream.str();
	p = (unsigned char *) s.c_str();
	while ((c = *p) != 0) {

		if ((sep = Separators[c]) > 1) {
			if (sep > 2)
				TargetList.EndWord(finWord, sep);
			if (found)
				cmp->TTLst->EndWord(endWord, finWord, sep);

			if (sep > begContext) begContext = sep;

			okCtx = !OrtContx;
			if (OrtContx && sep > OrtContx) {
				okCtx = false;
				if (found) {
					cmp->TTLst->EndCtx(pos, posMem);
					if (CheckTarget(cmp->TTLst, lst))
						okCtx = true;
					else {
						cmp->TTLst->Destroy();
						found = false;
					}
				}
				lst->Raz();
			}

			if (okCtx && (!OrtSepar || sep > 2 || OrtContx == 1)) {
				if (found) {
					cTTargetList *ttlst = cmp->TTLst;
					for (auto it = ttlst->begin(), iend = ttlst->end(); it != iend; it++) {
						tt = ttlst->TTarget(it);
						int len = tt->EndWord - tt->BegWord + 1;
						if (filter || CheckFilter(tt->BegWord, len, flt, OrtSymbol)) {
							AddWord(tt, len, file);
						}
					}
					ttlst->Destroy();
					found = false;
				}
				lst->Raz();
			}
		} else { // sep <= 1
			if (sep && OrtSymbol) c = 0; // ignore les symboles
			else {
				if (OrtUpper) c = Uppers[c];
				if (OrtModif) c = Modif(p);

				if (begContext) {
					switch (begContext) {
						case 4: // fin de paragraphe
							cmp->DebPara = memPara = posMem;
							posPara = pos;
						case 3: // fin de phrase
							cmp->DebSent = memSent = posMem;
							posSent = pos;
						case 2: // fin de mot
							cmp->BegWord = p;
							cmp->DebWord = memWord = posMem;
							posWord = pos;
							begContext = 0;
					}
					switch (OrtContx) {
						case 1:
							cmp->CtxMem = memWord;
							cmp->CtxPos = posWord;
							break;
						case 2:
							cmp->CtxMem = memSent;
							cmp->CtxPos = posSent;
							break;
						case 3:
							cmp->CtxMem = memPara;
							cmp->CtxPos = posPara;
					}
					begContext = 0;
				}
				endWord = p;
				finWord = posMem;

				if (c) {
					cmp->Mem = p;
					cmp->Pos = pos;
					int i = 0;
					for (auto it = lst->begin(), iend = lst->end(); it != iend; i++, it++) {
						a = lst->Arrow(it);
						if (a->Compare(c, cmp, i))
							found = true;
					}
				} // if(c)
			} // if(c)
		} // sep <= 1

		pos++;
		if (*p == '\n' || *p == '\r') pos++;
		p++;
		posMem++;

	} // while(*p)

	delete cmp;
	delete tlist;

}

bool cTemoa::CheckTarget(cTTargetList *tlist, cArrowList *alist) {
	if (tlist->size() == 0) return false;

	cArrow *a;
	bool ok = true;
	int order = 0;

	//isbegword: Found devient un compteur
	for (auto it = alist->begin(), iend = alist->end(); it != iend; it++) {
		a = alist->Arrow(it);
		if (a->Found && a->GenFrom) {
			a->GenFrom->Found = 1;
			a->Found = 0;
		}
	}


	// controle All et Exclude
	// raz le flag found pour le prochain appel de CheckTarget
	for (auto it = alist->begin(), iend = alist->end(); it != iend; it++) {
		a = alist->Arrow(it);
		if (a->GenFrom) continue;
		if (a->Found > 0) ok &= !a->Exclude;
		if (OrtAll) ok &= a->Found > 0 || a->Exclude;
		a->Found = 0; // false;
		a->OrderCheck = true; // init du flag pour le controle de l'ordre
	}

	// controle l'ordre
	if (OrtOrder) {
		for (auto it = tlist->begin(), iend = tlist->end(); it != iend; it++) {
			a = tlist->TTarget(it)->Arrow;
			if (a->GenFrom) continue;
			if (a->OrderCheck) {
				int o = a->Order;
				ok = o >= order;
				order = o;
				a->OrderCheck = false;
			}
		}
	}

	return ok;
}

int cTemoa::CheckFilter(unsigned char *p, short len, cFilterList *flt, bool symbol) {
	unsigned char *w;
	int b = 1;

	for (auto it = flt->begin(), iend = flt->end(); it != iend;) {
		cFilter *f = flt->Filter(it++);
		if (len >= f->Len) {
			switch (f->Place) {
				case 0:
					b = 0; // ???????????? pas bon !
					break;
				case 1:
					b = SeparStrNCmp((char *) p, f->Text, f->Len, symbol);
					break;
				case 2:
					w = p + len;
					for (short j = f->Len; j;) {
						if (!Separators[*(--w)]) j--;
					}
					b = SeparStrNCmp((char *) w, f->Text, f->Len, symbol);
					break;
			}
		}
	}
	return !b;
}

void cTemoa::AddWord(cTTarget *tt, short wlen, char *file) {
	cTarget *t;
	int i, j, n;
	int min, max, old, half, cmp;
	char word[128], *s;
	register unsigned char c;
	register bool sym = OrtResSym;

	char *w = (char *) tt->BegWord;

	i = wlen;
	s = word;

	if (OrtResult == 1) {
		while (i--) {
			c = *w;
			if (sym || Separators[c] != 1)
				*s++ = Uppers[c];
			w++;
		}
	} else if (OrtResult == 2) {
		while (i) {
			c = *w;
			if (sym || Separators[c] != 1) {
				if ((c = Modif((unsigned char *) w++)) != 0)
					*s++ = c;
				i--;
			} else {
				w++;
				i--;
			}
		}
	} else {
		while (i--) {
			if (sym || !Separators[(unsigned char) *w])
				*s++ = *w;
			w++;
		}
	}
	*s = '\0';

	max = TargetList.size();
	min = 0;
	half = -1;
	cmp = 1;
	if (max > 0) {
		for (;;) {
			old = half;
			if ((half = min + (max - min) / 2) == old) {
				break;
			}

			TargetIterator ti = std::next(TargetList.begin(), half);
			cTarget *t = TargetList.Target(ti);
			cmp = 0;
			if (t->File)
				cmp = strcmp(file, t->File);
			if (!cmp)
				cmp = strcmp(word, TargetList.Target(ti)->Text);

			if (!cmp) break;
			if (cmp < 0)
				max = half;
			else
				min = half;

			if (min == max) {
				break;
			}
		}
	}

	int page, folio, verso, vign;
	cPict *pict;
	int refer, refend;
	FindPage(tt->Pos, &page, &folio, &verso, &vign, &pict, &refer, &refend, false);

	if (cmp) {
		t = new cTarget;
		AllocStr(t->Text, word, wlen);
		AllocStr(t->File, file);
		t->Length = wlen;
		t->ArrowIndex = tt->Index;
		if (cmp > 0)
			half++;
		TargetIterator ti = std::next(TargetList.begin(), half);
		TargetList.insert(ti, t);

		cStart *st = new cStart;
		st->Pos = tt->Pos;
		st->Len = tt->Length;
		st->CtxPos = tt->CtxPos;
		st->CtxLen = tt->CtxLen;
		st->CtxMem = tt->CtxMem;
		st->CtxMemLen = tt->CtxMemLen;
		st->DebPara = tt->DebPara;
		st->FinPara = tt->FinPara;
		st->DebSent = tt->DebSent;
		st->FinSent = tt->FinSent;
		st->DebWord = tt->DebWord;
		st->FinWord = tt->FinWord;
		st->Page = page;
		st->Folio = folio;
		st->Verso = verso;
		t->Add(st);
		//    t->Add(start, len, ctxPos, ctxLen, ctxMem, ctxMemLen, page, folio, verso);
	} else {
		TargetIterator ti = std::next(TargetList.begin(), half);
		t = TargetList.Target(ti);
		cStartList *p = t->Starts;
		for (auto it = p->begin(), iend = p->end(); it != iend;) {
			if (p->Start(it++)->Pos == tt->Pos) break;
		}
		if (i == n) {
			cStart *st = new cStart;
			st->Pos = tt->Pos;
			st->Len = tt->Length;
			st->CtxPos = tt->CtxPos;
			st->CtxLen = tt->CtxLen;
			st->CtxMem = tt->CtxMem;
			st->CtxMemLen = tt->CtxMemLen;
			st->DebPara = tt->DebPara;
			st->FinPara = tt->FinPara;
			st->DebSent = tt->DebSent;
			st->FinSent = tt->FinSent;
			st->DebWord = tt->DebWord;
			st->FinWord = tt->FinWord;
			st->Page = page;
			st->Folio = folio;
			st->Verso = verso;
			t->Add(st);
			//      t->Add(start, len, ctxPos, ctxLen, ctxMem, ctxMemLen, page, folio, verso);
		}
	}

}

void cTemoa::FindPage(int p, int *page, int *folio, int *verso, int *vign, cPict **pict, int *refer, int *refend, bool refpos) {
	cMark *mark, *last;
	int pos;
	*page = *folio = *verso = *vign = 0;
	*pict = NULL;
	*refer = *refend = 0;

	for (auto it = MarkList.begin(), iend = MarkList.end(); it != iend;) {
		mark = (cMark *) MarkList.Mark(it++);
		pos = refpos ? mark->RefPos : mark->Pos;
		if (pos < p) {
			switch (mark->Type) {
				case MARK_PAGE:
					*page = mark->Num;
					break;
				case MARK_FOLIO:
					*folio = mark->Num;
					*verso = mark->Verso;
					break;
				case MARK_VIGN:
					*vign = mark->Num;
					*pict = mark->Pict;
					break;
				case MARK_REFER:
					*refer = refpos ? mark->RefPos : mark->Pos;
					*refend = mark->Num;
					break;
			}
		} else break;
	}
}

