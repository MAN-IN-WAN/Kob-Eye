//---------------------------------------------------------------------------

#include <stdio.h>

#include "RTF.h"

cMark::cMark(int pos, int refpos, int type, int num, int verso) {
	Pos = pos;
	Type = type;
	Num = num;
	Verso = verso;
	Pict = NULL;
	RefPos = refpos;
}

cMark::~cMark() {
	if (Pict) delete Pict;
}

void cMarkList::Destroy() {
	auto it = begin(), iend = end();
	for (; it != iend; it++) delete *it;
	clear();
}

cRtCtx::cRtCtx() {
	comment = false;
	note = false;
	pict = false;
}

cRtCtx::cRtCtx(cRtCtx *ctx) {
	cp(ctx);
}

void cRtCtx::cp(cRtCtx *ctx) {
	comment = ctx->comment;
	note = ctx->note;
	pict = ctx->pict;
}

void cRtCtxList::Destroy() {
	auto it = begin(), iend = end();
	for (; it != iend; it++) delete *it;
	clear();
}


typedef struct {
	const char *cmd;
	const short action;
} CmdPair;

CmdPair CmdPairs[] = {
	{"rtf", 4},
	{"par", 3},
	{"sect", 3},
	{"footnote", 1},
	{"header", 2},
	{"footerf", 2},
	{"footer", 2},
	{"info", 2},
	{"fonttbl", 2},
	{"stylesheet", 2},
	{"colortbl", 2},
	{"line", 5},
	{"row", 3},
	{"cell", 6},
	{"tab", 7},
	{"endash", 8},
	{"pict", 9},
	{"wmetafile", 10},
	{"picw", 11},
	{"pich", 12},
	{"emfblip", 13},
	{"pngblip", 13},
	{"jpegblip", 13},
	{"shppict", 13},
	{"nonshppict", 13},
	{"macpict", 13},
	{"pmmetafile", 13},
	{"dibitmap", 13},
	{"wbitmap", 13},
	{"bullet", 14},
	{"emdash", 15},
	{"enspace", 16},
	{"emspace", 17},
	{"lquote", 18},
	{"rquote", 19},
	{"ldblquote", 20},
	{"rdblquote", 21},
	{NULL, 0}
};

cRtf::cRtf(FILE *f, std::stringstream *strm, cMarkList *mrk, cLines *lin, cNoteList *note) {
	memset(this, 0, sizeof (cRtf));
	File = f;
	Mark = mrk;
	Lines = lin;
	Notes = note;
	Strm = strm;
	RefMark = NULL; // 20090508
}

void cRtf::Add(char c) {
	if (intext == RTF_TEXT_BLK) {
		Strm->write(text, RTF_TEXT_BLK);
		intext = 0;
	}
	text[intext++] = c;
}

void cRtf::NewLine(bool hard) {
	RefMarkEnd();
	RefPos++;

	if (bNote) {
		Note->Add('\n');
	} else {
		Lines->Add(Pos, 0);
		if (hard) {
			Add('\n');
		} else {
			Add('\r');
		}
		Pos += 2;
	}
}

void cRtf::Command() {
	CmdPair *p;

	for (int i = 0;; i++) {
		p = &CmdPairs[i];
		if (!p->action) break;
		if (!strcmp(cmd, p->cmd)) break;
	}
	switch (p->action) {
		case 1:
			RefMarkEnd(); // 20090508
			RefPos++;

			Note = new cData(RTF_TEXT_BLK);
			Ctx->note = bNote = true;

			Add(MarkNote);
			Pos++;
			break;
		case 2:
			Ctx->comment = bComment = true;
			break;
		case 3:
			NewLine(true);
			break;
		case 4:
			Rtf = true;
			break;
		case 5:
			NewLine(false);
			break;
		case 6:
		case 7:
			/* 200901
			   if(bNote)
				  Note->Add('\t');
				else {
				  Add('\t');
				  Pos++;
				}
			 */
			SingleChar('\t');
			break;
		case 8:
			/* 200901
			   if(bNote)
				  Note->Add('-');
				else {
				  Add('-');
				  Pos++;
				}
			 */
			SingleChar(0x96);
			break;
		case 9:
			RefMarkEnd(); // 20090508
			RefPos++;

			Ctx->pict = bPict = true;
			Pict = new cData(RTF_PICT_BLK);
			break;
		case 10:
			PicType = atoi(num);
			break;
		case 11:
			PicW = atoi(num);
			break;
		case 12:
			PicH = atoi(num);
			break;
		case 13:
			PicType = -1; // ignore picture
			break;
			// 200901
		case 14:
			SingleChar(0x95);
			break;
		case 15:
			SingleChar(0x97);
			break;
		case 16:
			SingleChar(' ');
			break;
		case 17:
			SingleChar(' ');
			break;
		case 18:
			SingleChar(0x91);
			break;
		case 19:
			SingleChar(0x92);
			break;
		case 20:
			SingleChar(0x93);
			break;
		case 21:
			SingleChar(0x04);
			break;
	}
}

