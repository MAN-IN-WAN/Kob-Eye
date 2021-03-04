import React, { useState,useEffect} from 'react';
import ReactDOM from 'react-dom';
import {  Link, useLocation } from "react-router-dom";

export default function CompoNscFicheHeader(props){
    let location = useLocation();
    let type = location.pathname.split("/")[1];
    const [Cats,setCats] = useState([0]);
    useEffect( () => {
        if (props.cats && props.cats.length)
            setCats(props.cats);
    },[props.cats]);
    let filtres = [];
    for (let filtre in filtreData){
        let classe = "inactive";
        if (Cats.indexOf(parseInt(filtreData[filtre])) != -1)
            classe = "active";
        filtres.push(
            <li className={classe} >
                {filtre}
            </li>
        );
    }
    let texte =
        <div id={"filtresNsc"} className={"row"}>
            <ul className={"col-md-11"}>
                {filtres}
            </ul>
            <div className="col-md-1 btnRetour">
                <Link to={"/"+type}><span></span></Link>
            </div>
        </div>
    ;




    return (
        <>
            {texte}
        </>
    );
}
