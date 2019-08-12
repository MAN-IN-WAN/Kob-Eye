//---------------------------------------------------------------------------

#ifndef RTFH
#define RTFH

#include <stdio.h>
#include <iostream>
#include <sstream>
#include "Target.h"

//---------------------------------------------------------------------------
#define RTF_TEXT_SIZE  32768
#define RTF_CMDW_LEN   32
#define RTF_PICT_BLK   4096
#define RTF_TEXT_BLK   4096

#define RTF_TXT 0
#define RTF_ESC 1
#define RTF_CMD 2
#define RTF_NUM 3
#define RTF_SEP 4
#define RTF_HEX 5
#define RTF_ASC 6


#define MARK_NONE  0
#define MARK_LIGNE 1
#define MARK_PAGE  2
#define MARK_FOLIO 3
#define MARK_VIGN  4
#define MARK_REFER 5

class cMark {
public:
	int Pos;
	short Type;
	int Num;
	int Verso;
	cPict *Pict;
	int RefPos;

	cMark(int pos, int refpos, int type, int num, int verso);
	~cMark();
};

typedef std::list<cMark*>::iterator MarkIterator;

class cMarkList : public std::list<cMark*> {
public:
	~cMarkList(void) { Destroy(); }
	void Destroy(void);
	inline cMark* Mark(MarkIterator it) { return *it; }
};

class cNote {
public:
	char *Text;
	int Page;

	cNote(int page, char *text);
	~cNote();
};

typedef std::list<cNote*>::iterator NoteIterator;

class cNoteList : public std::list<cNote*> {
public:

	~cNoteList(void) { Destroy(); }
	void Destroy(void);
	inline cNote* Note(NoteIterator it) { return *it; }
};

class cPict {
//	HENHMETAFILE hemf;
	void* hemf;

public:
	cPict(int type, int picw, int pich, int len, char *data);
	~cPict();
//	void Size(RECT *rect);
//	void Draw(HDC hdc, RECT *rect);
};

typedef std::list<cPict*>::iterator PictIterator;

class cPictList : public std::list<cPict*> {
public:
	~cPictList(void) { Destroy(); }
	void Destroy(void);
	inline cPict* Pict(PictIterator it) { return *it; }
};

class cRtCtx {
public:
	bool comment;
	bool note;
	bool pict;

	cRtCtx();
	cRtCtx(cRtCtx *ctx);
	void cp(cRtCtx *ctx);
};

typedef std::list<cRtCtx*>::iterator RtCtxIterator;

class cRtCtxList : public std::list<cRtCtx*> {
public:
	~cRtCtxList(void) { Destroy(); }
	void Destroy(void);
	inline cRtCtx* RtCtx(RtCtxIterator it) { return *it; }
};


class cRtf {
private:
	FILE *File;
	std::stringstream *Strm;

	bool Rtf;
	cRtCtx *Ctx;
	bool First;

	cMarkList *Mark;
	cLines *Lines;
	cNoteList *Notes;
	//  cPictList *Picts;

	bool bComment;
	bool bNote;

	bool bPict;
	int PicType;
	int PicW;
	int PicH;

	cData *Pict;
	cData *Text;
	cData *Note;

	int Pos;
	int Page;
	int Folio;
	int Line;
	int Vignette;
	cMark *RefMark;
	int RefPos;


	char cmd[RTF_CMDW_LEN];
	char num[RTF_CMDW_LEN];
	int incmd;
	int innum;


	char *text;
	int intext;

	void Command();
	void NewLine(bool hard);
	void Add(char c);
	void SingleChar(char c);
	void RefMarkEnd();

public:
	cRtf(FILE *f, std::stringstream *strm, cMarkList *mrk, cLines *lin, cNoteList *note); 
	int Read();
};


//class cContainer {
//public:
//	AnsiString DocumentName;
//	TRichEdit *reText;
//	TPanel *pnlDoc;
//	TPanel *pnlName;
//	TLabel *lblName;
//	TSpeedButton *btnClose;
//	TSplitter *splDoc;
//	bool Visible;
//	TMemoryStream *DocStream;
//	cMarkList *MarkList;
//	cLines *Lines;
//	cNoteList *NoteList;
//
//	cContainer();
//	~cContainer();
//	void Document(AnsiString doc);
//};


#endif