void cRtf::SingleChar(char c) {
	if (bNote)
		Note->Add(c);
	else {
		Add(c);
		Pos++;
		RefPos++; //20090508
	}
}

void cRtf::RefMarkEnd() {
	if (RefMark) {
		RefMark->Num = RefPos - RefMark->RefPos;
		RefMark = NULL;
	}
}

int cRtf::Read() {
	unsigned char c;
	short mode = RTF_TXT;
	bool recto = true;
	unsigned char buff[4096];
	size_t fmax, fpos;

	Ctx = new cRtCtx;
	cRtCtxList *CtxList = new cRtCtxList;

	Page = Folio = Line = 0;
	Pos = Vignette = 0;
	RefPos = 0;
	Rtf = false;


	text = (char *) malloc(RTF_TEXT_BLK);
	intext = 0;

	cmd[0] = '\0';
	num[0] = '\0';

	// read from file
	while (fmax = fread(buff, 1, 4096, File), fmax > 0) {
		fpos = 0;
		while (fpos < fmax) {
			c = buff[fpos++];
			if (c == '\r') continue;

			if (mode == RTF_HEX) {
				cmd[incmd++] = c;
				if (incmd == 2) {
					int i;
					cmd[incmd] = '\0';
					sscanf(cmd, "%x", &i);
					c = i;
					mode = RTF_TXT;
				} else continue;
			}

			if (mode == RTF_ASC) {
				if (c == '>') {
					cmd[incmd] = '\0';
					c = atoi(cmd);
					mode = RTF_TXT;
				} else {
					cmd[incmd++] = c;
					continue;
				}
			}

			if (mode == RTF_CMD) {
				if ((c >= 'a' && c <= 'z') || (c >= 'A' && c <= 'Z'))
					cmd[incmd++] = c;
				else {
					cmd[incmd] = '\0';
					mode = RTF_NUM;
					innum = 0;
				}
			}

			if (mode == RTF_NUM) {
				if ((c >= '0' && c <= '9') || (c == '-' && innum == 0))
					num[innum++] = c;
				else {
					num[innum] = '\0';
					Command();
					mode = c == ' ' ? RTF_SEP : RTF_TXT;
				}
			}


			if (mode != RTF_ESC) {
				if (c == '{') {
					CtxList->push_back(new cRtCtx(Ctx));
					First = true;
					continue;
				} else if (c == '}') {
					RtCtxIterator it = CtxList->end();
					std::advance(it, -1);
					cRtCtx *ctx = *it; // (cRtCtx *)
					Ctx->cp(ctx);
					CtxList->erase(it);
					delete ctx;
					bComment = Ctx->comment;
					if (bNote && !Ctx->note) {
						Note->Add('\0');
						Notes->push_back(new cNote(Page, (char *) Note->Data));
						delete Note;
						bNote = false;
					}
					if (bPict && !Ctx->pict) {
						//            Picts->Add(new cPict(PicType, PicW, PicH, Pict->Len, (char *) Pict->Data));
						//            bPict = false;
						//            delete Pict;
						MarkIterator mit = Mark->end(); mit--;
						if (!Mark->size() || ((cMark *)(*mit))->Pos != Pos - 1) {
							Mark->push_back(new cMark(Pos, RefPos, MARK_VIGN, ++Vignette, -1));
							Pos++;
							RefPos++;
							Add(MarkGlyph);
						}
						if (PicType != -1)
							((cMark *)(*mit))->Pict = new cPict(PicType, PicW, PicH, Pict->Len, (char *) Pict->Data);
						delete Pict;
						bPict = false;
					}
					First = false;
					continue;
				}
			}

			if (bComment) continue;


			if (mode != RTF_ESC && c == '\\') {
				mode = RTF_ESC;
				incmd = 0;
				continue;
			}

			if (mode == RTF_ESC) {
				if (c >= 'a' && c <= 'z') {
					cmd[incmd++] = c;
					mode = RTF_CMD;
					continue;
				} else {
					mode = RTF_TXT;
					switch (c) {
						case '*':
							Ctx->comment = bComment = true;
							continue;
						case '\'':
							mode = RTF_HEX;
							continue;
						case '<':
							mode = RTF_ASC;
							continue;
						case '~':
							c = ' ';
							break;
						case '_':
							c = '-';
							break;
					}
				}
			}

			if (mode == RTF_SEP) {
				mode = RTF_TXT;
				continue;
			}

			if (c == '\n' && Rtf) continue;

			if (mode == RTF_TXT) {
				First = false;

				if (bPict) {
					Pict->Add(c);
				} else if (bNote) {
					Note->Add(c);
				} else {
					cMark *mark;

					if (c == '\n') {
						if (!Rtf) {
							NewLine(true);
							continue;
						}
					} else if (c == MarkGlyph) { // Markers[MARKER_GLYPH])  //173:   // - vignette
						RefMarkEnd();
						Mark->push_back(new cMark(Pos, RefPos, MARK_VIGN, ++Vignette, -1));
					} else if (c == MarkPage) { // Markers[MARKER_PAGE])  //177:   // +/-  page
						RefMarkEnd();
						Mark->push_back(new cMark(Pos, RefPos, MARK_PAGE, ++Page, 0));
					} else if (c == MarkVerso) { // Markers[MARKER_VERSO]) {  //190:   // 3/4 verso
						RefMarkEnd();
						if (!recto) Folio++;
						recto = false;
						Mark->push_back(new cMark(Pos, RefPos, MARK_FOLIO, Folio, 1));
					}						// 242 => �   20130621
						//else if(c == '=' || c == MarkRecto) {    // Markers[MARKER_RECTO]) {  //242:  // = recto
					else if (c == '=') { // Markers[MARKER_RECTO]) {  //242:  // = recto
						RefMarkEnd();
						recto = true;
						Mark->push_back(new cMark(Pos, RefPos, MARK_FOLIO, ++Folio, 0));
					}						// 20090508
					else if (c == MarkRefer) {
						if (RefMark) {
							RefMarkEnd();
						} else {
							RefMark = new cMark(Pos, RefPos, MARK_REFER, 0, 0);
							Mark->push_back(RefMark);
						}
					}
					if (!Page) Mark->push_back(new cMark(Pos, RefPos, MARK_PAGE, ++Page, 0));

					Pos++;
					RefPos++; 
					Add(c);
				}
			}
		}
	}
	//  if(Text->Len) NewLine(true);
	//  c = '\0';
	//  Strm->Write(&c, 1);
	//  delete Text;

	// 20090509
	RefMarkEnd();

	if (intext) NewLine(true);
	Add('\0');
	Strm->write(text, intext);
	/*
	  if(intext) NewLine(true);

	  text[0] = '\0';
	  Strm->Write(text, 1);
	 */

	//  free(text);

	delete Ctx;
	delete CtxList;
	free(text);

	return 0;
}




