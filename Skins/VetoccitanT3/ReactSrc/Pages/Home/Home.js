import React from 'react';
import ReactDOM from 'react-dom';
import CompoSlider from './component_Slider.js';
import CompoBlocAccueil from './component_BlocAccueil.js';
import CompoHoraires from './component_Horaires.js';
import CompoEquipe from './component_Equipe.js';


export default class Page_home extends React.Component{

    constructor() {
        super();
        this.state = {};
    }

    render() {
        return (
            <>
                <div id="Slider">
                   <CompoSlider />
                </div>
                <div id="blocAccueil">
                    <div className="container">
                        <CompoBlocAccueil />
                    </div>
                </div>
                <div id="Horaires" className="blocHoraireHome couleurPrincipale">
                    <CompoHoraires />
                </div>
                <div id="Equipe">
                    <div className="container">
                        <CompoEquipe type={"homeEquipe"}/>
                    </div>
                </div>
            </>

        );
    }
}