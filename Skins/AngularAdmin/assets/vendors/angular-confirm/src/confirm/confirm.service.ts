import {Injectable, TemplateRef} from '@angular/core';
import {NgbModal, NgbModalOptions, NgbModalRef} from '@ng-bootstrap/ng-bootstrap';

@Injectable()
export class ConfirmService {

    constructor(private modalService: NgbModal) {}

    confirm(template: string | TemplateRef<any>, options ?: NgbModalOptions): NgbModalRef {
        return this.modalService.open(template, options);
    }
}