//-----------------------------------------------------------------
//-----------------------------------------------------------------

cNote::cNote(int page, char *text) {
	Page = page;
	Text = NULL;
	AllocStr(Text, text);
}

cNote::~cNote() {
	if (Text) free(Text);
}

void cNoteList::Destroy(void) {
	auto it = begin(), iend = end();
	for (; it != iend; it++) delete *it;
	clear();
}

cPict::cPict(int type, int picw, int pich, int len, char *data) {
	memset(this, 0, sizeof (cPict));

//	BYTE *buff = data;
//	if (buff) {
//		int i = 0;
//		while (len) {
//			unsigned char h = (((*data > '9') ? *data - 'a' + 10 : *data - '0') << 4) & 0xf0;
//			data++;
//			h |= ((*data > '9') ? *data - 'a' + 10 : *data - '0') & 0x0f;
//			data++;
//			buff[i++] = h;
//			len -= 2;
//		}
//
//		METAFILEPICT mp;
//		mp.mm = type;
//		mp.xExt = picw;
//		mp.yExt = pich;
//		mp.hMF = NULL;
//
//		hemf = SetWinMetaFileBits(i, buff, NULL, &mp);
//	}
}

cPict::~cPict() {
//	if (hemf) DeleteEnhMetaFile(hemf);
}

//void cPict::Size(RECT *rect) {
//	ENHMETAHEADER emh;
//	GetEnhMetaFileHeader(hemf, sizeof (emh), &emh);
//	rect->left = emh.rclBounds.left;
//	rect->top = emh.rclBounds.top;
//	rect->right = emh.rclBounds.right;
//	rect->bottom = emh.rclBounds.bottom;
//}
//
//void cPict::Draw(HDC hdc, RECT *rect) {
//	PlayEnhMetaFile(hdc, hemf, rect);
//}

void cPictList::Destroy(void) {
	auto it = begin(), iend = end();
	for (; it != iend; it++) delete *it;
	clear();
}




//cContainer::cContainer() {
//	DocumentName = "";
//	MarkList = new cMarkList;
//	Lines = new cLines;
//	DocStream = new TMemoryStream;
//	NoteList = new cNoteList;
//}
//
//cContainer::~cContainer() {
//	delete DocStream;
//	delete NoteList;
//	delete MarkList;
//	delete Lines;
//}
//
//void cContainer::Document(AnsiString doc) {
//	DocumentName = doc;
//	doc = ExtractFileName(doc);
//	int l = doc.Length() - ExtractFileExt(doc).Length();
//	doc = doc.SubString(1, l);
//	lblName->Caption = doc;
//}

