import React from 'react';
import ReactDOM from 'react-dom';
import CompoEquipe from '../Home/component_Equipe.js';

export default class Page_equipe extends React.Component{

    constructor() {
        super();
        this.state = {};
    }


    render() {
        return (
            <div className="container">
                <div id="PageEquipe">
                    <CompoEquipe type={"fullEquipe"}/>
                </div>
            </div>
            
        );
    }
}