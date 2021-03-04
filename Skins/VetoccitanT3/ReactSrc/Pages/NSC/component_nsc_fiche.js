import React from 'react';
import ReactDOM from 'react-dom';
import { useSelector } from 'react-redux';
import { useLocation,useParams } from "react-router-dom";
import { ficheHtml } from "../../Redux/cliniqueSlice.js"
import CompoNscFicheHeader from "./component_nsc_ficheHeader.js"

export default function CompoNscFiche(){
    let location = useLocation();
    let type = location.pathname.split("/")[1];
    let { url } = useParams();
    let selector = useSelector( (state) => {

        let data = state.nsc.fullData[type];

        for ( let item in data){
            if (data[item].Url === url) {
                return data[item];
            }
        }
        return {};
    });
    let cats = [];
    for (let c in selector.cats){
        cats.push(parseInt(selector.cats[c]));
    }
    let texte = ficheHtml(selector);

    return (
        <div className={"container"}>
            <CompoNscFicheHeader cats={cats}/>
            <div className={"row"}>
                {texte}
            </div>
        </div>
    );
}
