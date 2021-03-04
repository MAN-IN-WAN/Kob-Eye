import React from 'react';
import ReactDOM from 'react-dom';
import CompoClinique from './component_Clinique.js';

export default class Page_clinique extends React.Component{

    constructor() {
        super();
        this.state = {};
    }


    render() {
        return (
            <div className={"container"}>
                <div id="PageClinique" className={"row"}>
                    <CompoClinique/>
                </div>
            </div>
        );
    }
}